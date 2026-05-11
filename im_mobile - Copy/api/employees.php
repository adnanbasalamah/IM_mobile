<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = getDB();

$sql = "SELECT e.person_id, CONCAT(p.first_name, ' ', p.last_name) as nama
        FROM ospos_employees e
        JOIN ospos_people p ON e.person_id = p.person_id
        WHERE e.deleted = 0
        ORDER BY p.first_name";
$stmt = $db->query($sql);
$employees = $stmt->fetchAll();

echo json_encode(['employees' => $employees]);