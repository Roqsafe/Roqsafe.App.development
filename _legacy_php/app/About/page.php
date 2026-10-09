<?php
// ── app/About/page.php ─────────────────────────────────────────
// SVI – Security & Verification Inspector | Support & Contact

$db = svi_db();
$c = svi_content($db, 'main');
$siteNotice = trim(svi_setting($db, 'site_notice', ''));
$pageTitle = 'Support — Roqsafe SVI';

// Detect visitor IP
$visitorIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
if (strpos($visitorIp, ',') !== false) {
    $visitorIp = trim(explode(',', $visitorIp)[0]);
}

// Handle form POST → send mail
$formSent  = false;
$formError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['sp_name'])) {
    $to      = 'security@roqsafe.com';
    $name    = strip_tags($_POST['sp_name']   ?? '');
    $email   = strip_tags($_POST['sp_email']  ?? '');
    $topic   = strip_tags($_POST['sp_topic']  ?? '');
    $ip      = strip_tags($_POST['sp_ip']     ?? $visitorIp);
    $message = strip_tags($_POST['sp_message']?? '');

    $subject = "[Roqsafe SVI Support] {$topic} – from {$name}";
    $body    = "Name: {$name}\nEmail: {$email}\nTopic: {$topic}\nIP Address: {$ip}\n\nMessage:\n{$message}";
    
    require_once __DIR__ . '/../../lib/mail.php';
    
    // Attempt SMTP delivery via Spacemail
    if (svi_mail($to, $subject, $body, ['Reply-To' => $email])) {
        $formSent = true;
    } else {
        $formError = 'Mail delivery failed. Please email us directly at security@roqsafe.com.';
    }
}

require_once __DIR__ . '/../../components/public_header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
.sp-hero {
  background: #fff;
  border-bottom: 1px solid var(--border);
  padding: 64px 24px 56px;
}
.sp-hero-inner {
  max-width: 900px; margin: 0 auto;
  display: flex; align-items: center; justify-content: space-between; gap: 48px;
  flex-wrap: wrap;
}
.sp-hero-badge {
  display: inline-flex; align-items: center; gap: 7px;
  background: #f4f0ff; border: 1px solid #d6c9f8;
  padding: 5px 14px; border-radius: 50px; margin-bottom: 20px;
  color: var(--eh-purple); font-size: 11px; font-weight: 700; letter-spacing: 0.09em; text-transform: uppercase;
}
.sp-hero h1 {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(32px, 5vw, 52px);
  font-weight: 700; color: var(--svi-navy);
  letter-spacing: -0.02em; margin-bottom: 16px; line-height: 1.15;
}
.sp-hero p {
  font-size: 17px; color: var(--muted); line-height: 1.7; max-width: 520px;
}
.sp-hero-status {
  background: var(--input-bg); border-radius: 14px; padding: 28px;
  display: flex; flex-direction: column; gap: 14px; min-width: 240px;
}
.sp-status-row {
  display: flex; align-items: center; justify-content: space-between; gap: 20px;
}
.sp-status-label { font-size: 13px; color: var(--muted); font-weight: 500; }
.sp-status-dot { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; }
.sp-status-dot::before { content: ''; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.sp-status-dot.ok::before { background: var(--status-ok); box-shadow: 0 0 6px rgba(26,107,58,0.5); }
.sp-status-dot.ok { color: var(--status-ok); }

/* Main grid */
.sp-content {
  max-width: 1100px; margin: 64px auto; padding: 0 24px 80px;
  display: grid; grid-template-columns: 1fr 380px; gap: 40px;
}
@media (max-width: 900px) {
  .sp-content { grid-template-columns: 1fr; }
  .sp-hero-inner { flex-direction: column; }
}

/* Contact form card */
.sp-form-card {
  background: #fff; border: 1px solid var(--border);
  border-radius: 16px; padding: 40px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.05);
}
.sp-form-title {
  font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 700;
  color: var(--svi-navy); margin-bottom: 8px;
}
.sp-form-desc { font-size: 14px; color: var(--muted); margin-bottom: 28px; line-height: 1.6; }

