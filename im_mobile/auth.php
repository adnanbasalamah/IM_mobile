<?php
require_once 'config.php';

$action = $_REQUEST['action'] ?? 'login';

if ($action === 'logout') {
    session_destroy();
    header('Location: index.php?page=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Username dan password harus diisi';
        header('Location: index.php?page=login');
        exit;
    }

    $db = getDB();
    $stmt = $db->prepare(
        "SELECT e.person_id, e.username, e.password, e.hash_version, 
                p.first_name, p.last_name
         FROM ospos_employees e
         JOIN ospos_people p ON e.person_id = p.person_id
         WHERE e.username = ? AND e.deleted = 0"
    );
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['login_error'] = 'Username atau password salah';
        header('Location: index.php?page=login');
        exit;
    }

    $hashVerified = false;
    if ($user['hash_version'] == 2) {
        $hashVerified = password_verify($password, $user['password']);
    } else {
        $hashVerified = ($user['password'] == sha1($password));
    }

    if (!$hashVerified) {
        $_SESSION['login_error'] = 'Username atau password salah';
        header('Location: index.php?page=login');
        exit;
    }

    $_SESSION['user_id'] = $user['person_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    unset($_SESSION['login_error']);

    header('Location: index.php?page=dashboard');
    exit;
}

header('Location: index.php?page=login');
exit;