<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'ospos');
define('DB_USER', 'ospos');
define('DB_PASS', 'ospos');

define('REKENING_BANK', 'BCA 139.240.9577');
define('REKENING_AN', 'Elis Suryani');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
    }
    return $pdo;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        if (php_sapi_name() !== 'cli') {
            header('Location: index.php?page=login');
            exit;
        }
    }
}

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'person_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'first_name' => $_SESSION['first_name'] ?? '',
        'last_name' => $_SESSION['last_name'] ?? '',
    ];
}