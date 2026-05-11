<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$itemId = intval($input['item_id'] ?? 0);
$costPrice = isset($input['cost_price']) ? floatval($input['cost_price']) : null;
$unitPrice = isset($input['unit_price']) ? floatval($input['unit_price']) : null;

if (!$itemId || $costPrice === null || $unitPrice === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak lengkap']);
    exit;
}

$db = getDB();

$check = $db->prepare("SELECT item_id FROM ospos_items WHERE item_id = ? AND deleted = 0 AND category = 'PASAR SEGAR'");
$check->execute([$itemId]);
if (!$check->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Produk tidak ditemukan']);
    exit;
}

$stmt = $db->prepare("UPDATE ospos_items SET cost_price = ?, unit_price = ? WHERE item_id = ?");
$result = $stmt->execute([$costPrice, $unitPrice, $itemId]);

if ($result) {
    echo json_encode(['success' => true, 'item_id' => $itemId, 'cost_price' => $costPrice, 'unit_price' => $unitPrice]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan']);
}