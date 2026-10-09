<?php
// ── admin/settings.php — Site Settings ───────────────────────

svi_require_auth();

$db      = svi_db();
$message = '';
$msgType = 'ok';
$csrf    = svi_csrf_token();

// ── Handle save ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && svi_verify_csrf($_POST['csrf'] ?? '')) {
    $stmt = $db->prepare("UPDATE svi_settings SET value=? WHERE setting_key=?");

    // Load all setting keys
    $keys = $db->query("SELECT setting_key, type FROM svi_settings")->fetchAll();

    foreach ($keys as $row) {
        $key  = $row['setting_key'];
        $type = $row['type'];

        if ($type === 'boolean') {
            $val = isset($_POST[$key]) ? '1' : '0';
        } else {
            $val = trim($_POST[$key] ?? '');
        }
        $stmt->execute([$val, $key]);
    }

    $message = 'Settings saved successfully.';
}

// ── Load all settings ──────────────────────────────────────────
$settings = $db->query("SELECT * FROM svi_settings ORDER BY id")->fetchAll();

// Group by category
$groups = [
    'Public Page'     => ['allow_deblock','show_ip_tool','site_notice','deblock_cooldown_hours','max_check_attempts','auto_block_on_exceed','require_reason_for_block'],
    'Maintenance'     => ['maintenance_mode','maintenance_message'],
    'Admin Security'  => ['login_max_attempts','login_lockout_minutes','session_timeout_minutes'],
    'Notifications'   => ['admin_email'],
];

$settingMap = [];
foreach ($settings as $s) {
    $settingMap[$s['setting_key']] = $s;
}

$pageTitle  = 'Site Settings';
$activePage = 'settings';
include __DIR__ . '/../../components/_admin_head.php';
?>
</head>
<body>
<?php include __DIR__ . '/../../components/_admin_nav.php'; ?>

<div class="page-heading">
  <h1>Site Settings</h1>
  <p>Configure how SVI behaves on the public page and in the admin panel.</p>
</div>

<?php if ($message): ?>
  <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message, ENT_QUOTES) ?></div>
<?php endif; ?>

<form method="POST">
  <input type="hidden" name="csrf" value="<?= $csrf ?>">

  <?php foreach ($groups as $groupName => $keys): ?>
  <div class="card">
    <div class="card-title"><?= htmlspecialchars($groupName, ENT_QUOTES) ?></div>

    <?php foreach ($keys as $key):
      if (!isset($settingMap[$key])) continue;
      $s = $settingMap[$key];
    ?>
    <div class="form-group">
      <label for="<?= htmlspecialchars($key, ENT_QUOTES) ?>">
        <?= htmlspecialchars($s['label'], ENT_QUOTES) ?>
      </label>
      <?php if ($s['description']): ?>
        <div class="form-desc"><?= htmlspecialchars($s['description'], ENT_QUOTES) ?></div>
      <?php endif; ?>

      <?php if ($s['type'] === 'boolean'): ?>
        <div class="toggle-wrap">
          <label class="toggle">
            <input type="checkbox" name="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
                   id="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
                   <?= $s['value'] === '1' ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>
          <span style="font-size:13px;color:var(--muted);font-weight:300">
            <?= $s['value'] === '1' ? 'Enabled' : 'Disabled' ?>
          </span>
        </div>

      <?php elseif ($s['type'] === 'textarea'): ?>
        <textarea class="form-textarea"
                  name="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
                  id="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
                  rows="3"><?= htmlspecialchars($s['value'], ENT_QUOTES) ?></textarea>

      <?php elseif ($s['type'] === 'integer'): ?>
        <input class="form-input" type="number"
               name="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
               id="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
               value="<?= htmlspecialchars($s['value'], ENT_QUOTES) ?>"
               min="0" style="max-width:160px">

      <?php elseif ($s['type'] === 'email'): ?>
        <input class="form-input" type="email"
               name="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
               id="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
               value="<?= htmlspecialchars($s['value'], ENT_QUOTES) ?>"
               style="max-width:320px">

      <?php else: ?>
        <input class="form-input" type="text"
               name="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
               id="<?= htmlspecialchars($key, ENT_QUOTES) ?>"
               value="<?= htmlspecialchars($s['value'], ENT_QUOTES) ?>">
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

  </div>
  <?php endforeach; ?>

  <div style="display:flex;gap:12px;align-items:center">
    <button class="btn btn-primary" type="submit">💾 Save All Settings</button>
    <a class="btn btn-ghost" href="index.php">Cancel</a>
  </div>

</form>

  </main>
</div>
</body>
</html>
