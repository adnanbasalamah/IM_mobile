<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$search = trim($_GET['q'] ?? '');
$db = getDB();

if ($search === '') {
    $sql = "SELECT item_id, name, item_number, cost_price, unit_price, pack_name
            FROM ospos_items
            WHERE deleted = 0 AND category = 'PASAR SEGAR'
            ORDER BY name";
    $stmt = $db->prepare($sql);
    $stmt->execute();
} else {
    $sql = "SELECT item_id, name, item_number, cost_price, unit_price, pack_name
            FROM ospos_items
            WHERE deleted = 0 AND category = 'PASAR SEGAR'
              AND (name LIKE ? OR item_number LIKE ?)
            ORDER BY name";
    $stmt = $db->prepare($sql);
    $like = '%' . $search . '%';
    $stmt->execute([$like, $like]);
}

$items = [];
while ($row = $stmt->fetch()) {
    $items[] = [
        'item_id' => (int)$row['item_id'],
        'name' => $row['name'],
        'sku' => $row['item_number'],
        'cost_price' => (float)$row['cost_price'],
        'unit_price' => (float)$row['unit_price'],
        'pack_name' => $row['pack_name'],
    ];
}

echo json_encode(['items' => $items, 'total' => count($items)]);