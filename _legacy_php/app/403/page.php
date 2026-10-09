<?php
// ── app/403/page.php ──────────────────────────────────────────

$db = svi_db();
$c  = svi_content($db, '403');
$siteNotice = trim(svi_setting($db, 'site_notice', ''));

$pageTitle = txt($c, 'page_title', '403 Forbidden — SVI');
$heading   = txt($c, 'heading',    'Access Denied');
$emoji     = txt($c, 'emoji',      '⛔');
$body      = txt($c, 'body',       'You do not have permission to view this resource.');
$btn       = txt($c, 'btn_label',  'Return to Home');

require_once __DIR__ . '/../../components/public_header.php';
?>

<style>
  .error-full {
    min-height: 60vh;
    display: flex; align-items: center; justify-content: center;
    padding: 60px 24px;
  }
  .error-box {
    max-width: 520px; width: 100%;
    text-align: center; padding: 56px 40px;
    border: 1px solid var(--border); border-radius: 16px;
    box-shadow: 0 20px 60px rgba(46,58,89,0.07);
    background: #fff;
  }
  .error-code {
    font-family: 'Outfit', 'Inter', sans-serif;
    font-size: 96px; font-weight: 800;
    color: var(--border); line-height: 1;
    margin-bottom: 4px; letter-spacing: -4px;
  }
  .error-emoji { font-size: 52px; margin-bottom: 16px; display: block; }
  .error-box h1 {
    font-family: 'Playfair Display', serif; font-weight: 700;
    font-size: 30px; color: var(--svi-navy); margin-bottom: 14px;
  }
  .error-box p { font-size: 15px; color: var(--muted); margin-bottom: 32px; line-height: 1.7; }
  .error-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--eh-purple); color: #fff;
    text-decoration: none; padding: 13px 28px; border-radius: 8px;
    font-size: 14px; font-weight: 700; transition: all 0.2s;
  }
  .error-btn:hover { background: #3b236b; transform: translateY(-1px); }
  .error-links { margin-top: 20px; display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
  .error-link { font-size: 13px; color: var(--muted); text-decoration: none; }
  .error-link:hover { color: var(--eh-purple); }
</style>

<div class="error-full">
  <div class="error-box">
    <div class="error-code">403</div>
    <span class="error-emoji"><?= htmlspecialchars($emoji, ENT_QUOTES) ?></span>
    <h1><?= htmlspecialchars($heading, ENT_QUOTES) ?></h1>
    <p><?= htmlspecialchars($body, ENT_QUOTES) ?></p>
    <a href="/" class="error-btn">
      <i data-lucide="home" style="width:16px;height:16px;"></i>
      <?= htmlspecialchars($btn, ENT_QUOTES) ?>
    </a>
    <div class="error-links">
      <a href="/About" class="error-link">Contact Support</a>
      <a href="/Verifi/data/user/default" class="error-link">Verify IP Status</a>
    </div>
  </div>
</div>

<script>document.addEventListener('DOMContentLoaded',()=>{ if(typeof lucide!=='undefined') lucide.createIcons(); });</script>

<?php include_once __DIR__ . '/../../components/public_footer.php'; ?>
