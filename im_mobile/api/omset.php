<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = getDB();

$today = new DateTime();
$yesterday = new DateTime();
$yesterday->sub(new DateInterval('P1D'));

$firstThis = new DateTime();
$firstThis->modify('first day of this month');

$firstLast = new DateTime();
$firstLast->modify('first day of last month');

$lastDayLast = new DateTime();
$lastDayLast->modify('last day of last month');

$thisMonthStart = $firstThis->format('Y-m-d');
$thisMonthEnd = $yesterday->format('Y-m-d');
$lastMonthStart = $firstLast->format('Y-m-d');
$lastMonthEnd = $lastDayLast->format('Y-m-d');

function getTotalRevenue($db, $start, $end) {
    $sql = "SELECT sp.payment_type, SUM(sp.payment_amount) as total_payment, SUM(sp.cash_refund) as total_refund
            FROM ospos_sales s
            JOIN ospos_sales_payments sp ON s.sale_id = sp.sale_id
            WHERE s.sale_status = 0
              AND DATE(s.sale_time) BETWEEN ? AND ?
            GROUP BY sp.payment_type";
    $stmt = $db->prepare($sql);
    $stmt->execute([$start, $end]);
    $rows = $stmt->fetchAll();

    $total = 0;
    foreach ($rows as $row) {
        $paymentType = strtolower(trim($row['payment_type']));
        $grossAmount = (float)$row['total_payment'];
        $refund = (float)$row['total_refund'];
        $isCash = (strpos($paymentType, 'cash') !== false || strpos($paymentType, 'tunai') !== false);
        $netAmount = $isCash ? ($grossAmount - $refund) : $grossAmount;
        if ($netAmount < 0) $netAmount = 0;
        $total += $netAmount;
    }
    return $total;
}

$totalThisMonth = getTotalRevenue($db, $thisMonthStart, $thisMonthEnd);
$totalLastMonth = getTotalRevenue($db, $lastMonthStart, $lastMonthEnd);

$daysLastMonth = (int)$lastDayLast->format('j');

$daysThisMonth = 0;
if ($yesterday->format('Y-m') === $today->format('Y-m')) {
    $daysThisMonth = (int)$yesterday->format('j');
}

$avgThisMonth = $daysThisMonth > 0 ? $totalThisMonth / $daysThisMonth : 0;
$avgLastMonth = $daysLastMonth > 0 ? $totalLastMonth / $daysLastMonth : 0;
$changePercent = $avgLastMonth > 0 ? (($avgThisMonth - $avgLastMonth) / $avgLastMonth) * 100 : 0;

echo json_encode([
    'total_this_month' => round($totalThisMonth),
    'total_last_month' => round($totalLastMonth),
    'days_this_month' => $daysThisMonth,
    'days_last_month' => $daysLastMonth,
    'avg_this_month' => round($avgThisMonth),
    'avg_last_month' => round($avgLastMonth),
    'change_percent' => round($changePercent, 1),
]);