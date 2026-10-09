<?php
// ── admin/blocks.php — Blocked IP management ──────────────────

svi_require_auth();

$db      = svi_db();
$message = '';
$msgType = 'ok';
$csrf    = svi_csrf_token();

// ── Handle form actions ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token  = $_POST['csrf'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!svi_verify_csrf($token)) {
        $message = 'Invalid CSRF token. Please reload and try again.';
        $msgType = 'danger';
    } else {
        switch ($action) {

            case 'add':
                $ip        = trim($_POST['ip'] ?? '');
                $blockType = $_POST['block_type'] === 'permanent' ? 'permanent' : 'soft';
                $reason    = trim($_POST['reason'] ?? '');
                if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                    $message = 'Invalid IP address.'; $msgType = 'danger';
                } else {
                    try {
                        $db->prepare("INSERT INTO svi_blocked_ips (ip, block_type, reason) VALUES (?,?,?) ON DUPLICATE KEY UPDATE block_type=VALUES(block_type), reason=VALUES(reason), deblocked=0, deblock_req=0")
                           ->execute([$ip, $blockType, $reason]);
                        $message = "IP {$ip} has been blocked ({$blockType}).";
                    } catch (Exception $e) {
                        $message = 'Error saving block: ' . $e->getMessage(); $msgType = 'danger';
                    }
                }
                break;

            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                $db->prepare("DELETE FROM svi_blocked_ips WHERE id=?")->execute([$id]);
                $message = 'Block removed.';
                break;

            case 'change_type':
                $id   = (int)($_POST['id'] ?? 0);
                $type = $_POST['block_type'] === 'permanent' ? 'permanent' : 'soft';
                $db->prepare("UPDATE svi_blocked_ips SET block_type=?, deblocked=0, deblock_req=0 WHERE id=?")->execute([$type, $id]);
                $message = 'Block type updated.';
                break;

            case 'approve_deblock':
                $id = (int)($_POST['id'] ?? 0);
                $db->prepare("UPDATE svi_blocked_ips SET deblocked=1, deblock_req=0 WHERE id=? AND block_type='soft'")->execute([$id]);
                $message = 'IP deblocked and access restored.';
                break;

            case 'deny_deblock':
                $id = (int)($_POST['id'] ?? 0);
                $db->prepare("UPDATE svi_blocked_ips SET deblock_req=0 WHERE id=?")->execute([$id]);
                $message = 'Deblock request denied.';
                break;

            case 'upgrade_permanent':
                $id = (int)($_POST['id'] ?? 0);
                $db->prepare("UPDATE svi_blocked_ips SET block_type='permanent', deblock_req=0, deblocked=0 WHERE id=?")->execute([$id]);
                $message = 'Block upgraded to permanent.'; $msgType = 'warn';
                break;
        }
    }
}

// ── Filters ────────────────────────────────────────────────────
$filter  = $_GET['filter'] ?? 'all';
$search  = trim($_GET['q'] ?? '');
$where   = ['1=1'];
$params  = [];

if ($filter === 'soft')     { $where[] = "block_type='soft'"; }
if ($filter === 'perm')     { $where[] = "block_type='permanent'"; }
if ($filter === 'pending')  { $where[] = "deblock_req=1 AND deblocked=0"; }
if ($filter === 'restored') { $where[] = "deblocked=1"; }
if ($search !== '') {
    $where[] = "ip LIKE ?";
    $params[] = "%{$search}%";
}

$whereSQL = implode(' AND ', $where);

$ips = $db->prepare("SELECT * FROM svi_blocked_ips WHERE {$whereSQL} ORDER BY created_at DESC");
$ips->execute($params);
$ips = $ips->fetchAll();

$totalAll     = $db->query("SELECT COUNT(*) FROM svi_blocked_ips")->fetchColumn();
$totalSoft    = $db->query("SELECT COUNT(*) FROM svi_blocked_ips WHERE block_type='soft'")->fetchColumn();
$totalPerm    = $db->query("SELECT COUNT(*) FROM svi_blocked_ips WHERE block_type='permanent'")->fetchColumn();
$totalPending = $db->query("SELECT COUNT(*) FROM svi_blocked_ips WHERE deblock_req=1 AND deblocked=0")->fetchColumn();

$pageTitle  = 'Blocked IPs';
$activePage = 'blocks';
include __DIR__ . '/../../components/_admin_head.php';
?>
</head>
<body>
<?php include __DIR__ . '/../../components/_admin_nav.php'; ?>

<div class="page-heading">
  <h1>Blocked IPs</h1>
  <p>Add, edit, and review all blocked addresses.</p>
</div>

<?php if ($message): ?>
  <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message, ENT_QUOTES) ?></div>
<?php endif; ?>

<!-- ── Add new block ──────────────────────────────────────────── -->
<div class="card">
  <div class="card-title">Add / Update Block</div>
  <form method="POST" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
    <input type="hidden" name="action" value="add">
    <div class="form-group" style="flex:1;min-width:180px;margin:0">
      <label>IP Address</label>
      <input class="form-input mono" type="text" name="ip" placeholder="e.g. 1.2.3.4 or 2001:db8::1" required>
    </div>
    <div class="form-group" style="min-width:140px;margin:0">
      <label>Block Type</label>
      <select class="form-select" name="block_type">
        <option value="soft">Soft</option>
        <option value="permanent">Permanent</option>
      </select>
    </div>
    <div class="form-group" style="flex:2;min-width:200px;margin:0">
      <label>Reason (optional)</label>
      <input class="form-input" type="text" name="reason" placeholder="Reason for block">
    </div>
    <button class="btn btn-primary" type="submit">Add Block</button>
  </form>
