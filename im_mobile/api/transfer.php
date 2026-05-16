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

$sql = "SELECT s.sale_id,
               HOUR(s.sale_time) as jam,
               s.sale_time,
               CONCAT(emp.first_name, ' ', emp.last_name) as kasir_nama,
               s.customer_id,
               sp.payment_amount,
               sp.payment_type,
               COALESCE(c.company_name, CONCAT(cust.first_name, ' ', cust.last_name)) as pelanggan_nama
        FROM ospos_sales s
        JOIN ospos_sales_payments sp ON s.sale_id = sp.sale_id
        JOIN ospos_employees e ON s.employee_id = e.person_id
        JOIN ospos_people emp ON e.person_id = emp.person_id
        LEFT JOIN ospos_customers c ON s.customer_id = c.person_id
        LEFT JOIN ospos_people cust ON c.person_id = cust.person_id
        WHERE DATE(s.sale_time) = ?
          AND sp.payment_type LIKE '%Transfer%'
          AND s.sale_status = 0
          AND sp.cash_adjustment = 0
          AND (sp.reference_code IS NULL OR sp.reference_code = '')
        ORDER BY s.sale_time ASC";

$stmt = $db->prepare($sql);
$stmt->execute([$date]);
$rows = $stmt->fetchAll();

$sales = [];
$saleIds = [];

foreach ($rows as $row) {
    if (in_array($row['sale_id'], $saleIds)) continue;
    $saleIds[] = $row['sale_id'];

    $custName = $row['pelanggan_nama'];
    if (empty($custName) || empty($row['customer_id'])) {
        $custName = 'Umum';
    }

    $jam_fmt = substr($row['sale_time'], 11, 5) ?: sprintf('%02d:00', (int)$row['jam']);

    $sales[] = [
        'sale_id' => $row['sale_id'],
        'jam' => (int)$row['jam'],
        'jam_fmt' => $jam_fmt,
        'kasir' => $row['kasir_nama'],
        'pelanggan' => $custName,
        'nilai' => (float)$row['payment_amount'],
    ];
}

$totalNilai = 0;
foreach ($sales as $s) {
    $totalNilai += $s['nilai'];
}

echo json_encode([
    'date' => $date,
    'total_nilai' => $totalNilai,
    'total_transaksi' => count($sales),
    'transfers' => $sales,
]);