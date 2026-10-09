<?php
// ── app/myspeed/page.php ──────────────────────────────────────────────
$db = svi_db();
$c = svi_content($db, 'main');
$siteNotice = trim(svi_setting($db, 'site_notice', ''));
$pageTitle = 'MySpeed — Internet Speed Test';
require_once __DIR__ . '/../../components/public_header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
/* ── Page layout ───────────────────────────────────────────────── */
.ms-wrap {
  background: #fff;
  padding: 0 0 80px;
}

/* ── Hero bar ──────────────────────────────────────────────────── */
.ms-hero {
  background: #fff;
  border-bottom: 1px solid var(--border);
  padding: 56px 24px 48px;
  text-align: center;
}
.ms-badge {
  display: inline-flex; align-items: center; gap: 7px;
  background: #f4f0ff; border: 1px solid #ddd6fe;
  color: var(--eh-purple); padding: 5px 14px; border-radius: 50px;
  font-size: 11px; font-weight: 700; letter-spacing: 0.09em; text-transform: uppercase;
  margin-bottom: 18px;
}
.ms-hero h1 {
  font-family: 'Outfit', sans-serif;
  font-size: clamp(30px, 5vw, 52px); font-weight: 800;
  color: var(--svi-navy); letter-spacing: -0.03em; margin-bottom: 10px; line-height: 1.1;
}
.ms-hero h1 span { color: var(--eh-purple); }
.ms-hero p { color: var(--muted); font-size: 16px; }

/* ── Speed panel ───────────────────────────────────────────────── */
.ms-panel {
  max-width: 700px; margin: 48px auto 0; padding: 0 24px;
}

/* Gauge */
.ms-gauge-wrap {
  display: flex; flex-direction: column; align-items: center; gap: 6px;
  margin-bottom: 32px;
}
.ms-gauge {
  position: relative; width: 220px; height: 220px;
}
.ms-gauge svg { width: 100%; height: 100%; transform: rotate(-220deg); }
.ms-gauge-track {
  fill: none; stroke: var(--input-bg); stroke-width: 13; stroke-linecap: round;
}
.ms-gauge-fill {
  fill: none; stroke: var(--eh-purple); stroke-width: 13; stroke-linecap: round;
  stroke-dasharray: 534; stroke-dashoffset: 534;
  transition: stroke-dashoffset 0.35s ease, stroke 0.35s ease;
  filter: drop-shadow(0 0 6px rgba(76,45,138,0.4));
}
.ms-gauge-center {
  position: absolute; top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  text-align: center; pointer-events: none;
}
.ms-gauge-val {
  font-family: 'Outfit', sans-serif;
  font-size: 48px; font-weight: 800; color: var(--svi-navy); line-height: 1;
}
.ms-gauge-unit { font-size: 13px; color: var(--muted); font-weight: 500; margin-top: 2px; }
.ms-gauge-lbl  {
  font-size: 11px; color: var(--eh-purple); font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.1em; margin-top: 4px;
}
.ms-phase-label {
  font-size: 13px; color: var(--muted); height: 18px; margin-top: 4px;
}

