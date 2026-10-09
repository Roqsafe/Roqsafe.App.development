<?php
// ── api/search.php ──────────────────────────────────────────
// SVI – Security & Verification Inspector | Database Search
// Searches svi_content and return page matches.

require_once __DIR__ . '/../lib/env.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';

header('Content-Type: application/json');

$query = trim($_GET['q'] ?? '');

if (empty($query)) {
    echo json_encode([]);
    exit;
}

$db = svi_db();

// Search pages or content keys
$st = $db->prepare("
    SELECT DISTINCT page 
    FROM svi_content 
    WHERE page LIKE ? OR `key` LIKE ? OR `value` LIKE ?
    LIMIT 5
");

$searchParam = "%{$query}%";
$st->execute([$searchParam, $searchParam, $searchParam]);
$results = $st->fetchAll(PDO::FETCH_ASSOC);

$formatted = [];
foreach ($results as $row) {
    $pageName = $row['page'];
    $url = ($pageName === 'main' || $pageName === 'home') ? '/' : '/' . $pageName;
    
    $formatted[] = [
        'title' => ucfirst($pageName) . " Information",
        'url' => $url,
        'description' => "Security details regarding $pageName."
    ];
}

// Special case for project tools
if (stripos('Project Tools IP Verification', $query) !== false) {
    $formatted[] = [
        'title' => "Project Tools",
        'url' => "/Verifi/data/user/default",
        'description' => "IP security and de-blocking tools."
    ];
}

echo json_encode($formatted);
exit;
