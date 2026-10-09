<?php
// ── app/page.php ──────────────────────────────────────────────
// SVI – Security & Verification Inspector | Home

$db = svi_db();
$c = svi_content($db, 'main');
$siteNotice = trim(svi_setting($db, 'site_notice', ''));

require_once __DIR__ . '/../components/public_header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
/* ── Page base ───────────────────────────────────────────────── */
.hp-wrap { background: #fff; }

/* ── HERO ────────────────────────────────────────────────────── */
.hp-hero {
  max-width: 1200px; margin: 0 auto;
  padding: 80px 32px 64px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 64px;
  align-items: center;
}
@media (max-width: 900px) {
  .hp-hero { grid-template-columns: 1fr; gap: 40px; padding: 56px 24px 48px; }
  .hp-hero-right { display: none; }
}

/* Left column */
.hp-trust-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: transparent;
  border: 1px solid #e2e8f0;
  color: #475569;
  padding: 4px 12px 4px 8px;
  border-radius: 50px;
  font-size: 12px; font-weight: 500;
  margin-bottom: 22px;
  width: fit-content;
}
.hp-trust-badge .trust-star {
  color: #22c55e; font-size: 14px; line-height: 1;
}
.hp-trust-badge .trust-text {
  color: #64748b; font-size: 12px; font-weight: 500;
}

.hp-hero h1 {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(38px, 5.5vw, 68px);
  font-weight: 800;
  color: var(--svi-navy);
  line-height: 1.08;
  letter-spacing: -0.03em;
  margin-bottom: 20px;
}

.hp-hero p {
  font-size: 17px; color: var(--muted); line-height: 1.7;
  max-width: 480px; margin-bottom: 36px;
}

.hp-cta-row { display: flex; gap: 14px; flex-wrap: wrap; align-items: center; }

