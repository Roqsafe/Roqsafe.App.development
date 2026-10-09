<?php
// ── app/TorContribution/page.php ────────────────────────────────
// SVI – Security & Verification Inspector | Tor Network Commitment

$db = svi_db();
$c = svi_content($db, 'main'); 
$siteNotice = trim(svi_setting($db, 'site_notice', ''));

require_once __DIR__ . '/../../components/public_header.php';
?>

<main class="page-container">
  <div class="heading-block" style="margin-bottom: 40px;">
    <h1 style="font-family:'Playfair Display',serif; font-size: clamp(32px, 8vw, 48px); color: var(--svi-navy); font-weight: 700;">Tor Network Commitment</h1>
  </div>
  
  <div class="body-section" style="max-width: 800px;">
    <p style="font-size: 16px; line-height: 1.8; color: var(--muted); margin-bottom: 24px;">
      At Roqsafe, we recognize the essential role that anonymity networks play in global digital privacy. Our commitment to the Tor Project includes technical contributions and network integrity support.
    </p>
    <p style="font-size: 16px; line-height: 1.8; color: var(--muted);">
      Through our monitoring and verification tools, we aim to ensure the SVI ecosystem remains a safe and reliable gateway for privacy-conscious users worldwide. We believe that privacy is a fundamental human right and strive to protect it through advanced security inspections.
    </p>
  </div>
</main>

<?php include_once __DIR__ . '/../../components/public_footer.php'; ?>
