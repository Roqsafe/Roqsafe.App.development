<?php
// ── admin/login_log.php — Full Login Audit Log ────────────────

svi_require_auth();

$db = svi_db();

$page    = max(1, (int)($_GET['page'] ?? 1));
$limit   = 30;
$offset  = ($page - 1) * $limit;
$filter  = $_GET['filter'] ?? 'all';

$where  = '1=1';
if ($filter === 'success') $where = 'success=1';
if ($filter === 'fail')    $where = 'success=0';

$total = $db->query("SELECT COUNT(*) FROM svi_login_log WHERE {$where}")->fetchColumn();
$pages = max(1, (int)ceil($total / $limit));

$logs = $db->query("
  SELECT * FROM svi_login_log WHERE {$where}
  ORDER BY created_at DESC
  LIMIT {$limit} OFFSET {$offset}
")->fetchAll();

$pageTitle  = 'Login Audit Log';
$activePage = 'login_log';
include __DIR__ . '/../../components/_admin_head.php';
?>
</head>
<body>
<?php include __DIR__ . '/../../components/_admin_nav.php'; ?>

<div class="page-heading">
  <h1>Login Audit Log</h1>
  <p><?= number_format($total) ?> total events recorded.</p>
</div>

<!-- Filter -->
<div style="display:flex;gap:8px;margin-bottom:16px">
  <?php foreach (['all'=>'All','success'=>'✓ Successful','fail'=>'✗ Failed'] as $k=>$lbl): ?>
    <a class="btn btn-sm <?= $filter===$k?'btn-primary':'btn-ghost' ?>"
       href="login_log.php?filter=<?= $k ?>">
      <?= $lbl ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="card" style="padding:0;overflow:hidden">
  <div class="tbl-wrap">
    <table class="svi-table">
      <thead>
        <tr><th>#</th><th>Time</th><th>Username</th><th>IP</th><th>Result</th><th>User-Agent</th></tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $l): ?>
        <tr>
          <td style="color:var(--muted);font-size:11px"><?= $l['id'] ?></td>
          <td style="font-size:12px;white-space:nowrap"><?= date('d M Y H:i:s', strtotime($l['created_at'])) ?></td>
          <td><?= htmlspecialchars($l['username'], ENT_QUOTES) ?></td>
          <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($l['ip'], ENT_QUOTES) ?></td>
          <td><?= $l['success'] ? '<span class="badge badge-ok">✓ OK</span>' : '<span class="badge badge-perm">✗ Fail</span>' ?></td>
          <td style="font-size:11px;color:var(--muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
            <?= htmlspecialchars(substr($l['user_agent'] ?? '', 0, 80), ENT_QUOTES) ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:28px">No events found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<?php if ($pages > 1): ?>
<div style="display:flex;gap:6px;margin-top:12px;flex-wrap:wrap">
  <?php for($i=1;$i<=$pages;$i++): ?>
    <a class="btn btn-sm <?= $i===$page?'btn-primary':'btn-ghost' ?>"
       href="login_log.php?filter=<?= $filter ?>&page=<?= $i ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>
</div>
<?php endif; ?>

  </main>
</div>
</body>
</html>
