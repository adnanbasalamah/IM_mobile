<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = getDB();
$personId = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT COUNT(*) FROM ospos_grants WHERE person_id = ? AND permission_id IN ('employees', 'config')");
$stmt->execute([$personId]);
$isAdmin = $stmt->fetchColumn() > 0;

echo json_encode(['is_admin' => (bool)$isAdmin]);