/* Metrics row */
.ms-metrics {
  display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 24px;
}
.ms-metric {
  background: #fff; border: 1px solid var(--border);
  border-radius: 12px; padding: 18px 14px; text-align: center;
  transition: border-color 0.25s, box-shadow 0.25s;
}
.ms-metric.active {
  border-color: var(--eh-purple);
  box-shadow: 0 0 0 3px rgba(76,45,138,0.08);
}
.ms-metric-icon {
  width: 34px; height: 34px; border-radius: 9px; margin: 0 auto 10px;
  display: flex; align-items: center; justify-content: center;
}
.ms-metric-icon.dl { background: #f4f0ff; color: var(--eh-purple); }
.ms-metric-icon.ul { background: #ecfdf5; color: #059669; }
.ms-metric-icon.pi { background: #eff6ff; color: #2563eb; }
.ms-metric-val {
  font-family: 'Outfit', sans-serif;
  font-size: 26px; font-weight: 800; color: var(--svi-navy); line-height: 1;
}
.ms-metric-unit { font-size: 10px; color: var(--muted); font-weight: 600; margin-top: 2px; letter-spacing: 0.04em; }
.ms-metric-label { font-size: 10px; color: var(--muted); margin-top: 5px; text-transform: uppercase; letter-spacing: 0.07em; }

/* Progress */
.ms-progress-wrap { margin-bottom: 24px; }
.ms-progress-bar {
  width: 100%; height: 3px;
  background: var(--input-bg); border-radius: 4px; overflow: hidden;
}
.ms-progress-fill {
  height: 100%; width: 0%;
  background: linear-gradient(90deg, var(--eh-purple), #a78bfa);
  border-radius: 4px; transition: width 0.4s ease;
}
.ms-progress-labels {
  display: flex; justify-content: space-between;
  font-size: 11px; color: var(--muted); margin-top: 8px;
}
.ms-progress-step { transition: color 0.3s; }
.ms-progress-step.done   { color: var(--eh-purple); }
.ms-progress-step.active { color: var(--svi-navy); font-weight: 700; }

/* Start button */
.ms-start-btn {
  width: 100%; padding: 16px;
  background: var(--eh-purple); color: #fff;
  border: none; border-radius: 10px;
  font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 700;
  cursor: pointer; transition: all 0.25s;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  box-shadow: 0 6px 24px rgba(76,45,138,0.3);
}
.ms-start-btn:hover:not(:disabled) {
  background: #3b236b;
  transform: translateY(-2px);
  box-shadow: 0 10px 32px rgba(76,45,138,0.45);
}
.ms-start-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

/* Results */
.ms-results {
  display: none;
  background: transparent; border: none;
  padding: 32px 0 0 0; margin-top: 24px;
}
.ms-results.show { display: flex; flex-direction: column; gap: 32px; align-items: center; }
.ms-result-item { text-align: center; }
.ms-result-val { font-family: 'Outfit', sans-serif; font-size: 48px; font-weight: 800; line-height: 1; color: var(--svi-navy); }
.ms-result-label { font-size: 13px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.12em; margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 6px; }
.ms-rating {
  text-align: center; margin-top: 8px;
  font-size: 17px; font-weight: 700; color: var(--svi-navy);
}
</style>

<div class="ms-wrap">

  <!-- Hero -->
  <div class="ms-hero">
    <div class="ms-badge">
      <i data-lucide="gauge" style="width:12px;height:12px;"></i>
      Network Intelligence
    </div>
    <h1>Test Your <span>Internet</span> Speed</h1>
    <p>Powered by Cloudflare's global edge network — accurate download, upload & ping in seconds.</p>
  </div>

  <!-- Speed panel -->
  <div class="ms-panel">

    <!-- Gauge -->
    <div class="ms-gauge-wrap">
      <div class="ms-gauge">
        <svg viewBox="0 0 200 200">
          <circle class="ms-gauge-track" cx="100" cy="100" r="85"
            stroke-dasharray="534" stroke-dashoffset="93" />
          <circle class="ms-gauge-fill" id="gauge-fill" cx="100" cy="100" r="85" />
        </svg>
        <div class="ms-gauge-center">
          <div class="ms-gauge-val" id="gauge-val">–</div>
          <div class="ms-gauge-unit">Mbps</div>
          <div class="ms-gauge-lbl" id="gauge-lbl">Ready</div>
        </div>
      </div>
      <div class="ms-phase-label" id="phase-label">Press "Start Test" to measure your connection</div>
    </div>

    <!-- Metrics -->
    <div class="ms-metrics">
      <div class="ms-metric" id="m-dl">
        <div class="ms-metric-icon dl"><i data-lucide="arrow-down" style="width:17px;height:17px;"></i></div>
        <div class="ms-metric-val" id="dl-val">–</div>
        <div class="ms-metric-unit">Mbps</div>
        <div class="ms-metric-label">Download</div>
      </div>
      <div class="ms-metric" id="m-ul">
        <div class="ms-metric-icon ul"><i data-lucide="arrow-up" style="width:17px;height:17px;"></i></div>
        <div class="ms-metric-val" id="ul-val">–</div>
        <div class="ms-metric-unit">Mbps</div>
        <div class="ms-metric-label">Upload</div>
      </div>
      <div class="ms-metric" id="m-ping">
        <div class="ms-metric-icon pi"><i data-lucide="activity" style="width:17px;height:17px;"></i></div>
        <div class="ms-metric-val" id="ping-val">–</div>
        <div class="ms-metric-unit">ms</div>
        <div class="ms-metric-label">Ping</div>
      </div>
    </div>

    <!-- Progress -->
    <div class="ms-progress-wrap">
      <div class="ms-progress-bar">
        <div class="ms-progress-fill" id="prog-fill"></div>
      </div>
      <div class="ms-progress-labels">
        <span class="ms-progress-step" id="st-ping">Ping</span>
        <span class="ms-progress-step" id="st-dl">Download</span>
        <span class="ms-progress-step" id="st-ul">Upload</span>
        <span class="ms-progress-step" id="st-done">Complete</span>
      </div>
    </div>

    <!-- Button -->
    <button class="ms-start-btn" id="start-btn" onclick="startTest()">
      <i data-lucide="play" style="width:19px;height:19px;" id="btn-icon"></i>
      <span id="btn-text">Start Test</span>
    </button>

    <!-- Results -->
    <div class="ms-results" id="results-box">
      <div class="ms-result-item">
        <div class="ms-result-val" id="r-dl">–</div>
        <div class="ms-result-label"><span>⬇</span> DOWNLOAD MBPS</div>
      </div>
      <div class="ms-result-item">
        <div class="ms-result-val" id="r-ul">–</div>
        <div class="ms-result-label"><span>⬆</span> UPLOAD MBPS</div>
      </div>
      <div class="ms-result-item">
        <div class="ms-result-val" id="r-ping">–</div>
        <div class="ms-result-label"><span>🏓</span> PING MS</div>
      </div>
      <div class="ms-result-item">
        <div class="ms-result-val" id="r-jitter">–</div>
        <div class="ms-result-label"><span>〰</span> JITTER MS</div>
      </div>
      <div class="ms-rating" id="rating-box"></div>
    </div>
  </div>

</div>

<script>
let testRunning = false;
const GAUGE_MAX = 200, GAUGE_ARC = 440;

function setGauge(mbps, color) {
  const pct = Math.min(mbps / GAUGE_MAX, 1);
  const offset = 534 - pct * GAUGE_ARC;
  const fill = document.getElementById('gauge-fill');
  fill.style.strokeDashoffset = offset;
  fill.style.stroke = color || 'var(--eh-purple)';
  document.getElementById('gauge-val').textContent = mbps > 0 ? mbps.toFixed(1) : '–';
}

function setStep(step) {
  const order = ['ping','dl','ul','done'];
  const curIdx = order.indexOf(step);
  const progress = { ping: 25, dl: 75, ul: 95, done: 100 };
  document.getElementById('prog-fill').style.width = (progress[step] || 0) + '%';
  order.forEach((s, i) => {
    const el = document.getElementById('st-' + s);
    el.className = 'ms-progress-step' + (i < curIdx ? ' done' : i === curIdx ? ' active' : '');
  });
}

function activateMetric(id) {
  document.querySelectorAll('.ms-metric').forEach(m => m.classList.remove('active'));
  if (id) document.getElementById(id).classList.add('active');
}

async function measurePing(n = 8) {
  const pings = [];
  for (let i = 0; i < n; i++) {
    const t0 = performance.now();
    try { await fetch('https://1.1.1.1/cdn-cgi/trace?_=' + Date.now(), { method:'GET', mode:'no-cors', cache:'no-store' }); } catch(e) {}
    pings.push(performance.now() - t0);
    await sleep(80);
  }
  pings.sort((a,b)=>a-b);
  const t = pings.slice(1,-1);
  return { ping: Math.round(t.reduce((a,b)=>a+b,0)/t.length), jitter: Math.round(Math.max(...t) - Math.min(...t)) };
}

async function measureDownload() {
  const jobs = [
    { url: 'https://speed.cloudflare.com/__down?bytes=1000000',  bytes: 1e6  },
    { url: 'https://speed.cloudflare.com/__down?bytes=5000000',  bytes: 5e6  },
    { url: 'https://speed.cloudflare.com/__down?bytes=10000000', bytes: 10e6 },
  ];
  const speeds = [];
  for (const { url, bytes } of jobs) {
    try {
      const t0 = performance.now();
      await (await fetch(url + '&_=' + Date.now(), { cache:'no-store' })).arrayBuffer();
      const mbps = (bytes * 8) / ((performance.now() - t0) / 1000) / 1e6;
      speeds.push(mbps);
      setGauge(mbps, 'var(--eh-purple)');
      document.getElementById('dl-val').textContent = mbps.toFixed(1);
    } catch(e) { speeds.push(0); }
  }
  speeds.sort((a,b)=>a-b);
  return speeds[speeds.length - 1];
}

async function measureUpload() {
  const sizes = [500000, 2000000, 5000000];
  const speeds = [];
  for (const size of sizes) {
    try {
      const blob = new Blob([new Uint8Array(size)]);
      const t0 = performance.now();
      await fetch('https://speed.cloudflare.com/__up', { method:'POST', body:blob, cache:'no-store' });
      const mbps = (size * 8) / ((performance.now() - t0) / 1000) / 1e6;
      speeds.push(mbps);
      setGauge(mbps, '#059669');
      document.getElementById('ul-val').textContent = mbps.toFixed(1);
    } catch(e) { speeds.push(0); }
  }
  speeds.sort((a,b)=>a-b);
  return speeds[speeds.length - 1];
}

function getRating(dl, ul, ping) {
  if (dl > 100 && ul > 50 && ping < 20) return '🚀 Excellent — Ultra-fast connection';
  if (dl > 50  && ul > 20 && ping < 50) return '✅ Great — Fast and reliable';
  if (dl > 20  && ul > 10 && ping < 80) return '👍 Good — Suitable for most tasks';
  if (dl > 5   && ul > 3)               return '⚡ Fair — Fine for everyday browsing';
  return '⚠️ Slow — May struggle with HD streaming';
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

async function startTest() {
  if (testRunning) return;
  testRunning = true;
  const btn = document.getElementById('start-btn');
  btn.disabled = true;
  document.getElementById('btn-text').textContent = 'Testing…';
  document.getElementById('results-box').classList.remove('show');
  ['dl-val','ul-val','ping-val'].forEach(id => document.getElementById(id).textContent = '–');
  setGauge(0); setStep('ping');

  // Ping
  activateMetric('m-ping');
  document.getElementById('phase-label').textContent = 'Measuring latency…';
  document.getElementById('gauge-lbl').textContent = 'Ping';
  const { ping, jitter } = await measurePing();
  document.getElementById('ping-val').textContent = ping;
  document.getElementById('gauge-val').textContent = ping;
  document.getElementById('gauge-lbl').textContent = 'ms';
  setGauge(0); await sleep(350);

  // Download
  setStep('dl'); activateMetric('m-dl');
  document.getElementById('phase-label').textContent = 'Measuring download speed…';
  document.getElementById('gauge-lbl').textContent = 'Download';
  const dl = await measureDownload();
  document.getElementById('dl-val').textContent = dl.toFixed(1);
  await sleep(350);

  // Upload
  setStep('ul'); activateMetric('m-ul');
  document.getElementById('phase-label').textContent = 'Measuring upload speed…';
  document.getElementById('gauge-lbl').textContent = 'Upload';
  setGauge(0, '#059669');
  const ul = await measureUpload();
  document.getElementById('ul-val').textContent = ul.toFixed(1);
  await sleep(350);

  // Done
  setStep('done'); activateMetric(null);
  setGauge(dl, 'var(--eh-purple)');
  document.getElementById('gauge-lbl').textContent = 'Done ✓';
  document.getElementById('phase-label').textContent = 'Test complete!';

  document.getElementById('r-dl').textContent     = dl.toFixed(1);
  document.getElementById('r-ul').textContent     = ul.toFixed(1);
  document.getElementById('r-ping').textContent   = ping;
  document.getElementById('r-jitter').textContent = jitter;
  document.getElementById('rating-box').textContent = getRating(dl, ul, ping);
  document.getElementById('results-box').classList.add('show');

  btn.disabled = false;
  document.getElementById('btn-text').textContent = 'Test Again';
  testRunning = false;
}

document.addEventListener('DOMContentLoaded', () => {
  if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>

<?php include_once __DIR__ . '/../../components/public_footer.php'; ?>
