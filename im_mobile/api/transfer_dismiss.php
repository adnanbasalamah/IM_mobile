<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$sale_id = intval($input['sale_id'] ?? 0);

if (!$sale_id) {
    http_response_code(400);
    echo json_encode(['error' => 'sale_id required']);
    exit;
}

$db = getDB();

$stmt = $db->prepare("UPDATE ospos_sales_payments SET reference_code = '1' WHERE sale_id = ? AND payment_type LIKE '%Transfer%' AND cash_adjustment = 0");
$stmt->execute([$sale_id]);

echo json_encode(['success' => true, 'sale_id' => $sale_id]);