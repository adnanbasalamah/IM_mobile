<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$saleId = intval($_GET['id'] ?? 0);
if (!$saleId) {
    http_response_code(400);
    echo json_encode(['error' => 'Sale ID required']);
    exit;
}

$db = getDB();

$saleSql = "SELECT s.sale_id, s.sale_time, s.sale_status,
                   COALESCE(CONCAT(emp.first_name, ' ', emp.last_name), emp.first_name, emp.last_name, 'Unknown') as kasir_nama,
                   s.customer_id
            FROM ospos_sales s
            JOIN ospos_employees e ON s.employee_id = e.person_id
            JOIN ospos_people emp ON e.person_id = emp.person_id
            WHERE s.sale_id = ?";
$saleStmt = $db->prepare($saleSql);
$saleStmt->execute([$saleId]);
$sale = $saleStmt->fetch();

if (!$sale) {
    http_response_code(404);
    echo json_encode(['error' => 'Sale not found']);
    exit;
}

$custName = 'Umum';
$custId = $sale['customer_id'];
if ($custId) {
    $custSql = "SELECT COALESCE(c.company_name, CONCAT(p.first_name, ' ', p.last_name)) as nama
                FROM ospos_customers c
                JOIN ospos_people p ON c.person_id = p.person_id
                WHERE c.person_id = ?";
    $custStmt = $db->prepare($custSql);
    $custStmt->execute([$custId]);
    $result = $custStmt->fetchColumn();
    if ($result) $custName = $result;
}

$itemsSql = "SELECT si.item_id, si.quantity_purchased, si.item_unit_price,
                    si.discount, si.discount_type,
                    i.name as item_name, i.item_number, i.pack_name
             FROM ospos_sales_items si
             JOIN ospos_items i ON si.item_id = i.item_id
             WHERE si.sale_id = ?";
$itemsStmt = $db->prepare($itemsSql);
$itemsStmt->execute([$saleId]);
$items = $itemsStmt->fetchAll();

$itemsList = [];
$subtotal = 0;
foreach ($items as $item) {
    $unitPrice = (float)$item['item_unit_price'];
    $qty = (float)$item['quantity_purchased'];
    $discount = (float)$item['discount'];
    $discountType = (int)$item['discount_type'];

    if ($discountType == 1) {
        $lineTotal = ($unitPrice * $qty) - ($discount * $qty);
    } elseif ($discountType == 0 && $discount > 0) {
        $lineTotal = ($unitPrice * $qty) * (1 - $discount / 100);
    } else {
        $lineTotal = $unitPrice * $qty - $discount;
    }

    $itemsList[] = [
        'name' => $item['item_name'],
        'sku' => $item['item_number'],
        'pack_name' => $item['pack_name'],
        'qty' => $qty,
        'unit_price' => $unitPrice,
        'discount' => $discount,
        'discount_type' => $discountType,
        'line_total' => round($lineTotal),
    ];
    $subtotal += $lineTotal;
}

$paymentSql = "SELECT payment_type, payment_amount FROM ospos_sales_payments WHERE sale_id = ?";
$paymentStmt = $db->prepare($paymentSql);
$paymentStmt->execute([$saleId]);
$payments = $paymentStmt->fetchAll();

$paymentList = [];
$totalPaid = 0;
foreach ($payments as $p) {
    $paymentList[] = [
        'type' => $p['payment_type'],
        'amount' => (float)$p['payment_amount'],
    ];
    $totalPaid += (float)$p['payment_amount'];
}

$isTransfer = false;
foreach ($paymentList as $p) {
    if (stripos($p['type'], 'Transfer') !== false) {
        $isTransfer = true;
        break;
    }
}

echo json_encode([
    'sale_id' => $saleId,
    'sale_time' => $sale['sale_time'],
    'kasir' => $sale['kasir_nama'],
    'pelanggan' => $custName,
    'items' => $itemsList,
    'subtotal' => round($subtotal),
    'payments' => $paymentList,
    'total' => round($totalPaid),
    'is_transfer' => $isTransfer,
    'rekening' => REKENING_BANK,
    'rekening_an' => REKENING_AN,
]);