</div>

<!-- ── Filter bar ─────────────────────────────────────────────── -->
<div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:16px;">
  <div style="display:flex;gap:6px;flex-wrap:wrap">
    <?php
    $filters = [
      'all'     => "All ({$totalAll})",
      'soft'    => "Soft ({$totalSoft})",
      'perm'    => "Permanent ({$totalPerm})",
      'pending' => "Pending ({$totalPending})",
      'restored'=> "Restored",
    ];
    foreach ($filters as $k => $label):
      $active = $filter === $k ? 'btn-primary' : 'btn-ghost';
    ?>
      <a class="btn btn-sm <?= $active ?>"
         href="blocks.php?filter=<?= $k ?><?= $search ? '&q='.urlencode($search) : '' ?>">
        <?= $label ?>
      </a>
    <?php endforeach; ?>
  </div>
  <form method="GET" style="margin-left:auto;display:flex;gap:6px">
    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter, ENT_QUOTES) ?>">
    <input class="form-input mono" style="width:200px;padding:7px 12px"
           type="text" name="q" placeholder="Search IP…"
           value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
    <button class="btn btn-ghost btn-sm" type="submit">Search</button>
    <?php if ($search): ?><a class="btn btn-ghost btn-sm" href="blocks.php?filter=<?= $filter ?>">✕</a><?php endif; ?>
  </form>
</div>

<!-- ── IP table ───────────────────────────────────────────────── -->
<div class="card" style="padding:0;overflow:hidden">
  <div class="tbl-wrap">
    <table class="svi-table">
      <thead>
        <tr>
          <th>#</th>
          <th>IP Address</th>
          <th>Type</th>
          <th>Status</th>
          <th>Reason</th>
          <th>Added</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ips as $row): ?>
        <tr>
          <td style="color:var(--muted);font-size:12px"><?= $row['id'] ?></td>
          <td style="font-family:monospace;font-size:13px"><?= htmlspecialchars($row['ip'], ENT_QUOTES) ?></td>
          <td>
            <span class="badge badge-<?= $row['block_type'] === 'permanent' ? 'perm' : 'soft' ?>">
              <?= $row['block_type'] ?>
            </span>
          </td>
          <td>
            <?php if ($row['deblocked']): ?>
              <span class="badge badge-ok">Restored</span>
            <?php elseif ($row['deblock_req']): ?>
              <span class="badge badge-info">Deblock Requested</span>
            <?php else: ?>
              <span class="badge badge-muted">Active Block</span>
            <?php endif; ?>
          </td>
          <td style="font-size:13px;color:var(--muted);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            <?= htmlspecialchars($row['reason'] ?? '—', ENT_QUOTES) ?>
          </td>
          <td style="font-size:12px;color:var(--muted);white-space:nowrap">
            <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
          </td>
          <td style="white-space:nowrap">
            <div style="display:flex;gap:5px;flex-wrap:wrap">

              <?php if ($row['deblock_req'] && !$row['deblocked'] && $row['block_type'] === 'soft'): ?>
                <!-- Approve deblock -->
                <form method="POST" style="display:inline">
                  <input type="hidden" name="csrf" value="<?= $csrf ?>">
                  <input type="hidden" name="action" value="approve_deblock">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <button class="btn btn-ok btn-sm" type="submit">✓ Approve</button>
                </form>
                <!-- Deny deblock -->
                <form method="POST" style="display:inline">
                  <input type="hidden" name="csrf" value="<?= $csrf ?>">
                  <input type="hidden" name="action" value="deny_deblock">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <button class="btn btn-warn btn-sm" type="submit">✗ Deny</button>
                </form>
              <?php endif; ?>

              <?php if ($row['block_type'] === 'soft' && !$row['deblocked']): ?>
                <!-- Upgrade to permanent -->
                <form method="POST" style="display:inline"
                      onsubmit="return confirm('Make this block permanent? This cannot be undone.')">
                  <input type="hidden" name="csrf" value="<?= $csrf ?>">
                  <input type="hidden" name="action" value="upgrade_permanent">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <button class="btn btn-danger btn-sm" type="submit">🔒 Permanent</button>
                </form>
              <?php endif; ?>

              <?php if (!$row['deblocked']): ?>
                <!-- Change type -->
                <form method="POST" style="display:inline">
                  <input type="hidden" name="csrf" value="<?= $csrf ?>">
                  <input type="hidden" name="action" value="change_type">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <select name="block_type" class="form-select" style="padding:4px 8px;font-size:12px;width:auto;display:inline-block">
                    <option value="soft"      <?= $row['block_type']==='soft'      ? 'selected':'' ?>>Soft</option>
                    <option value="permanent" <?= $row['block_type']==='permanent' ? 'selected':'' ?>>Permanent</option>
                  </select>
                  <button class="btn btn-ghost btn-sm" type="submit">Save</button>
                </form>
              <?php endif; ?>

              <!-- Delete -->
              <form method="POST" style="display:inline"
                    onsubmit="return confirm('Permanently delete this block record?')">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button class="btn btn-danger btn-sm" type="submit">🗑</button>
              </form>

            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($ips)): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:28px">No blocked IPs found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

  </main>
</div>
</body>
</html>
