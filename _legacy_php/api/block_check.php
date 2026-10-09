<?php
// ── block_check.php ───────────────────────────────────────────
// AJAX endpoint: checks IP block status + enforces rate limiting
// POST JSON { "ip": "1.2.3.4" }
// Response JSON { "status": "...", "message": "..." }


   // for svi_setting()

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$raw  = file_get_contents('php://input');
$body = @json_decode($raw, true);
$ip   = trim($body['ip'] ?? $_POST['ip'] ?? '');
$checkerIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

if ($ip === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No IP address provided.']);
    exit;
}

if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'The value entered is not a valid IP address.']);
    exit;
}

$db = svi_db();

// ── Rate limiting ─────────────────────────────────────────────
$maxAttempts   = (int) svi_setting($db, 'max_check_attempts', 10);
$autoBlock     = svi_setting($db, 'auto_block_on_exceed', '1') === '1';

$todayChecks = (int) $db->prepare("
  SELECT COUNT(*) FROM svi_check_log
  WHERE ip=? AND DATE(created_at)=CURDATE()
")->execute([$checkerIp]) ?
  $db->query("SELECT COUNT(*) FROM svi_check_log WHERE ip='{$checkerIp}' AND DATE(created_at)=CURDATE()")->fetchColumn() : 0;

// Re-query properly
if ($checkerIp === '::1' || $checkerIp === '127.0.0.1' || $checkerIp === 'localhost') {
    $todayChecks = 0; // Bypass rate limit for local developers
} else {
    $st = $db->prepare("SELECT COUNT(*) FROM svi_check_log WHERE ip=? AND DATE(created_at)=CURDATE()");
    $st->execute([$checkerIp]);
    $todayChecks = (int)$st->fetchColumn();
}

if ($todayChecks >= $maxAttempts) {
    if ($autoBlock) {
        $db->prepare("INSERT INTO svi_blocked_ips (ip, block_type, reason) VALUES (?,?,?) ON DUPLICATE KEY UPDATE id=id")
           ->execute([$checkerIp, 'soft', 'Auto-blocked: exceeded daily check limit']);
    }
    echo json_encode(['status' => 'error', 'message' => 'Too many requests. You have exceeded the daily check limit.']);
    exit;
}

// ── Log this check ────────────────────────────────────────────
$db->prepare("INSERT INTO svi_check_log (ip, target_ip) VALUES (?,?)")->execute([$checkerIp, $ip]);

// ── Content message helper ────────────────────────────────────
function get_msg(PDO $db, string $key): string {
    $st = $db->prepare("SELECT `value` FROM svi_content WHERE page='main' AND `key`=?");
    $st->execute([$key]);
    $r = $st->fetch();
    return $r ? $r['value'] : '';
}

// ── Lookup the IP ─────────────────────────────────────────────
if ($ip === '::1' || $ip === '127.0.0.1' || $ip === 'localhost') {
    $st = $db->prepare("SELECT * FROM svi_blocked_ips WHERE ip IN ('::1', '127.0.0.1', 'localhost') ORDER BY id DESC LIMIT 1");
    $st->execute();
} else {
    $st = $db->prepare("SELECT * FROM svi_blocked_ips WHERE ip=?");
    $st->execute([$ip]);
}
$record = $st->fetch();

// Legacy Block Fallback Database
if (!$record) {
    if ($ip === '::1' || $ip === '127.0.0.1' || $ip === 'localhost') {
        $st2 = $db->prepare("SELECT * FROM blocked_ips WHERE ip_address IN ('::1', '127.0.0.1', 'localhost') AND active=1 ORDER BY id DESC LIMIT 1");
        $st2->execute();
    } else {
        $st2 = $db->prepare("SELECT * FROM blocked_ips WHERE ip_address=? AND active=1 ORDER BY id DESC LIMIT 1");
        $st2->execute([$ip]);
    }
    $r2 = $st2->fetch();
    
    if ($r2) {
        $record = [
            'id' => $r2['id'],
            'ip' => $r2['ip_address'],
            'block_type' => 'permanent',
            'reason' => $r2['reason'],
            'deblock_req' => 0,
            'deblocked' => 0,
            'blocked_website' => null
        ];
    }
}

if (!$record) {
    echo json_encode(['status' => 'not_found', 'message' => get_msg($db, 'result_not_found')]);
    exit;
}

if ($record['deblocked']) {
    echo json_encode(['status' => 'deblocked', 'message' => get_msg($db, 'result_deblocked')]);
    exit;
}

if ($record['block_type'] === 'permanent') {
    $url = !empty($record['blocked_website']) ? $record['blocked_website'] : 'our platform';
    $msg = "Suspicious activities detected on " . htmlspecialchars($url) . " involving your IP address. Please verify.<br><br>" . get_msg($db, 'result_perm_blocked');
    echo json_encode(['status' => 'permanent', 'message' => $msg]);
    exit;
}

// Soft block
$url = !empty($record['blocked_website']) ? $record['blocked_website'] : 'our platform';
$msg = "Suspicious activities detected on " . htmlspecialchars($url) . " involving your IP address. Please verify.<br><br>" . get_msg($db, 'result_soft_blocked');
echo json_encode([
    'status'            => 'soft',
    'message'           => $msg,
    'deblock_requested' => (bool) $record['deblock_req'],
]);
