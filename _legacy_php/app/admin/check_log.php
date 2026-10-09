<?php
// ── admin/check_log.php — IP Check Rate-Limit Log ────────────

svi_require_auth();

$db    = svi_db();
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 30;
$offset= ($page - 1) * $limit;

$total = $db->query("SELECT COUNT(*) FROM svi_check_log")->fetchColumn();
$pages = max(1, (int)ceil($total / $limit));

$logs = $db->query("
  SELECT * FROM svi_check_log
  ORDER BY created_at DESC
  LIMIT {$limit} OFFSET {$offset}
")->fetchAll();

// Top checkers today
$topCheckers = $db->query("
  SELECT ip, COUNT(*) AS cnt
  FROM svi_check_log
  WHERE DATE(created_at) = CURDATE()
  GROUP BY ip
  ORDER BY cnt DESC
  LIMIT 5
")->fetchAll();

$pageTitle  = 'IP Check Log';
$activePage = 'check_log';
include __DIR__ . '/../../components/_admin_head.php';
?>
</head>
<body>
<?php include __DIR__ . '/../../components/_admin_nav.php'; ?>

<div class="page-heading">
  <h1>IP Check Log</h1>
  <p>All public-page block-check queries. <?= number_format($total) ?> total records.</p>
</div>

<?php if ($topCheckers): ?>
<div class="card" style="margin-bottom:20px">
  <div class="card-title">Top Checkers Today</div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <?php foreach ($topCheckers as $t): ?>
      <span class="badge badge-info" style="font-size:13px;padding:5px 14px">
        <?= htmlspecialchars($t['ip'], ENT_QUOTES) ?>
        <strong style="margin-left:6px"><?= $t['cnt'] ?>×</strong>
      </span>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="card" style="padding:0;overflow:hidden">
  <div class="tbl-wrap">
    <table class="svi-table">
      <thead>
        <tr><th>#</th><th>Checker IP</th><th>Target IP Queried</th><th>Time</th></tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $l): ?>
        <tr>
          <td style="font-size:11px;color:var(--muted)"><?= $l['id'] ?></td>
          <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($l['ip'], ENT_QUOTES) ?></td>
          <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($l['target_ip'], ENT_QUOTES) ?></td>
          <td style="font-size:12px;color:var(--muted);white-space:nowrap">
            <?= date('d M Y H:i:s', strtotime($l['created_at'])) ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
          <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:28px">No check log entries yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($pages > 1): ?>
<div style="display:flex;gap:6px;margin-top:12px;flex-wrap:wrap">
  <?php for($i=1;$i<=$pages;$i++): ?>
    <a class="btn btn-sm <?= $i===$page?'btn-primary':'btn-ghost' ?>"
       href="check_log.php?page=<?= $i ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>

  </main>
</div>
</body>
</html>
