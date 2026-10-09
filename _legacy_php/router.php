<?php
// ── router.php ────────────────────────────────────────────────
// Front Controller mirroring Next.js App Router structure.
// All requests flow through this file via .htaccess rules.

require_once __DIR__ . '/lib/env.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';

// Parse the request URI — robust for cPanel/shared hosting
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip any subfolder prefix if the app lives inside a subdirectory (e.g. /svi/)
$scriptDir = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($scriptDir !== '' && str_starts_with(ltrim($requestUri, '/'), $scriptDir . '/')) {
    $requestUri = substr(ltrim($requestUri, '/'), strlen($scriptDir));
}

$requestUri = trim($requestUri, '/');

// Strip .php extension if someone typed URL directly
if (str_ends_with($requestUri, '.php')) {
    $requestUri = substr($requestUri, 0, -4);
}

// Root → index
if ($requestUri === '') {
    $requestUri = 'index';
}

$method = $_SERVER['REQUEST_METHOD'];

// ── Rate Limiting & Unknown Route Autoboock ───────────────────
function handle_404_autoblock(PDO $db, string $ip) {
    if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) return;

    $st = $db->prepare("SELECT * FROM svi_blocked_ips WHERE ip=?");
    $st->execute([$ip]);
    $record = $st->fetch();

    if ($record && $record['block_type'] === 'permanent') return; // already perm blocked

    // Track 404 hit
    $db->prepare("INSERT INTO svi_check_log (ip, target_ip, created_at) VALUES (?, '404_NOT_FOUND', NOW())")->execute([$ip]);

    // Count recent 404s (e.g., today)
    $stCount = $db->prepare("SELECT COUNT(*) FROM svi_check_log WHERE ip=? AND target_ip='404_NOT_FOUND' AND DATE(created_at)=CURDATE()");
    $stCount->execute([$ip]);
    $misses = (int)$stCount->fetchColumn();

    if ($misses >= 3) {
        $db->prepare("INSERT INTO svi_blocked_ips (ip, block_type, reason) VALUES (?, 'soft', 'Auto-blocked: multiple unknown route access attempts') ON DUPLICATE KEY UPDATE block_type='soft', reason='Auto-blocked: multiple unknown route access attempts', deblocked=0, deblock_req=0")
           ->execute([$ip]);
    }
}

function check_global_block(PDO $db, string $ip) {
    if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) return null;
    
    // Unify local development IP schemas so block rules always trigger correctly on testing
    $ips = ($ip === '::1' || $ip === '127.0.0.1' || $ip === 'localhost') ? ['::1', '127.0.0.1', 'localhost'] : [$ip];
    $inClause = implode(',', array_fill(0, count($ips), '?'));
    
    // Primary Table Check
    $st = $db->prepare("SELECT * FROM svi_blocked_ips WHERE ip IN ($inClause) AND deblocked=0");
    $st->execute($ips);
    $record = $st->fetch();
    if ($record) return $record;
    
    // External / Legacy Table Check 
    $st2 = $db->prepare("SELECT * FROM blocked_ips WHERE ip_address IN ($inClause) AND active=1");
    $st2->execute($ips);
    $r2 = $st2->fetch();
    if ($r2) {
        return [
            'id' => $r2['id'],
            'ip' => $r2['ip_address'],
            'block_type' => 'permanent',
            'reason' => $r2['reason'],
            'deblock_req' => 0,
            'deblocked' => 0,
            'blocked_website' => null
        ];
    }

    return null;
}

$db = svi_db();
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
$blockRecord = check_global_block($db, $clientIp);

// If blocked and not accessing a safe route (like deblock api or public blocked page if we had one)
// But wait, the public page IS the deblock page!
// So we shouldn't prevent access to the main page or deblock api.
// Admin routes, however, should be blocked.

// ── Route matching ────────────────────────────────────────────

// 1. Check if it's an API route -> api/$requestUri.php
if (str_starts_with($requestUri, 'api/')) {
    $apiFile = __DIR__ . '/' . $requestUri . '.php';
    if (file_exists($apiFile)) {
        require $apiFile;
        exit;
    }
}

// 1.5 Special dynamic routes
if (preg_match('#^Verifi/data/user/([^/]+)$#', $requestUri, $matches)) {
    $_GET['token'] = $matches[1];
    $toolFile = __DIR__ . '/app/tool/page.php';
    if (file_exists($toolFile)) {
        require $toolFile;
        exit;
    }
}

// 2. Check if it's an App route -> app/$requestUri/page.php or app/$requestUri.php
$appFile = __DIR__ . '/app/' . $requestUri . '/page.php';
$appFileAlt = __DIR__ . '/app/' . $requestUri . '.php';

if ($requestUri === 'index') {
    $appFile = __DIR__ . '/app/page.php';
}

if (file_exists($appFile)) {
    require $appFile;
    exit;
} elseif (file_exists($appFileAlt)) {
    require $appFileAlt;
    exit;
}

// 3. Admin routes moved to secure-svi-portal via DB token mechanism
$adminToken = 'secure-svi-portal'; // fallback
try {
    $stmt = $db->prepare("SELECT value FROM svi_settings WHERE setting_key = 'admin_access_token'");
    $stmt->execute();
    $tokenRow = $stmt->fetch();
    if ($tokenRow && !empty($tokenRow['value'])) {
        $adminToken = $tokenRow['value'];
    }
} catch (Exception $e) { }

$adminPrefix = 'admin/' . $adminToken;

if (str_starts_with($requestUri, 'admin/')) {
    // Check if it's the valid protected token route
    if (str_starts_with($requestUri, $adminPrefix . '/') || $requestUri === $adminPrefix) {
        if ($requestUri === $adminPrefix) {
            header("Location: /$adminPrefix/");
            exit;
        }

        $subRoute = substr($requestUri, strlen($adminPrefix));
        $subRoute = ltrim($subRoute, '/');
        if ($subRoute === '') $subRoute = 'index';
        
        $adminFile = __DIR__ . '/app/admin/' . $subRoute . '.php';
        if (file_exists($adminFile)) {
            require $adminFile;
            exit;
        }
    } else {
        // Invalid token or direct admin access attempt
        $_GET['error_type'] = '404';
        require __DIR__ . '/app/404/page.php';
        exit;
    }
}

// Preserve backwards compat for secure-svi-portal just in case something breaks locally
if (str_starts_with($requestUri, 'secure-svi-portal/')) {
    $mappedUri = str_replace('secure-svi-portal/', 'admin/', $requestUri);
    $adminFile = __DIR__ . '/app/' . $mappedUri . '.php';
    if (file_exists($adminFile)) {
        require $adminFile;
        exit;
    }
}

// ── 404 Not Found ────────────────────────────────────────────
http_response_code(404);
handle_404_autoblock($db, $clientIp);

// If blocked due to this 404, we might want to redirect to /
$newBlockRecord = check_global_block($db, $clientIp);
if ($newBlockRecord && !$blockRecord) {
     header('Location: /');
     exit;
}

// Load 404 page content if exists
$notFoundFile = __DIR__ . '/app/404/page.php';
if (file_exists($notFoundFile)) {
    require $notFoundFile;
} else {
    echo "<h1>404 Not Found</h1>";
}
exit;