.hp-btn-primary {
  display: inline-flex; align-items: center; gap: 8px;
  background: var(--svi-navy); color: #fff;
  padding: 14px 28px; border-radius: 8px;
  text-decoration: none; font-size: 15px; font-weight: 700;
  transition: all 0.25s;
}
.hp-btn-primary:hover { background: #1a2640; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(46,58,89,0.3); }

.hp-btn-secondary {
  display: inline-flex; align-items: center; gap: 8px;
  background: #fff; color: var(--svi-navy);
  border: 1.5px solid var(--border);
  padding: 13px 26px; border-radius: 8px;
  text-decoration: none; font-size: 15px; font-weight: 600;
  transition: all 0.25s;
}
.hp-btn-secondary:hover { border-color: var(--svi-navy); transform: translateY(-1px); }

/* Right column — floating UI mockup */
.hp-hero-right {
  position: relative; height: 420px;
}
.hp-float-card {
  position: absolute;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 16px 20px;
  box-shadow: 0 12px 40px rgba(46,58,89,0.12);
  font-family: 'Inter', sans-serif;
}
.hp-card-ip {
  top: 0; left: 10%;
  min-width: 220px;
}
.hp-card-ip .ip-label { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 8px; }
.hp-card-ip .ip-addr { font-family: 'Outfit', monospace; font-size: 22px; font-weight: 800; color: var(--svi-navy); margin-bottom: 10px; }
.hp-card-ip .ip-status {
  display: inline-flex; align-items: center; gap: 6px;
  background: #ecfdf5; color: #059669; padding: 5px 12px; border-radius: 50px;
  font-size: 12px; font-weight: 700;
}
.hp-card-ip .ip-status::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #10b981; }

.hp-card-stat {
  bottom: 60px; right: 0;
  min-width: 170px;
  text-align: center;
}
.hp-card-stat .stat-num { font-family: 'Outfit', sans-serif; font-size: 38px; font-weight: 800; color: var(--eh-purple); line-height: 1; }
.hp-card-stat .stat-lbl { font-size: 12px; color: var(--muted); margin-top: 4px; font-weight: 500; }

.hp-card-alert {
  top: 50%; left: 0; transform: translateY(-50%);
  min-width: 200px;
  border-left: 4px solid #ef4444;
}
.hp-card-alert .alert-title { font-size: 13px; font-weight: 700; color: #991b1b; margin-bottom: 4px; }
.hp-card-alert .alert-body  { font-size: 12px; color: var(--muted); }

.hp-card-shield {
  bottom: 20px; left: 25%;
  display: flex; align-items: center; gap: 12px;
  min-width: 190px;
}
.hp-card-shield .shield-icon {
  width: 40px; height: 40px; border-radius: 10px;
  background: #f4f0ff; color: var(--eh-purple);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.hp-card-shield .shield-text-title { font-size: 13px; font-weight: 700; color: var(--svi-navy); }
.hp-card-shield .shield-text-sub   { font-size: 11px; color: var(--muted); }

/* ── Divider line ────────────────────────────────────────────── */
.hp-divider { border: none; border-top: 1px solid var(--border); }

/* ── STATS ROW ───────────────────────────────────────────────── */
.hp-stats-section {
  padding: 72px 32px;
  max-width: 1200px; margin: 0 auto;
}
.hp-stats-eyebrow {
  font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
  color: var(--muted); margin-bottom: 10px;
  display: flex; align-items: center; gap: 10px;
}
.hp-stats-eyebrow::after { content: ''; flex: 1; height: 1px; background: var(--border); }
.hp-stats-headline {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(26px, 4vw, 36px); font-weight: 700;
  color: var(--svi-navy); margin-bottom: 48px;
}
.hp-stats-row {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 0;
  border: 1px solid var(--border); border-radius: 16px; overflow: hidden;
}
@media (max-width: 768px) { .hp-stats-row { grid-template-columns: 1fr 1fr; } }
.hp-stat-cell {
  padding: 36px 28px; text-align: center;
  border-right: 1px solid var(--border);
}
.hp-stat-cell:last-child { border-right: none; }
.hp-stat-num {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(32px, 4vw, 52px); font-weight: 800;
  color: var(--svi-navy); line-height: 1;
  margin-bottom: 6px;
}
.hp-stat-num span { color: var(--eh-purple); }
.hp-stat-label { font-size: 13px; color: var(--muted); font-weight: 500; }

/* ── FEATURES SPLIT ──────────────────────────────────────────── */
.hp-features-section {
  border-top: 1px solid var(--border);
  padding: 80px 32px;
  max-width: 1200px; margin: 0 auto;
}
.hp-features-top {
  display: flex; align-items: flex-end; justify-content: space-between;
  gap: 40px; flex-wrap: wrap; margin-bottom: 48px;
}
.hp-features-section-label {
  font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
  color: var(--eh-purple); margin-bottom: 10px;
}
.hp-features-section-title {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(26px, 4vw, 40px); font-weight: 700;
  color: var(--svi-navy); letter-spacing: -0.02em; max-width: 480px;
}
.hp-features-link {
  display: inline-flex; align-items: center; gap: 6px;
  color: var(--eh-purple); font-weight: 700; font-size: 14px;
  text-decoration: none; white-space: nowrap;
}
.hp-features-link:hover { text-decoration: underline; }

.hp-features-grid {
  display: grid; grid-template-columns: repeat(3,1fr); gap: 24px;
}
@media (max-width: 900px) { .hp-features-grid { grid-template-columns: 1fr; } }

.hp-feat-card {
  background: #fff; border: 1px solid var(--border);
  border-radius: 14px; padding: 32px;
  transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
  position: relative; overflow: hidden;
}
.hp-feat-card::before {
  content: ''; position: absolute; inset: 0 0 auto 0; height: 3px;
  background: linear-gradient(90deg, var(--eh-purple), #a78bfa);
  transform: scaleX(0); transform-origin: left;
  transition: transform 0.3s ease;
}
.hp-feat-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(76,45,138,0.10); border-color: #ddd6fe; }
.hp-feat-card:hover::before { transform: scaleX(1); }

.hp-feat-icon {
  width: 48px; height: 48px; border-radius: 12px;
  background: #f4f0ff; color: var(--eh-purple);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 20px;
}
.hp-feat-title {
  font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 700;
  color: var(--svi-navy); margin-bottom: 10px;
}
.hp-feat-body { font-size: 14px; color: var(--muted); line-height: 1.7; }

/* ── CTA BAR ─────────────────────────────────────────────────── */
.hp-cta-bar {
  border-top: 1px solid var(--border);
  padding: 64px 32px;
  text-align: center;
  background: #fafbff;
}
.hp-cta-bar h2 {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(24px, 4vw, 38px); font-weight: 800;
  color: var(--svi-navy); margin-bottom: 14px; letter-spacing: -0.02em;
}
.hp-cta-bar p { font-size: 16px; color: var(--muted); margin-bottom: 32px; }
</style>

<div class="hp-wrap">

<!-- ── HERO ───────────────────────────────────────────────────── -->
<section class="hp-hero">

  <!-- Left -->
  <div>
    <div class="hp-trust-badge">
      <span class="trust-star">★</span>
      <span class="trust-text">4.7 on TrustPilot</span>
    </div>

    <h1>Verify your<br>security status<br>instantly</h1>

    <p>Powered by Roqsafe SVI — check your IP reputation, request unblocks, monitor network health and more in real time.</p>

    <div class="hp-cta-row">
      <a href="/Verifi/data/user/default" class="hp-btn-primary">
        <i data-lucide="shield-check" style="width:17px;height:17px;"></i>
        Check My Status
      </a>
      <a href="/About" class="hp-btn-secondary">
        <i data-lucide="headphones" style="width:17px;height:17px;"></i>
        Contact Support
      </a>
    </div>
  </div>

  <!-- Right — floating UI mockup -->
  <div class="hp-hero-right">
    <!-- IP card -->
    <div class="hp-float-card hp-card-ip">
      <div class="ip-label">Your IP Address</div>
      <div class="ip-addr">185.220.101.x</div>
      <span class="ip-status">Not Blocked</span>
    </div>
    <!-- Alert card -->
    <div class="hp-float-card hp-card-alert">
      <div class="alert-title">⚠ Activity Detected</div>
      <div class="alert-body">Suspicious request from<br>unknown origin flagged.</div>
    </div>
    <!-- Stat card -->
    <div class="hp-float-card hp-card-stat">
      <div class="stat-num">99.9%</div>
      <div class="stat-lbl">Uptime SLA</div>
    </div>
    <!-- Shield card -->
    <div class="hp-float-card hp-card-shield">
      <div class="shield-icon">
        <i data-lucide="shield" style="width:20px;height:20px;"></i>
      </div>
      <div>
        <div class="shield-text-title">Roqsafe SVI Active</div>
        <div class="shield-text-sub">All systems operational</div>
      </div>
    </div>
  </div>

</section>

<hr class="hp-divider">

<!-- ── STATS ──────────────────────────────────────────────────── -->
<div class="hp-stats-section">
  <div class="hp-stats-eyebrow">What the numbers say</div>
  <div class="hp-stats-headline">Don't take our word for it.</div>

  <div class="hp-stats-row">
    <div class="hp-stat-cell">
      <div class="hp-stat-num"><span>2.4M</span>+</div>
      <div class="hp-stat-label">IP checks processed</div>
    </div>
    <div class="hp-stat-cell">
      <div class="hp-stat-num">99.9<span>%</span></div>
      <div class="hp-stat-label">Platform uptime SLA</div>
    </div>
    <div class="hp-stat-cell">
      <div class="hp-stat-num"><span>&lt;50</span>ms</div>
      <div class="hp-stat-label">Average response time</div>
    </div>
    <div class="hp-stat-cell">
      <div class="hp-stat-num"><span>24</span>/7</div>
      <div class="hp-stat-label">Active monitoring</div>
    </div>
  </div>
</div>

<!-- ── FEATURES ───────────────────────────────────────────────── -->
<div style="border-top: 1px solid var(--border); background: #fafbff;">
<div class="hp-features-section">
  <div class="hp-features-top">
    <div>
      <div class="hp-features-section-label">Platform Capabilities</div>
      <div class="hp-features-section-title">Why Organizations Choose Roqsafe SVI</div>
    </div>
    <a href="/About" class="hp-features-link">
      Learn more <i data-lucide="arrow-right" style="width:15px;height:15px;"></i>
    </a>
  </div>

  <div class="hp-features-grid">
    <div class="hp-feat-card">
      <div class="hp-feat-icon">
        <i data-lucide="shield-check" style="width:24px;height:24px;"></i>
      </div>
      <div class="hp-feat-title">IP Verification</div>
      <div class="hp-feat-body">Real-time IP reputation checks with instant block detection. Know exactly where your connection stands in our global threat database.</div>
    </div>
    <div class="hp-feat-card">
      <div class="hp-feat-icon">
        <i data-lucide="lock-open" style="width:24px;height:24px;"></i>
      </div>
      <div class="hp-feat-title">Deblock Requests</div>
      <div class="hp-feat-body">Submit formal unblock requests directly through the platform. Our security team reviews and processes them with full auditability.</div>
    </div>
    <div class="hp-feat-card">
      <div class="hp-feat-icon">
        <i data-lucide="gauge" style="width:24px;height:24px;"></i>
      </div>
      <div class="hp-feat-title">Network Monitor</div>
      <div class="hp-feat-body">Run instant speed tests powered by Cloudflare's edge network and track latency, jitter, and throughput over time.</div>
    </div>
  </div>
</div>
</div>

<!-- ── CTA BAR ────────────────────────────────────────────────── -->
<div class="hp-cta-bar">
  <h2>Start verifying your connection today.</h2>
  <p>No account required — just enter your IP and get an instant security report.</p>
  <div class="hp-cta-row" style="justify-content:center;">
    <a href="/Verifi/data/user/default" class="hp-btn-primary">
      <i data-lucide="shield-check" style="width:17px;height:17px;"></i>
      Check My IP Status
    </a>
    <a href="/myspeed" class="hp-btn-secondary">
      <i data-lucide="gauge" style="width:17px;height:17px;"></i>
      Run Speed Test
    </a>
  </div>
</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>

<?php include_once __DIR__ . '/../components/public_footer.php'; ?>