.sp-form-group { margin-bottom: 20px; }
.sp-label { display: block; font-size: 12px; font-weight: 700; color: var(--svi-navy); margin-bottom: 7px; letter-spacing: 0.04em; text-transform: uppercase; }
.sp-input, .sp-select, .sp-textarea {
  width: 100%; padding: 12px 16px;
  background: var(--input-bg); border: 1px solid transparent;
  border-radius: 8px; font-size: 14px; color: var(--body-text);
  font-family: 'Inter', sans-serif; outline: none;
  transition: all 0.2s;
}
.sp-input:focus, .sp-select:focus, .sp-textarea:focus {
  border-color: var(--eh-purple);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(76,45,138,0.08);
}
.sp-textarea { min-height: 130px; resize: vertical; }
.sp-submit {
  width: 100%; padding: 14px;
  background: var(--eh-purple); color: #fff;
  border: none; border-radius: 8px;
  font-size: 15px; font-weight: 700; cursor: pointer;
  font-family: 'Inter', sans-serif;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: all 0.25s;
}
.sp-submit:hover { background: #3b236b; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(76,45,138,0.3); }
.sp-success {
  display: none; text-align: center; padding: 20px;
  background: var(--status-ok-bg); border-radius: 8px;
  color: var(--status-ok); font-weight: 600; font-size: 15px; margin-top: 16px;
}

/* Sidebar */
.sp-sidebar { display: flex; flex-direction: column; gap: 24px; }

.sp-info-card {
  background: #fff; border: 1px solid var(--border);
  border-radius: 14px; padding: 28px;
}
.sp-info-title {
  font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700;
  color: var(--svi-navy); margin-bottom: 18px;
  display: flex; align-items: center; gap: 10px;
}

.sp-contact-row {
  display: flex; align-items: flex-start; gap: 14px; padding: 14px 0;
  border-bottom: 1px solid #f0f0f0;
}
.sp-contact-row:last-child { border-bottom: none; }
.sp-contact-icon {
  width: 38px; height: 38px; border-radius: 10px;
  background: #f4f0ff; color: var(--eh-purple);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.sp-contact-method { font-size: 12px; color: var(--muted); font-weight: 500; margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.05em; }
.sp-contact-value { font-size: 14px; font-weight: 600; color: var(--svi-navy); }
.sp-contact-value a { color: var(--eh-purple); text-decoration: none; }
.sp-contact-value a:hover { text-decoration: underline; }

/* FAQ */
.sp-faq-item {
  border-bottom: 1px solid var(--border); padding: 16px 0;
}
.sp-faq-q {
  font-weight: 700; color: var(--svi-navy); font-size: 14px;
  cursor: pointer; display: flex; justify-content: space-between; align-items: center;
  gap: 12px; user-select: none;
}
.sp-faq-a {
  font-size: 13px; color: var(--muted); line-height: 1.7; display: none; margin-top: 10px;
}
.sp-faq-item.open .sp-faq-a { display: block; }
.sp-faq-chevron { transition: transform 0.25s; flex-shrink: 0; }
.sp-faq-item.open .sp-faq-chevron { transform: rotate(180deg); }

/* About company section */
.sp-about-strip {
  background: linear-gradient(135deg, #0f0c29, #302b63);
  border-radius: 16px; padding: 48px;
  text-align: center; margin: 0 24px 64px;
  max-width: 1052px; margin-left: auto; margin-right: auto;
}
.sp-about-strip h2 { font-family: 'Outfit', sans-serif; font-size: 30px; font-weight: 700; color: #fff; margin-bottom: 14px; }
.sp-about-strip p { color: rgba(255,255,255,0.6); font-size: 15px; line-height: 1.7; max-width: 640px; margin: 0 auto; }
</style>

<!-- Hero -->
<div class="sp-hero">
  <div class="sp-hero-inner">
    <div>
      <div class="sp-hero-badge">
        <i data-lucide="headphones" style="width:12px;height:12px;"></i>
        Roqsafe SVI Support
      </div>
      <h1>How can we<br>help you?</h1>
      <p>Our security team is ready to assist with IP blocks, account verifications, and all platform-related issues. We typically respond within 24 hours.</p>
    </div>
    <div class="sp-hero-gfx">
      <img src="/app/img/svi_locker.png" alt="Security Locker" style="width: 240px; height: auto; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.1));">
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="sp-content">

  <!-- Contact Form -->
  <div class="sp-form-card">
    <div class="sp-form-title">Send a Support Request</div>
    <div class="sp-form-desc">Fill in the form below and a Roqsafe SVI specialist will contact you. For urgent IP unblock requests, please mention your IP address.</div>

    <form id="sp-form" method="POST" action="/About">
      <div class="sp-form-group">
        <label class="sp-label" for="sp-name">Full Name</label>
        <input class="sp-input" id="sp-name" name="sp_name" type="text" placeholder="Your name" required>
      </div>
      <div class="sp-form-group">
        <label class="sp-label" for="sp-email">Email Address</label>
        <input class="sp-input" id="sp-email" name="sp_email" type="email" placeholder="you@example.com" required>
      </div>
      <div class="sp-form-group">
        <label class="sp-label" for="sp-topic">Topic</label>
        <select class="sp-select" id="sp-topic" name="sp_topic" onchange="autoFillMessage(this.value)">
          <option value="">Select a topic…</option>
          <option value="IP Block / Unblock Request">IP Block / Unblock Request</option>
          <option value="Identity Verification">Identity Verification</option>
          <option value="Account Access Issue">Account Access Issue</option>
          <option value="Security Incident Report">Security Incident Report</option>
          <option value="MySpeed / Network Tools">MySpeed / Network Tools</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="sp-form-group">
        <label class="sp-label" for="sp-ip">Your IP Address (optional)</label>
        <input class="sp-input" id="sp-ip" name="sp_ip" type="text" value="<?= htmlspecialchars($visitorIp, ENT_QUOTES) ?>">
      </div>
      <div class="sp-form-group">
        <label class="sp-label" for="sp-message">Message</label>
        <textarea class="sp-textarea" id="sp-message" name="sp_message" placeholder="Describe your issue in detail…" required></textarea>
      </div>
      <button type="submit" class="sp-submit" id="sp-submit-btn">
        <i data-lucide="send" style="width:16px;height:16px;"></i>
        Send Request
      </button>
      <?php if ($formSent): ?>
      <div class="sp-success" id="sp-success" style="display:block;">✅ Your request has been submitted. We'll be in touch within 24 hours.</div>
      <?php elseif ($formError): ?>
      <div class="sp-success" id="sp-success" style="display:block; background:var(--status-danger-bg); color:var(--status-danger);"><?= htmlspecialchars($formError) ?></div>
      <?php else: ?>
      <div class="sp-success" id="sp-success">✅ Your request has been submitted. We'll be in touch within 24 hours.</div>
      <?php endif; ?>
    </form>
  </div>

  <!-- Sidebar -->
  <div class="sp-sidebar">

    <!-- Contact Info -->
    <div class="sp-info-card">
      <div class="sp-info-title">
        <i data-lucide="map-pin" style="width:20px;height:20px; color:var(--eh-purple);"></i>
        Contact Details
      </div>
      <div class="sp-contact-row">
        <div class="sp-contact-icon"><i data-lucide="clock" style="width:18px;height:18px;"></i></div>
        <div>
          <div class="sp-contact-method">Response Time</div>
          <div class="sp-contact-value">Within 24 hours</div>
        </div>
      </div>
      <div class="sp-contact-row">
        <div class="sp-contact-icon"><i data-lucide="globe" style="width:18px;height:18px;"></i></div>
        <div>
          <div class="sp-contact-method">Headquarters</div>
          <div class="sp-contact-value">Roqsafe LTD, Netherlands</div>
        </div>
      </div>
    </div>

    <!-- FAQ -->
    <div class="sp-info-card">
      <div class="sp-info-title">
        <i data-lucide="circle-help" style="width:20px;height:20px; color:var(--eh-purple);"></i>
        Frequently Asked
      </div>

      <div class="sp-faq-item">
        <div class="sp-faq-q">
          My IP is blocked. What do I do?
          <i data-lucide="chevron-down" class="sp-faq-chevron" style="width:16px;height:16px;"></i>
        </div>
        <div class="sp-faq-a">Visit the <a href="/Verifi/data/user/default" style="color:var(--eh-purple);">Verification Portal</a> and enter your IP address. If blocked, submit a deblock request or contact support directly.</div>
      </div>

      <div class="sp-faq-item">
        <div class="sp-faq-q">
          How long does an IP block last?
          <i data-lucide="chevron-down" class="sp-faq-chevron" style="width:16px;height:16px;"></i>
        </div>
        <div class="sp-faq-a">Soft blocks are typically reviewed within 48 hours. Permanent blocks require a manual review by our security team and direct written communication.</div>
      </div>

      <div class="sp-faq-item">
        <div class="sp-faq-q">
          What triggers an automatic block?
          <i data-lucide="chevron-down" class="sp-faq-chevron" style="width:16px;height:16px;"></i>
        </div>
        <div class="sp-faq-a">Our system automatically blocks IPs that exceed daily check limits, attempt access to restricted routes, or display patterns consistent with unauthorized scanning.</div>
      </div>

      </div>

    </div>

    <!-- Quick Links -->
    <div class="sp-info-card">
      <div class="sp-info-title">
        <i data-lucide="link" style="width:20px;height:20px; color:var(--eh-purple);"></i>
        Quick Links
      </div>
      <div style="display:flex; flex-direction:column; gap:10px;">
        <a href="/Verifi/data/user/default" style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--input-bg); border-radius:8px; text-decoration:none; color:var(--svi-navy); font-size:14px; font-weight:600; transition:all 0.2s;">
          <i data-lucide="shield-check" style="width:16px;height:16px; color:var(--eh-purple);"></i>
          IP Verification Portal
        </a>
        <a href="/myspeed" style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--input-bg); border-radius:8px; text-decoration:none; color:var(--svi-navy); font-size:14px; font-weight:600; transition:all 0.2s;">
          <i data-lucide="gauge" style="width:16px;height:16px; color:var(--eh-purple);"></i>
          MySpeed Monitor
        </a>
        <a href="/" style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--input-bg); border-radius:8px; text-decoration:none; color:var(--svi-navy); font-size:14px; font-weight:600; transition:all 0.2s;">
          <i data-lucide="home" style="width:16px;height:16px; color:var(--eh-purple);"></i>
          Back to Home
        </a>
      </div>
    </div>

  </div>
</div>

<script>
const topicMessages = {
  'IP Block / Unblock Request': 'Hello Roqsafe SVI team,\n\nI would like to request an unblock review for the IP address listed above. This IP is associated with my connection and I believe the block was triggered in error.\n\nPlease review and let me know what steps I need to take.\n\nThank you.',
  'Identity Verification': 'Hello Roqsafe SVI team,\n\nI need assistance verifying my identity on the SVI platform. I am unable to access certain sections and believe my account requires manual verification.\n\nPlease advise on the required steps.\n\nThank you.',
  'Account Access Issue': 'Hello Roqsafe SVI team,\n\nI am experiencing issues accessing my account. I am unable to log in and have tried resetting my credentials without success.\n\nCould you please look into this for me?\n\nThank you.',
  'Security Incident Report': 'Hello Roqsafe SVI Security Team,\n\nI am writing to report a potential security incident. I have noticed suspicious activity related to the IP address listed above and wanted to bring it to your attention immediately.\n\nPlease investigate at your earliest convenience.\n\nThank you.',
  'MySpeed / Network Tools': 'Hello Roqsafe SVI team,\n\nI need assistance with the MySpeed Network Monitor tool. I am encountering issues with the setup or configuration and would appreciate guidance.\n\nThank you.',
  'Other': 'Hello Roqsafe SVI team,\n\nI am reaching out regarding a general inquiry. Please see my details below and feel free to contact me at the email address provided.\n\nThank you.'
};

function autoFillMessage(topic) {
  const msg = document.getElementById('sp-message');
  if (topic && topicMessages[topic]) {
    msg.value = topicMessages[topic];
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (typeof lucide !== 'undefined') lucide.createIcons();

  // FAQ accordion
  document.querySelectorAll('.sp-faq-q').forEach(q => {
    q.addEventListener('click', () => {
      const item = q.closest('.sp-faq-item');
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.sp-faq-item').forEach(i => i.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    });
  });
});
</script>

<?php include_once __DIR__ . '/../../components/public_footer.php'; ?>
