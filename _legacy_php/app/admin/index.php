<?php
// ── admin/index.php — Dashboard ───────────────────────────────

svi_require_auth();

$db = svi_db();

// ── Stats ──────────────────────────────────────────────────────
$stats = [];
$rows = $db->query("
  SELECT
    COUNT(*) AS total,
    SUM(block_type = 'soft')      AS soft,
    SUM(block_type = 'permanent') AS perm,
    SUM(deblock_req = 1 AND deblocked = 0 AND block_type = 'soft') AS pending,
    SUM(deblocked = 1)            AS restored
  FROM svi_blocked_ips
")->fetch();

$loginToday = $db->query("SELECT COUNT(*) FROM svi_login_log WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$loginFails = $db->query("SELECT COUNT(*) FROM svi_login_log WHERE DATE(created_at) = CURDATE() AND success = 0")->fetchColumn();
$checksToday = $db->query("SELECT COUNT(*) FROM svi_check_log WHERE DATE(created_at) = CURDATE()")->fetchColumn();

// ── Recent blocks ──────────────────────────────────────────────
$recentBlocks = $db->query("
  SELECT ip, block_type, reason, deblock_req, deblocked, created_at
  FROM svi_blocked_ips
  ORDER BY created_at DESC
  LIMIT 8
")->fetchAll();

// ── Recent login events ────────────────────────────────────────
$recentLogins = $db->query("
  SELECT username, ip, success, created_at
  FROM svi_login_log
  ORDER BY created_at DESC
  LIMIT 6
")->fetchAll();

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
include __DIR__ . '/../../components/_admin_head.php';
?>
</head>
<body>
<?php include __DIR__ . '/../../components/_admin_nav.php'; ?>

<!-- ── Dashboard content ──────────────────────────────────────── -->
<div class="page-heading">
  <h1>Dashboard</h1>
  <p>Security overview — <?= date('l, d F Y') ?></p>
</div>

<!-- Stat cards -->
<div class="stat-grid">
  <div class="stat-card">
    <span class="stat-label">Total Blocked</span>
    <span class="stat-value"><?= (int)$rows['total'] ?></span>
  </div>
  <div class="stat-card soft">
    <span class="stat-label">Soft Blocks</span>
    <span class="stat-value"><?= (int)$rows['soft'] ?></span>
  </div>
  <div class="stat-card perm">
    <span class="stat-label">Permanent</span>
    <span class="stat-value"><?= (int)$rows['perm'] ?></span>
  </div>
  <div class="stat-card info">
    <span class="stat-label">Deblock Pending</span>
    <span class="stat-value"><?= (int)$rows['pending'] ?></span>
  </div>
  <div class="stat-card ok">
    <span class="stat-label">Restored</span>
    <span class="stat-value"><?= (int)$rows['restored'] ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Checks Today</span>
    <span class="stat-value"><?= (int)$checksToday ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Logins Today</span>
    <span class="stat-value"><?= (int)$loginToday ?></span>
  </div>
  <div class="stat-card perm">
    <span class="stat-label">Failed Logins</span>
    <span class="stat-value"><?= (int)$loginFails ?></span>
  </div>
</div>

<!-- Quick actions -->
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:28px;">
  <a class="btn btn-primary" href="blocks.php">🚫 Manage Blocks</a>
  <a class="btn btn-warn"    href="blocks.php?filter=pending">⏳ Review Pending (<?= (int)$rows['pending'] ?>)</a>
  <a class="btn btn-ghost"   href="settings.php">⚙️ Site Settings</a>
  <a class="btn btn-ghost"   href="content.php">✏️ Edit Content</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;flex-wrap:wrap;">

  <!-- Recent blocks -->
  <div class="card" style="min-width:0">
    <div class="card-title">Recent Blocked IPs</div>
    <div class="tbl-wrap">
      <table class="svi-table">
        <thead>
          <tr>
            <th>IP Address</th>
            <th>Type</th>
            <th>Status</th>
            <th>Added</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentBlocks as $b): ?>
          <tr>
            <td style="font-family:monospace;font-size:13px"><?= htmlspecialchars($b['ip'], ENT_QUOTES) ?></td>
            <td>
              <span class="badge badge-<?= $b['block_type'] === 'permanent' ? 'perm' : 'soft' ?>">
                <?= $b['block_type'] ?>
              </span>
            </td>
            <td>
              <?php if ($b['deblocked']): ?>
                <span class="badge badge-ok">Restored</span>
              <?php elseif ($b['deblock_req']): ?>
                <span class="badge badge-info">Pending</span>
              <?php else: ?>
                <span class="badge badge-muted">Active</span>
              <?php endif; ?>
            </td>
            <td style="font-size:12px;color:var(--muted)"><?= date('d M H:i', strtotime($b['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recentBlocks)): ?>
            <tr><td colspan="4" style="color:var(--muted);text-align:center">No blocked IPs yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div style="margin-top:14px"><a class="btn btn-ghost btn-sm" href="blocks.php">View all →</a></div>
  </div>

  <!-- Recent login events -->
  <div class="card" style="min-width:0">
    <div class="card-title">Recent Login Events</div>
    <div class="tbl-wrap">
      <table class="svi-table">
        <thead>
          <tr>
            <th>User</th>
            <th>IP</th>
            <th>Result</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentLogins as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l['username'], ENT_QUOTES) ?></td>
            <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($l['ip'], ENT_QUOTES) ?></td>
            <td>
              <?php if ($l['success']): ?>
                <span class="badge badge-ok">✓ OK</span>
              <?php else: ?>
                <span class="badge badge-perm">✗ Fail</span>
              <?php endif; ?>
            </td>
            <td style="font-size:12px;color:var(--muted)"><?= date('d M H:i', strtotime($l['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recentLogins)): ?>
            <tr><td colspan="4" style="color:var(--muted);text-align:center">No login events yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div style="margin-top:14px"><a class="btn btn-ghost btn-sm" href="login_log.php">View all →</a></div>
  </div>

</div>

  </main>
</div><!-- .admin-layout -->
</body>
</html>
