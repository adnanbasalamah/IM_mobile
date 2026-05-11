<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$kategori = $_GET['kategori'] ?? '';

if (empty($kategori)) {
    echo json_encode(['items' => []]);
    exit;
}

$db = getDB();

$sql = "SELECT i.name, i.item_number, i.category, i.pack_name, i.reorder_level,
               iq.quantity,
               COALESCE(s.company_name, 'Tanpa Supplier') AS supplier_nama
        FROM ospos_items i
        JOIN ospos_item_quantities iq ON i.item_id = iq.item_id AND iq.location_id = 1
        LEFT JOIN ospos_suppliers s ON i.supplier_id = s.person_id
        WHERE i.deleted = 0
          AND iq.quantity <= i.reorder_level
          AND i.category = ?
        ORDER BY supplier_nama, i.name";

$stmt = $db->prepare($sql);
$stmt->execute([$kategori]);
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
        'category' => $row['category'],
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

echo json_encode(['items' => $result]);