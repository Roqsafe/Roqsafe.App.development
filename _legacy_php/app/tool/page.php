<?php
// ── app/tool/page.php ──────────────────────────────────────────
// SVI – Security & Verification Inspector | Project Tools
// This page captures the {token} from the URL via router.php

$db = svi_db();
$token = $_GET['token'] ?? 'default';

// Load content for 'main' page (original tool content)
$c = svi_content($db, 'main');

// Settings
$maintenanceMode = svi_setting($db, 'maintenance_mode', '0') === '1';
$maintenanceMsg  = svi_setting($db, 'maintenance_message', 'We are currently performing scheduled maintenance.');
$showIpTool      = svi_setting($db, 'show_ip_tool', '1') === '1';
$siteNotice      = trim(svi_setting($db, 'site_notice', ''));

// Dynamic Block Context
$isBlocked = isset($blockRecord) && $blockRecord !== null;
$heroHeading = $isBlocked ? txt($c, 'hero_heading', 'Oh no…') : txt($c, 'hero_heading_safe', 'All Good!');
$heroEmoji   = $isBlocked ? txt($c, 'hero_emoji', '🚫') : txt($c, 'hero_emoji_safe', '✅');
$introP1     = $isBlocked ? txt($c, 'intro_p1') : txt($c, 'intro_p1_safe', 'You are a great user! Your IP address has been verified and is currently safe on our platform.');
$introP2     = $isBlocked ? txt($c, 'intro_p2') : txt($c, 'intro_p2_safe', 'Our systems continuously monitor network health to ensure Erickson Holding remains a secure environment. Your connection checks out perfectly.');

$sec1Title = $isBlocked ? txt($c, 'section1_title') : txt($c, 'section1_title_safe', 'Security First');
$sec1Body  = $isBlocked ? txt($c, 'section1_body') : txt($c, 'section1_body_safe', 'We prioritize your online safety by actively monitoring our network infrastructure.');
$sec2Title = $isBlocked ? txt($c, 'section2_title') : txt($c, 'section2_title_safe', 'Continuous Verification');
$sec2Body  = $isBlocked ? txt($c, 'section2_body') : txt($c, 'section2_body_safe', 'Occasional soft-checks help guarantee the ecosystem stays protected.');
$sec3Title = $isBlocked ? txt($c, 'section3_title') : txt($c, 'section3_title_safe', 'Data Privacy');
$sec3Body  = $isBlocked ? txt($c, 'section3_body') : txt($c, 'section3_body_safe', 'Your session and connection details remain securely encrypted.');
$sec4Title = $isBlocked ? txt($c, 'section4_title') : txt($c, 'section4_title_safe', 'Best Practices');
$sec4Item1 = $isBlocked ? txt($c, 'section4_item1') : txt($c, 'section4_item1_safe', 'Always use strong, unique passwords.');
$sec4Item2 = $isBlocked ? txt($c, 'section4_item2') : txt($c, 'section4_item2_safe', 'Enable Two-Factor Authentication.');
$sec4Item3 = $isBlocked ? txt($c, 'section4_item3') : txt($c, 'section4_item3_safe', 'Regularly monitor your active sessions.');
$closing   = $isBlocked ? txt($c, 'closing_note') : txt($c, 'closing_note_safe', 'Thank you for being a part of our secure platform.');

// Include Header
require_once __DIR__ . '/../../components/public_header.php';
?>

<main class="page-container">

<?php if ($maintenanceMode): ?>
  <div class="maintenance-box">
    <div class="m-icon">🔧</div>
    <h1>Under Maintenance</h1>
    <p><?= htmlspecialchars($maintenanceMsg, ENT_QUOTES) ?></p>
  </div>
<?php else: ?>

<?php if ($token === 'default'): ?>
<script>
  setTimeout(function() {
    window.location.reload();
  }, 60000); // automatic refresh
