<?php
// ── admin/login.php ───────────────────────────────────────────


// Already logged in → redirect to dashboard
if (!empty($_SESSION['svi_admin_id'])) {
    header('Location: index.php');
    exit;
}

$db    = svi_db();
$error = '';
$info  = '';

if (isset($_GET['expired'])) $info = 'Your session expired. Please sign in again.';

// ── Handle POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    // map email to username for logs
    $username = $email;
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua       = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512);

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } elseif (!str_ends_with($email, '@ericksonholding.com')) {
        $error = 'Only authorized Erickson Holding emails can log in.';
    } else {
        $maxAttempts   = (int) svi_setting($db, 'login_max_attempts', 5);
        $lockoutMins   = (int) svi_setting($db, 'login_lockout_minutes', 15);

        $stmt = $db->prepare("SELECT * FROM `svi_admins` WHERE `email` = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin) {
            // Check if locked
            if ($admin['locked_until'] && new DateTime() < new DateTime($admin['locked_until'])) {
                $unlockAt = (new DateTime($admin['locked_until']))->format('H:i');
                $error = "Account temporarily locked due to too many failed attempts. Try again after {$unlockAt}.";
            } elseif (password_verify($password, $admin['password_hash'])) {
                // ✓ Success — reset attempts
                $db->prepare("UPDATE `svi_admins` SET `login_attempts`=0, `locked_until`=NULL, `last_login`=NOW() WHERE `id`=?")->execute([$admin['id']]);
                $db->prepare("INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,1,?)")->execute([$username, $clientIp, $ua]);

                session_regenerate_id(true);
                $_SESSION['svi_admin_id']       = $admin['id'];
                $_SESSION['svi_admin_username'] = $admin['username'];
                $_SESSION['svi_last_active']    = time();
                $_SESSION['csrf_token']         = bin2hex(random_bytes(32));

                $next = $_GET['next'] ?? 'index.php';
                header('Location: ' . $next);
                exit;
            } else {
                // ✗ Wrong password
                $newAttempts = $admin['login_attempts'] + 1;
                $lockUntil   = null;
                if ($newAttempts >= $maxAttempts) {
                    $lockUntil = (new DateTime())->modify("+{$lockoutMins} minutes")->format('Y-m-d H:i:s');
                    $error = "Too many failed attempts. Account locked for {$lockoutMins} minutes.";
                } else {
                    $remaining = $maxAttempts - $newAttempts;
                    $error = "Incorrect username or password. {$remaining} attempt(s) remaining.";
                }
                $db->prepare("UPDATE `svi_admins` SET `login_attempts`=?, `locked_until`=? WHERE `id`=?")->execute([$newAttempts, $lockUntil, $admin['id']]);
                $db->prepare("INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,0,?)")->execute([$username, $clientIp, $ua]);
            }
        } else {
            $error = 'Incorrect username or password.';
            $db->prepare("INSERT INTO `svi_login_log` (`username`,`ip`,`success`,`user_agent`) VALUES (?,?,0,?)")->execute([$username, $clientIp, $ua]);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EH.SVI | Admin Sign In</title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" href="/app/img/ehltd_eh.png">
<link rel="stylesheet" href="/ehltd.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Serif+4:wght@300;400;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
  :root {
    --eh-purple:#4C2D8A; --svi-navy:#2E3A59; --svi-navy-light:#3D4E6B;
    --body-text:#1a1a2e; --muted:#5a6070; --border:#d0d4dc;
    --bg:#f3f4f7; --bg-white:#fff; --input-bg:#ecedf0;
    --perm-bg:#fceaea; --perm-col:#7a1515;
    --info-bg:#eaf0fb; --info-col:#1a3a6b;
  }
  body {
    font-family:'Source Serif 4',Georgia,serif;
    background:var(--bg); color:var(--body-text);
    min-height:100vh; display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    background-image: radial-gradient(ellipse at 80% 20%, rgba(76,45,138,0.07) 0%, transparent 60%),
                      radial-gradient(ellipse at 20% 80%, rgba(46,58,89,0.06) 0%, transparent 60%);
  }
  .login-box {
    background:var(--bg-white); border:1px solid var(--border);
    border-radius:14px; padding:44px 40px;
    width:100%; max-width:400px;
    box-shadow:0 4px 28px rgba(46,58,89,0.09);
  }
  .login-brand {
    display:flex; align-items:center; gap:14px; margin-bottom:32px;
  }
  .brand-eh {
    font-family:'Playfair Display',serif; font-weight:700;
    font-size:14px; line-height:1.2; color:var(--eh-purple);
  }
  .brand-divider { width:1px; height:36px; background:#b0b4c0; }
  .brand-svi { display:flex; flex-direction:column; gap:2px; }
  .brand-svi-title {
    font-family:'Playfair Display',serif;
    font-size:22px; color:var(--svi-navy); line-height:1;
  }
  .brand-svi-sub {
    font-size:10px; color:var(--svi-navy-light);
    letter-spacing:0.06em; font-weight:300;
  }
  h1 {
    font-family:'Playfair Display',serif; font-weight:400;
    font-size:22px; color:var(--svi-navy); margin-bottom:6px;
  }
  .sub { font-size:13px; color:var(--muted); font-weight:300; margin-bottom:28px; }
  .form-group { margin-bottom:16px; }
  label { display:block; font-size:12px; font-weight:600; color:var(--svi-navy); margin-bottom:6px; letter-spacing:0.04em; }
  input[type=text], input[type=password], input[type=email] {
    width:100%; background:var(--input-bg); border:1px solid transparent;
    border-radius:7px; padding:12px 16px;
    font-family:'Source Serif 4',Georgia,serif; font-size:14px;
    color:var(--body-text); outline:none;
    transition:background 0.15s, border-color 0.15s;
  }
  input:focus { background:#e2e4e8; border-color:var(--svi-navy-light); }
  .btn-login {
    width:100%; background:var(--svi-navy); color:#fff; border:none;
    border-radius:7px; padding:13px;
    font-family:'Source Serif 4',Georgia,serif; font-size:15px; font-weight:600;
    cursor:pointer; transition:background 0.18s, transform 0.1s; margin-top:8px;
    letter-spacing:0.02em;
  }
  .btn-login:hover { background:var(--svi-navy-light); }
  .btn-login:active { transform:scale(0.98); }
  .alert {
    padding:10px 14px; border-radius:7px; font-size:13px; margin-bottom:18px;
  }
  .alert-danger { background:var(--perm-bg); color:var(--perm-col); }
  .alert-info   { background:var(--info-bg); color:var(--info-col); }
  .footer-note {
    margin-top:24px; text-align:center;
    font-size:11px; color:var(--muted); font-weight:300;
  }
  .back-link {
    display:block; text-align:center; margin-top:12px;
    font-size:12px; color:var(--muted); text-decoration:none;
    transition:color 0.15s;
  }
  .back-link:hover { color:var(--svi-navy); }
</style>
</head>
<body>

<div class="login-box">
  <div class="login-brand">
    <img src="/app/img/eh_logo_web44.png" alt="Erickson Holding" style="height:36px; object-fit:contain;">
    <span class="brand-divider"></span>
    <span class="brand-svi">
      <span class="brand-svi-title">SVI</span>
      <span class="brand-svi-sub">Security and verification inspector</span>
    </span>
  </div>

  <h1>Admin Sign In</h1>
  <p class="sub">Restricted access — authorised personnel only.</p>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
  <?php endif; ?>
  <?php if (!empty($info)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($info, ENT_QUOTES) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email"
             value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>"
             autocomplete="email" autofocus required>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password"
             autocomplete="current-password" required>
    </div>
    <button class="btn-login" type="submit">Sign In →</button>
  </form>

  <p class="footer-note">SVI Admin Panel · Erickson Holding</p>
  <a class="back-link" href="../index.php">← Back to public page</a>
</div>

</body>
</html>
