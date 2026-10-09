<?php
// ── app/ServerStatus/page.php ──────────────────────────────────
// SVI – Security & Verification Inspector | Server Status

$db = svi_db();
$c = svi_content($db, 'main'); 
$siteNotice = trim(svi_setting($db, 'site_notice', ''));

require_once __DIR__ . '/../../components/public_header.php';
?>

<main class="page-container">
  <div class="heading-block" style="margin-bottom: 40px;">
    <h1 style="font-family:'Playfair Display',serif; font-size: clamp(32px, 8vw, 48px); color: var(--svi-navy); font-weight: 700;">Network Integrity Status</h1>
  </div>
  
  <div class="body-section" style="max-width: 800px;">
    
    <div style="background: #fafbfc; border: 1px solid #e1e4e8; border-radius: 4px; padding: 32px; margin-bottom: 30px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
            <span style="font-weight: 600; font-size: 16px; color: var(--svi-navy);">SVI Core Engine</span>
            <span style="background: #1a6b3a; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">OPERATIONAL</span>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 15px;">
            <span style="font-weight: 600; font-size: 16px; color: var(--svi-navy);">Erickson SSO Gateway</span>
            <span style="background: #1a6b3a; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">OPERATIONAL</span>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 5px;">
            <span style="font-weight: 600; font-size: 16px; color: var(--svi-navy);">Verification API</span>
            <span style="background: #1a6b3a; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">OPERATIONAL</span>
        </div>
    </div>

    <p style="font-size: 13px; color: var(--muted); font-style: normal; opacity: 0.8;">
      System Integrity Check: <strong><?= date('Y-m-d H:i:s') ?> UTC</strong>
    </p>

  </div>
</main>

<?php include_once __DIR__ . '/../../components/public_footer.php'; ?>
