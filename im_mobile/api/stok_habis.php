<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$period = $_GET['period'] ?? 'none';

$daysMap = [
    '1-week' => 7,
    '2-weeks' => 14,
    '1-month' => 30,
];

if ($period === 'none' || !isset($daysMap[$period])) {
    echo json_encode(['period' => $period, 'days' => 0, 'items' => []]);
    exit;
}

$days = $daysMap[$period];
$db = getDB();

$sql = "SELECT i.name, i.item_number, i.pack_name, i.reorder_level,
               iq.quantity AS current_quantity,
               (iq.quantity - COALESCE(inv_period.total_change, 0)) AS past_quantity,
               (iq.quantity - COALESCE(inv_today.total_change, 0)) AS yesterday_quantity,
               COALESCE(s.company_name, 'Tanpa Supplier') AS supplier_nama
        FROM ospos_items i
        JOIN ospos_item_quantities iq ON i.item_id = iq.item_id AND iq.location_id = 1
        LEFT JOIN (
            SELECT trans_items, SUM(trans_inventory) AS total_change
            FROM ospos_inventory
            WHERE trans_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY trans_items
        ) inv_period ON i.item_id = inv_period.trans_items
        LEFT JOIN (
            SELECT trans_items, SUM(trans_inventory) AS total_change
            FROM ospos_inventory
            WHERE trans_date >= CURDATE()
            GROUP BY trans_items
        ) inv_today ON i.item_id = inv_today.trans_items
        LEFT JOIN ospos_suppliers s ON i.supplier_id = s.person_id
        WHERE i.deleted = 0
          AND i.stock_type = 0
          AND (iq.quantity - COALESCE(inv_today.total_change, 0)) <= i.reorder_level
          AND (iq.quantity - COALESCE(inv_period.total_change, 0)) > i.reorder_level
        ORDER BY supplier_nama, i.name";

$stmt = $db->prepare($sql);
$stmt->bindValue(':days', $days, PDO::PARAM_INT);
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
        'quantity' => (float)$row['current_quantity'],
        'past_quantity' => (float)$row['past_quantity'],
        'yesterday_quantity' => (float)$row['yesterday_quantity'],
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