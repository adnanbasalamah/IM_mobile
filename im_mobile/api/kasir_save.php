<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$personId = intval($input['person_id'] ?? 0);
$cashier = trim($input['cashier'] ?? '');
$recordDate = trim($input['record_date'] ?? '');
$recordTime = trim($input['record_time'] ?? '');
$rp100k = intval($input['rp100k'] ?? 0);
$rp50k = intval($input['rp50k'] ?? 0);
$rp20k = intval($input['rp20k'] ?? 0);
$rp10k = intval($input['rp10k'] ?? 0);
$rp5k = intval($input['rp5k'] ?? 0);
$rp2k = intval($input['rp2k'] ?? 0);
$rp1k = intval($input['rp1k'] ?? 0);
$coinTotal = intval($input['coin_total'] ?? 0);
$totalKutipan = intval($input['total_kutipan'] ?? 0);
$totalDiKasir = intval($input['total_di_kasir'] ?? 0);

if ($personId <= 0 || empty($cashier) || empty($recordDate) || empty($recordTime)) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak lengkap']);
    exit;
}

$db = getDB();

$sql = "INSERT INTO ospos_cash_records 
        (person_id, cashier, record_date, record_time, rp100k, rp50k, rp20k, rp10k, rp5k, rp2k, rp1k, coin_total, total_kutipan, total_di_kasir)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $db->prepare($sql);
$result = $stmt->execute([
    $personId, $cashier, $recordDate, $recordTime,
    $rp100k, $rp50k, $rp20k, $rp10k, $rp5k, $rp2k, $rp1k,
    $coinTotal, $totalKutipan, $totalDiKasir
]);

if ($result) {
    echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan data']);
}