</script>
<?php endif; ?>

  <!-- Hero heading -->
  <div class="heading-block">
    <h1>
      <?= htmlspecialchars($heroHeading, ENT_QUOTES) ?>
      <span class="emoji"><?= $heroEmoji ?></span>
    </h1>
    <?php if ($token !== 'default'): ?>
    <p style="font-size: 14px; color: var(--muted); margin-top: 8px;">Active Session Token: <code><?= htmlspecialchars($token, ENT_QUOTES) ?></code></p>
    <?php endif; ?>
  </div>

  <!-- Intro paragraphs -->
  <div class="body-section"><p><?= htmlspecialchars($introP1, ENT_QUOTES) ?></p></div>
  <div class="body-section"><p><?= htmlspecialchars($introP2, ENT_QUOTES) ?></p></div>

  <?php if ($showIpTool): ?>
  <!-- IP check form -->
  <style>
    .ip-form { margin:28px 0 16px; display:flex; flex-direction:column; gap:10px; max-width:420px; }
    .ip-row { display:flex; gap:8px; }
    .ip-input {
      flex:1; background:var(--input-bg); border:none; border-radius:6px;
      padding:14px 20px; font-family:'Source Serif 4',serif; font-size:15px;
      color:var(--body-text); outline:none; transition:background 0.2s;
    }
    .ip-input:focus { background:#dddfe4; }
    .ip-btn {
      background:var(--svi-navy); color:#fff; border:none; border-radius:6px;
      padding:0 22px; font-family:'Source Serif 4',serif; font-size:14px;
      font-weight:600; cursor:pointer; transition:background 0.2s, transform 0.1s;
      white-space:nowrap;
    }
    .ip-btn:hover { background:var(--svi-navy-light); }
    .ip-hint { font-size:11px; color:var(--muted); letter-spacing:0.03em; text-align:center; }
    #result-box { display:none; border-radius:8px; padding:14px 18px; font-size:14px; line-height:1.65; max-width:420px; margin-top:4px; }
    #result-box.show { display:block; }
    #result-box.not_found { background:var(--status-ok-bg);     color:var(--status-ok); }
    #result-box.deblocked { background:var(--status-info-bg);   color:var(--status-info); }
    #result-box.soft      { background:var(--status-warn-bg);   color:var(--status-warn); }
    #result-box.permanent { background:var(--status-danger-bg); color:var(--status-danger); }
    #result-box.pending   { background:var(--status-warn-bg);   color:var(--status-warn); }
    #result-box.ok        { background:var(--status-ok-bg);     color:var(--status-ok); }
    #result-box.error     { background:var(--status-danger-bg); color:var(--status-danger); }
    .spinner { display:inline-block; width:14px; height:14px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin 0.7s linear infinite; vertical-align:middle; }
    @keyframes spin { to { transform:rotate(360deg); } }
    .deblock-btn { display:inline-block; margin-top:10px; background:var(--status-warn); color:#fff; border:none; border-radius:5px; padding:8px 18px; font-family:'Source Serif 4',serif; font-size:13px; font-weight:600; cursor:pointer; }
  </style>
  <?php if ($isBlocked): ?>
  <div class="ip-form" style="background: var(--status-danger-bg); padding: 20px 24px; border-radius: 8px; border: 1px dashed var(--status-danger); display: flex; flex-direction: column; gap: 14px; margin-top: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
        <span style="color: var(--status-danger); font-weight: 600; font-size: 15px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
            Self-Verification Suspended
        </span>
        <a href="/About" class="ip-btn" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; background: var(--status-danger);">Contact Support</a>
    </div>
    <p style="font-size: 13px; color: var(--status-danger); margin: 0; line-height: 1.5; opacity: 0.85;">Your network segment is currently restricted. Manual IP verification tools and self-service checks are disabled while your address remains blocked.</p>
  </div>
  <?php else: ?>
  <form class="ip-form" id="ip-form" autocomplete="off" novalidate>
    <div class="ip-row">
      <input
        class="ip-input" id="ip-input" type="text" name="ip"
        placeholder="<?= htmlspecialchars(txt($c, 'ip_input_placeholder', 'type your ip in for deblock'), ENT_QUOTES) ?>"
        aria-label="Enter your IP address" maxlength="45"
      >
      <button class="ip-btn" id="check-btn" type="submit">
        <?= htmlspecialchars(txt($c, 'ip_submit_btn', 'Check IP'), ENT_QUOTES) ?>
      </button>
    </div>
    <span class="ip-hint">Check if the unblock works for you</span>
    <div id="result-box" role="alert" aria-live="polite"></div>
  </form>
  <?php endif; ?>
  <?php endif; ?>

  <!-- Sections -->
  <h2 class="section-title" style="font-family:'Playfair Display',serif; font-weight:400; font-size:clamp(22px,5vw,32px); color:var(--svi-navy); margin:44px 0 16px; line-height:1.2;"><?= htmlspecialchars($sec1Title, ENT_QUOTES) ?></h2>
  <p class="section-body"><?= htmlspecialchars($sec1Body, ENT_QUOTES) ?></p>

  <h2 class="section-title" style="font-family:'Playfair Display',serif; font-weight:400; font-size:clamp(22px,5vw,32px); color:var(--svi-navy); margin:44px 0 16px; line-height:1.2;"><?= htmlspecialchars($sec2Title, ENT_QUOTES) ?></h2>
  <p class="section-body"><?= htmlspecialchars($sec2Body, ENT_QUOTES) ?></p>

  <h2 class="section-title" style="font-family:'Playfair Display',serif; font-weight:400; font-size:clamp(22px,5vw,32px); color:var(--svi-navy); margin:44px 0 16px; line-height:1.2;"><?= htmlspecialchars($sec3Title, ENT_QUOTES) ?></h2>
  <p class="section-body"><?= htmlspecialchars($sec3Body, ENT_QUOTES) ?></p>

  <div class="note-box" style="margin-top: 30px; border-left: 4px solid var(--eh-purple); padding-left: 20px;">
    <h3 class="note-title" style="font-family:'Playfair Display',serif; font-size: 22px; color: var(--svi-navy); margin-bottom: 10px;"><?= htmlspecialchars($sec4Title, ENT_QUOTES) ?></h3>
    <ul style="list-style: none; padding: 0;">
      <li style="margin-bottom: 8px; font-weight: 300;">— <?= htmlspecialchars($sec4Item1, ENT_QUOTES) ?></li>
      <li style="margin-bottom: 8px; font-weight: 300;">— <?= htmlspecialchars($sec4Item2, ENT_QUOTES) ?></li>
      <li style="margin-bottom: 8px; font-weight: 300;">— <?= htmlspecialchars($sec4Item3, ENT_QUOTES) ?></li>
    </ul>
  </div>

  <p class="section-body" style="font-weight:600; margin-top:32px;">
    <?= htmlspecialchars($closing, ENT_QUOTES) ?>
  </p>

<?php endif; ?>

</main>

<?php if ($showIpTool && !$maintenanceMode): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
  'use strict';
  const form      = document.getElementById('ip-form');
  const input     = document.getElementById('ip-input');
  const checkBtn  = document.getElementById('check-btn');
  const resultBox = document.getElementById('result-box');
  let lastIp = '';

  function showResult(status, html) {
    resultBox.className = 'show ' + status;
    resultBox.innerHTML = html;
  }

  function setLoading(btn, yes) {
    btn.disabled = yes;
    if (yes) { btn.dataset.orig = btn.textContent; btn.innerHTML = '<span class="spinner"></span>'; }
    else      { btn.textContent = btn.dataset.orig || btn.textContent; }
  }

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      const ip = input.value.trim();
      if (!ip) { input.focus(); return; }
      lastIp = ip;
      setLoading(checkBtn, true);
      resultBox.className = ''; resultBox.innerHTML = '';
      try {
        const res  = await fetch('/api/block_check', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ip}) });
        const data = await res.json();
        let html = data.message || 'Unknown response.';
        if (data.status === 'soft' && !data.deblock_requested) {
          html += `<br><button class="deblock-btn" id="deblock-btn" style="margin-top:20px;" type="button">Request Deblock</button>`;
        }
        
        let targetIcon = 'warning';
        let targetTitle = 'Action Required';
        if (data.status === 'not_found' || data.status === 'deblocked') { targetIcon = 'success'; targetTitle = 'Verified'; }
        if (data.status === 'error') { targetIcon = 'error'; targetTitle = 'Error'; }

        Swal.fire({
          title: targetTitle,
          html: html,
          icon: targetIcon,
          confirmButtonColor: 'var(--eh-purple)',
          didOpen: () => {
             const pb = Swal.getPopup().querySelector('#deblock-btn');
             if (pb) pb.addEventListener('click', handleDeblock);
          }
        });

        // Also update standard box
        showResult(data.status, html);
        const db = document.getElementById('deblock-btn');
        if (db) db.addEventListener('click', handleDeblock);
      } catch (err) {
        showResult('error', 'A network error occurred. Please try again.');
      } finally {
        setLoading(checkBtn, false);
      }
    });
  }

  async function handleDeblock() {
    const btn = document.getElementById('deblock-btn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Submitting…'; }
    try {
      const res  = await fetch('/api/deblock', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ip: lastIp}) });
      const data = await res.json();
      Swal.fire({
          title: data.status === 'success' ? 'Debloc Requested' : 'Notice',
          html: data.message || 'Done.',
          icon: data.status === 'success' ? 'success' : 'info',
          confirmButtonColor: 'var(--eh-purple)'
      });
      showResult(data.status, data.message || 'Done.');
    } catch (err) {
      showResult('error', 'Network error while submitting deblock request.');
    }
  }
})();
</script>
<?php endif; ?>

<?php include_once __DIR__ . '/../../components/public_footer.php'; ?>
