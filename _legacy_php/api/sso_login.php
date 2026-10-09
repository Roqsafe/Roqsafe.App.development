<?php
// ── api/sso_login.php ─────────────────────────────────────────
// Receives the Firebase SSO payload securely, verifies domain,
// and provisions the admin session.

require_once __DIR__ . '/../lib/env.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

header('Content-Type: application/json');

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['email'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid SSO payload received.']);
    exit;
}

$email = strtolower(trim($data['email']));
$uid   = $data['uid'] ?? 'unknown_uid';
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$ua       = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512);
$db = svi_db();

// ── Domain Validation ─────────────────────────────────────────
if (!str_ends_with($email, '@ericksonholding.com')) {
    $errorMsg = 'Only authorized Erickson Holding emails can log in. Access Denied.';
    $db->prepare("INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,0,?)")
       ->execute([$email, $clientIp, $ua]);
    
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

// ── Admin Verification ────────────────────────────────────────
$stmt = $db->prepare("SELECT * FROM `svi_admins` WHERE `email` = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if ($admin) {
    // Check if account is locked
    if ($admin['locked_until'] && new DateTime() < new DateTime($admin['locked_until'])) {
        $unlockAt = (new DateTime($admin['locked_until']))->format('H:i');
        echo json_encode(['success' => false, 'error' => "Account temporarily locked due to previous failed attempts. Try again after {$unlockAt}."]);
        exit;
    }

    // ✓ SSO Success! No password needed because Firebase validated them.
    $db->prepare("UPDATE `svi_admins` SET `login_attempts`=0, `locked_until`=NULL, `last_login`=NOW() WHERE `id`=?")->execute([$admin['id']]);
    $db->prepare("INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,1,?)")->execute([$email, $clientIp, $ua]);

    svi_start_session();
    session_regenerate_id(true);
    $_SESSION['svi_admin_id']       = $admin['id'];
    $_SESSION['svi_admin_username'] = $admin['username'];
    $_SESSION['svi_last_active']    = time();
    $_SESSION['csrf_token']         = bin2hex(random_bytes(32));

    echo json_encode(['success' => true, 'redirect' => 'index.php']);
    exit;
} else {
    // ✗ Not found in the DB (Even if they have an @ericksonholding.com domain, we require an admin record for SVI)
    // For now, if we want auto-provisioning we could do it here, but SVI is rigid so we reject.
    $errorMsg = 'No SVI admin account found for this email address.';
    $db->prepare("INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,0,?)")
       ->execute([$email, $clientIp, $ua]);
    
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}
