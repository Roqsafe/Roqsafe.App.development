<?php
// ── lib/auth.php ──────────────────────────────────────────────
// Admin session management, CSRF, and settings helper.
// Compatible with the new lib/ path and router.php front controller.



function svi_start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('svi_admin');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function svi_require_auth(): void {
    svi_start_session();
    if (empty($_SESSION['svi_admin_id'])) {
        header('Location: /admin/login?next=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    // Session timeout
    $db      = svi_db();
    $timeout = (int) svi_setting($db, 'session_timeout_minutes', 60);
    if (!empty($_SESSION['svi_last_active'])) {
        if (time() - $_SESSION['svi_last_active'] > $timeout * 60) {
            session_destroy();
            header('Location: /admin/login?expired=1');
            exit;
        }
    }
    $_SESSION['svi_last_active'] = time();
}

// ── Settings ──────────────────────────────────────────────────
function svi_setting(PDO $db, string $key, mixed $default = ''): mixed {
    static $cache = [];
    if (!isset($cache[$key])) {
        $st = $db->prepare("SELECT `value` FROM svi_settings WHERE setting_key = ?");
        $st->execute([$key]);
        $row = $st->fetch();
        $cache[$key] = $row ? $row['value'] : $default;
    }
    return $cache[$key];
}

// ── CSRF ───────────────────────────────────────────────────────
function svi_csrf_token(): string {
    svi_start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function svi_verify_csrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function svi_admin_name(): string {
    return $_SESSION['svi_admin_username'] ?? 'Admin';
}

// ── Content helper ─────────────────────────────────────────────
function svi_content(PDO $db, string $page = 'main'): array {
    $st = $db->prepare("SELECT `key`, `value` FROM svi_content WHERE page = ?");
    $st->execute([$page]);
    $c = [];
    foreach ($st->fetchAll() as $row) {
        $c[$row['key']] = $row['value'];
    }
    return $c;
}

function txt(array $c, string $key, string $fallback = ''): string {
    return $c[$key] ?? $fallback;
}
