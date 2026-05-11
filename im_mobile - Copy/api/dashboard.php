<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$date = $_GET['date'] ?? date('Y-m-d');
$db = getDB();

$sql = "SELECT HOUR(s.sale_time) as jam,
               sp.payment_type,
               SUM(sp.payment_amount) as total_payment,
               SUM(sp.cash_refund) as total_refund
        FROM ospos_sales s
        JOIN ospos_sales_payments sp ON s.sale_id = sp.sale_id
        WHERE DATE(s.sale_time) = ?
          AND s.sale_status = 0
        GROUP BY HOUR(s.sale_time), sp.payment_type
        ORDER BY jam";

$stmt = $db->prepare($sql);
$stmt->execute([$date]);
$rows = $stmt->fetchAll();

$hourlyData = [];
for ($h = 6; $h <= 21; $h++) {
    $hourlyData[$h] = [
        'jam' => $h,
        'jumlah_transaksi' => 0,
        'nilai_transaksi' => 0,
        'tunai' => 0,
        'debit' => 0,
        'transfer' => 0,
        'qris' => 0,
    ];
}

$totalJumlah = 0;
$totalNilai = 0;

$saleCountSql = "SELECT COUNT(DISTINCT sale_id) as cnt 
                  FROM ospos_sales 
                  WHERE DATE(sale_time) = ? AND sale_status = 0";
$countStmt = $db->prepare($saleCountSql);
$countStmt->execute([$date]);
$totalJumlah = (int)$countStmt->fetchColumn();

foreach ($rows as $row) {
    $h = (int)$row['jam'];
    if (!isset($hourlyData[$h])) continue;

    $paymentType = strtolower(trim($row['payment_type']));
    $grossAmount = (float)$row['total_payment'];
    $refund = (float)$row['total_refund'];

    $isCash = (strpos($paymentType, 'cash') !== false || strpos($paymentType, 'tunai') !== false);
    $netAmount = $isCash ? ($grossAmount - $refund) : $grossAmount;
    if ($netAmount < 0) $netAmount = 0;

    $hourlyData[$h]['nilai_transaksi'] += $netAmount;

    if ($isCash) {
        $hourlyData[$h]['tunai'] += $netAmount;
    } elseif (strpos($paymentType, 'debit') !== false) {
        $hourlyData[$h]['debit'] += $netAmount;
    } elseif (strpos($paymentType, 'transfer') !== false) {
        $hourlyData[$h]['transfer'] += $netAmount;
    } elseif (strpos($paymentType, 'qris') !== false) {
        $hourlyData[$h]['qris'] += $netAmount;
    } else {
        $hourlyData[$h]['tunai'] += $netAmount;
    }
}

foreach ($hourlyData as $h => &$data) {
    $totalNilai += $data['nilai_transaksi'];
}

$saleHourSql = "SELECT HOUR(sale_time) as jam, COUNT(DISTINCT sale_id) as cnt 
                FROM ospos_sales 
                WHERE DATE(sale_time) = ? AND sale_status = 0 
                GROUP BY HOUR(sale_time)";
$hourStmt = $db->prepare($saleHourSql);
$hourStmt->execute([$date]);
$hourCounts = $hourStmt->fetchAll(PDO::FETCH_KEY_PAIR);
foreach ($hourlyData as $h => &$data) {
    $data['jumlah_transaksi'] = isset($hourCounts[$h]) ? (int)$hourCounts[$h] : 0;
}

echo json_encode([
    'date' => $date,
    'total_jumlah' => $totalJumlah,
    'total_nilai' => $totalNilai,
    'hourly' => array_values($hourlyData),
]);