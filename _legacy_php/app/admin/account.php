<?php
// ── admin/account.php — My Account / Change Password ──────────

svi_require_auth();

$db      = svi_db();
$message = '';
$msgType = 'ok';
$csrf    = svi_csrf_token();
$adminId = $_SESSION['svi_admin_id'];

$admin = $db->prepare("SELECT * FROM svi_admins WHERE id=?")->execute([$adminId]) ?
         $db->prepare("SELECT * FROM svi_admins WHERE id=?") : null;
$stAdmin = $db->prepare("SELECT * FROM svi_admins WHERE id=?");
$stAdmin->execute([$adminId]);
$admin = $stAdmin->fetch();

// ── Handle updates ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && svi_verify_csrf($_POST['csrf'] ?? '')) {
    $action = $_POST['action'] ?? '';

    if ($action === 'change_password') {
        $current  = $_POST['current_password'] ?? '';
        $newPass  = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $admin['password_hash'])) {
            $message = 'Current password is incorrect.'; $msgType = 'danger';
        } elseif (strlen($newPass) < 8) {
            $message = 'New password must be at least 8 characters.'; $msgType = 'danger';
        } elseif ($newPass !== $confirm) {
            $message = 'New passwords do not match.'; $msgType = 'danger';
        } else {
            $hash = password_hash($newPass, PASSWORD_BCRYPT);
            $db->prepare("UPDATE svi_admins SET password_hash=? WHERE id=?")->execute([$hash, $adminId]);
            $message = 'Password changed successfully.';
        }
    }

    if ($action === 'change_email') {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Invalid email address.'; $msgType = 'danger';
        } else {
            $db->prepare("UPDATE svi_admins SET email=? WHERE id=?")->execute([$email, $adminId]);
            $message = 'Email updated.';
        }
    }

    // Refresh admin data
    $stAdmin->execute([$adminId]);
    $admin = $stAdmin->fetch();
}

// ── Recent login history for this admin ───────────────────────
$loginHistory = $db->prepare("
  SELECT ip, success, created_at, user_agent
  FROM svi_login_log
  WHERE username = ?
  ORDER BY created_at DESC
  LIMIT 10
");
$loginHistory->execute([$admin['username']]);
$loginHistory = $loginHistory->fetchAll();

$pageTitle  = 'My Account';
$activePage = 'account';
include __DIR__ . '/../../components/_admin_head.php';
?>
</head>
<body>
<?php include __DIR__ . '/../../components/_admin_nav.php'; ?>

<div class="page-heading">
  <h1>My Account</h1>
  <p>Manage your admin credentials and review your login history.</p>
</div>

<?php if ($message): ?>
  <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message, ENT_QUOTES) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;flex-wrap:wrap">

  <!-- Change password -->
  <div class="card">
    <div class="card-title">🔑 Change Password</div>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="change_password">

      <div class="form-group">
        <label>Current Password</label>
        <input class="form-input" type="password" name="current_password" required autocomplete="current-password">
      </div>
      <div class="form-group">
        <label>New Password</label>
        <input class="form-input" type="password" name="new_password" required autocomplete="new-password" minlength="8">
        <div class="form-desc">Minimum 8 characters.</div>
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <input class="form-input" type="password" name="confirm_password" required autocomplete="new-password">
      </div>
      <button class="btn btn-primary" type="submit">Update Password</button>
    </form>
  </div>

  <!-- Account info + email -->
  <div>
    <div class="card" style="margin-bottom:20px">
      <div class="card-title">👤 Account Info</div>
      <table class="svi-table">
        <tr>
          <td style="font-weight:600;color:var(--svi-navy);width:40%">Username</td>
          <td style="font-family:monospace"><?= htmlspecialchars($admin['username'], ENT_QUOTES) ?></td>
        </tr>
        <tr>
          <td style="font-weight:600;color:var(--svi-navy)">Email</td>
          <td><?= htmlspecialchars($admin['email'] ?? '—', ENT_QUOTES) ?></td>
        </tr>
        <tr>
          <td style="font-weight:600;color:var(--svi-navy)">Last Login</td>
          <td style="font-size:13px;color:var(--muted)">
            <?= $admin['last_login'] ? date('d M Y H:i', strtotime($admin['last_login'])) : 'N/A' ?>
          </td>
        </tr>
        <tr>
          <td style="font-weight:600;color:var(--svi-navy)">Account Since</td>
          <td style="font-size:13px;color:var(--muted)"><?= date('d M Y', strtotime($admin['created_at'])) ?></td>
        </tr>
      </table>
    </div>

    <div class="card">
      <div class="card-title">📧 Update Email</div>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="change_email">
        <div class="form-group">
          <label>Email Address</label>
          <input class="form-input" type="email" name="email"
                 value="<?= htmlspecialchars($admin['email'] ?? '', ENT_QUOTES) ?>" required>
        </div>
        <button class="btn btn-primary" type="submit">Save Email</button>
      </form>
    </div>
  </div>

</div>

<!-- Login history -->
<div class="card" style="margin-top:4px">
  <div class="card-title">🔐 My Recent Login Events</div>
  <div class="tbl-wrap">
    <table class="svi-table">
      <thead>
        <tr><th>Time</th><th>IP</th><th>Result</th><th>Browser</th></tr>
      </thead>
      <tbody>
        <?php foreach ($loginHistory as $ev): ?>
        <tr>
          <td style="font-size:12px;color:var(--muted);white-space:nowrap">
            <?= date('d M Y H:i', strtotime($ev['created_at'])) ?>
          </td>
          <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($ev['ip'], ENT_QUOTES) ?></td>
          <td>
            <?= $ev['success']
              ? '<span class="badge badge-ok">✓ Success</span>'
              : '<span class="badge badge-perm">✗ Failed</span>' ?>
          </td>
          <td style="font-size:12px;color:var(--muted);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?= htmlspecialchars(substr($ev['user_agent'] ?? '', 0, 80), ENT_QUOTES) ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($loginHistory)): ?>
          <tr><td colspan="4" style="text-align:center;color:var(--muted)">No login events recorded yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

  </main>
</div>
</body>
</html>
