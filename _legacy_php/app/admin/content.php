<?php
// ── admin/content.php — Page Text Editor ─────────────────────

svi_require_auth();

$db      = svi_db();
$message = '';
$msgType = 'ok';
$csrf    = svi_csrf_token();

// ── Handle save ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && svi_verify_csrf($_POST['csrf'] ?? '')) {
    $stmt = $db->prepare("UPDATE svi_content SET value=? WHERE page=? AND `key`=?");
    $items = $db->query("SELECT page, `key` FROM svi_content")->fetchAll();

    foreach ($items as $row) {
        $fieldName = $row['page'] . '__' . $row['key'];
        if (isset($_POST[$fieldName])) {
            $stmt->execute([trim($_POST[$fieldName]), $row['page'], $row['key']]);
        }
    }
    $message = 'Content saved successfully.';
}

// ── Load all content grouped by page ──────────────────────────
$rows = $db->query("SELECT * FROM svi_content ORDER BY page, id")->fetchAll();

$groups = [];
foreach ($rows as $r) {
    $groups[$r['page']][] = $r;
}

// Human-readable labels for known keys
$labels = [
    'brand_eh'            => 'Brand: Erickson Holding (HTML)',
    'brand_svi_title'     => 'Brand: SVI Title',
    'brand_svi_sub'       => 'Brand: Subtitle',
    'page_title'          => 'Browser Tab Title',
    'hero_heading'        => 'Hero Heading',
    'hero_emoji'          => 'Hero Emoji',
    'intro_p1'            => 'Intro Paragraph 1',
    'intro_p2'            => 'Intro Paragraph 2',
    'ip_input_placeholder'=> 'IP Input: Placeholder',
    'ip_input_hint'       => 'IP Input: Hint Text',
    'ip_submit_btn'       => 'IP Input: Button Label',
    'section1_title'      => 'Section 1: Title',
    'section1_body'       => 'Section 1: Body',
    'section2_title'      => 'Section 2: Title',
    'section2_body'       => 'Section 2: Body',
    'section3_title'      => 'Section 3: Title',
    'section3_body'       => 'Section 3: Body',
    'section4_title'      => 'Section 4: Title',
    'section4_item1'      => 'Section 4: List Item 1',
    'section4_item2'      => 'Section 4: List Item 2',
    'section4_item3'      => 'Section 4: List Item 3',
    'closing_note'        => 'Closing Note',
    'footer_text'         => 'Footer Text',
    'result_not_found'    => 'IP Result: Not Found',
    'result_soft_blocked' => 'IP Result: Soft Blocked',
    'result_perm_blocked' => 'IP Result: Permanently Blocked',
    'result_deblocked'    => 'IP Result: Already Deblocked',
];

// Multi-line fields (textarea instead of input)
$multiline = ['intro_p1','intro_p2','section1_body','section2_body','section3_body',
              'closing_note','result_not_found','result_soft_blocked','result_perm_blocked',
              'result_deblocked','section4_item1','section4_item2','section4_item3'];

$pageTitle  = 'Page Content Editor';
$activePage = 'content';
include __DIR__ . '/../../components/_admin_head.php';
?>
</head>
<body>
<?php include __DIR__ . '/../../components/_admin_nav.php'; ?>

<div class="page-heading">
  <h1>Page Content Editor</h1>
  <p>All text displayed on the public SVI page. Supports basic HTML.</p>
</div>

<?php if ($message): ?>
  <div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($message, ENT_QUOTES) ?></div>
<?php endif; ?>

<form method="POST">
  <input type="hidden" name="csrf" value="<?= $csrf ?>">

  <?php foreach ($groups as $page => $items): ?>
  <div class="card">
    <div class="card-title" style="text-transform:capitalize">
      Page: <em><?= htmlspecialchars($page, ENT_QUOTES) ?></em>
    </div>

    <?php foreach ($items as $item):
      $fieldName = $item['page'] . '__' . $item['key'];
      $label     = $labels[$item['key']] ?? $item['key'];
      $isMulti   = in_array($item['key'], $multiline);
    ?>
    <div class="form-group">
      <label for="<?= htmlspecialchars($fieldName, ENT_QUOTES) ?>">
        <?= htmlspecialchars($label, ENT_QUOTES) ?>
        <span style="font-size:10px;color:var(--muted);margin-left:6px;font-weight:300">
          [key: <?= htmlspecialchars($item['key'], ENT_QUOTES) ?>]
        </span>
      </label>

      <?php if ($isMulti): ?>
        <textarea class="form-textarea"
                  name="<?= htmlspecialchars($fieldName, ENT_QUOTES) ?>"
                  id="<?= htmlspecialchars($fieldName, ENT_QUOTES) ?>"
                  rows="3"><?= htmlspecialchars($item['value'], ENT_QUOTES) ?></textarea>
      <?php else: ?>
        <input class="form-input" type="text"
               name="<?= htmlspecialchars($fieldName, ENT_QUOTES) ?>"
               id="<?= htmlspecialchars($fieldName, ENT_QUOTES) ?>"
               value="<?= htmlspecialchars($item['value'], ENT_QUOTES) ?>">
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

  </div>
  <?php endforeach; ?>

  <div style="display:flex;gap:12px;align-items:center;margin-bottom:40px">
    <button class="btn btn-primary" type="submit">💾 Save Content</button>
    <a class="btn btn-ghost" href="../index.php" target="_blank">🌐 Preview Public Page ↗</a>
  </div>

</form>

  </main>
</div>
</body>
</html>
