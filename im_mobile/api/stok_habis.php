<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$period = $_GET['period'] ?? '1-week';

$daysMap = [
    '1-week' => 7,
    '2-weeks' => 14,
    '1-month' => 30,
];

$days = $daysMap[$period] ?? 7;

$db = getDB();

$sql = "SELECT i.name, i.item_number, i.pack_name, i.reorder_level,
               iq.quantity,
               COALESCE(s.company_name, 'Tanpa Supplier') AS supplier_nama
        FROM ospos_items i
        JOIN ospos_item_quantities iq ON i.item_id = iq.item_id AND iq.location_id = 1
        LEFT JOIN ospos_suppliers s ON i.supplier_id = s.person_id
        WHERE i.deleted = 0
          AND iq.quantity <= i.reorder_level
          AND i.category = 'PASAR SEGAR'
          AND i.item_id IN (
            SELECT DISTINCT trans_items
            FROM ospos_inventory
            WHERE trans_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
              AND trans_inventory < 0
          )
        ORDER BY supplier_nama, i.name";

$stmt = $db->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll();

$grouped = [];
foreach ($rows as $row) {
    $supplier = $row['supplier_nama'];
    if (!isset($grouped[$supplier])) {
        $grouped[$supplier] = [];
    }
    $grouped[$supplier][] = [
        'name' => $row['name'],
        'sku' => $row['item_number'],
        'pack_name' => $row['pack_name'],
        'quantity' => (float)$row['quantity'],
        'reorder_level' => (float)$row['reorder_level'],
    ];
}

$result = [];
foreach ($grouped as $supplier => $items) {
    $result[] = [
        'supplier' => $supplier,
        'items' => $items,
    ];
}

echo json_encode(['period' => $period, 'days' => $days, 'items' => $result]);