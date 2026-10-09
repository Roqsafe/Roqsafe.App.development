<?php
// ── deblock.php ───────────────────────────────────────────────
// AJAX endpoint: submit a deblock request (soft-block only)
// POST JSON { "ip": "1.2.3.4" }
// Permanent blocks are rejected — cannot be deblocked.


   // for svi_setting()

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$raw  = file_get_contents('php://input');
$body = @json_decode($raw, true);
$ip   = trim($body['ip'] ?? $_POST['ip'] ?? '');

if ($ip === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No IP address provided.']);
    exit;
}

if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid IP address format.']);
    exit;
}

$db = svi_db();

// ── Check if deblock is globally allowed ──────────────────────
if (svi_setting($db, 'allow_deblock', '1') !== '1') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Deblock requests are currently not accepted. Please contact support directly.',
    ]);
    exit;
}

// ── Lookup the IP ─────────────────────────────────────────────
$st = $db->prepare("SELECT * FROM svi_blocked_ips WHERE ip=?");
$st->execute([$ip]);
$record = $st->fetch();

if (!$record) {
    echo json_encode(['status' => 'not_found', 'message' => 'This IP address was not found in our records.']);
    exit;
}

// ── Permanent block: cannot be deblocked — ever ───────────────
if ($record['block_type'] === 'permanent') {
    echo json_encode([
        'status'  => 'permanent',
        'message' => 'This IP address has been permanently blocked. This decision is final and cannot be reversed. No deblock request can be submitted.',
    ]);
    exit;
}

if ($record['deblocked']) {
    echo json_encode(['status' => 'already', 'message' => 'This IP address is already deblocked. No further action is required.']);
    exit;
}

// ── Cooldown check ────────────────────────────────────────────
$cooldownHours = (int) svi_setting($db, 'deblock_cooldown_hours', 24);
if ($record['deblock_req'] && $cooldownHours > 0) {
    $updatedAt = new DateTime($record['updated_at']);
    $diff      = (new DateTime())->diff($updatedAt);
    $hoursSince= $diff->h + ($diff->days * 24);

    if ($hoursSince < $cooldownHours) {
        $remaining = $cooldownHours - $hoursSince;
        echo json_encode([
            'status'  => 'pending',
            'message' => "A deblock request for this IP is already pending review. Please wait approximately {$remaining} hour(s) before resubmitting.",
        ]);
        exit;
    }
}

// ── Submit deblock request ────────────────────────────────────
$db->prepare("UPDATE svi_blocked_ips SET deblock_req=1 WHERE ip=?")->execute([$ip]);

echo json_encode([
    'status'  => 'ok',
    'message' => 'Your deblock request has been submitted successfully. Our team will review your case and contact you if additional information is required.',
]);
