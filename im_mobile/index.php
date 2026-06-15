<?php
require_once 'config.php';

$page = $_GET['page'] ?? 'dashboard';
$validPages = ['dashboard', 'transfer', 'nota', 'stok', 'omset'];

if (!isLoggedIn()) {
    $page = 'login';
} elseif (!in_array($page, $validPages)) {
    $page = 'dashboard';
}

$user = currentUser();
$initial = $user ? strtoupper(substr($user['first_name'], 0, 1)) : 'U';
$displayName = $user ? $user['first_name'] : 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $page === 'login' ? 'Login' : ucfirst($page); ?> | IkhwanMart</title>
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="IkhwanMart">
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/icons/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/icons/apple-touch-icon-180x180.png">
    <link rel="stylesheet" href="assets/css/style.css?v=7">
</head>
<body class="<?= $page === 'login' ? '' : 'has-nav' ?>">

<?php if ($page === 'login'): ?>
    <?php include 'pages/login.php'; ?>
<?php else: ?>
    <header class="app-header">
        <div class="brand">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <h1>IkhwanMart</h1>
        </div>
        <div class="user-info">
            <span style="font-size:0.75rem;color:var(--on-surface-variant);display:none;" class="sm-hide"><?= htmlspecialchars($displayName) ?></span>
            <a href="auth.php?action=logout" class="user-avatar" title="Logout (<?= htmlspecialchars($displayName) ?>)" style="text-decoration:none;cursor:pointer;"><?= $initial ?></a>
        </div>
    </header>

    <main id="content" class="app-content">
        <?php include "pages/{$page}.php"; ?>
    </main>

    <nav class="bottom-nav">
        <a href="#dashboard" class="nav-item <?= $page === 'dashboard' ? 'active' : '' ?>" data-page="dashboard">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="#transfer" class="nav-item <?= $page === 'transfer' ? 'active' : '' ?>" data-page="transfer">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="17 1 21 5 17 9"/>
                <path d="M3 11V9a4 4 0 0 1 4-4h14"/>
                <polyline points="7 23 3 19 7 15"/>
                <path d="M21 13v2a4 4 0 0 1-4 4H3"/>
            </svg>
            <span class="nav-label">Transfer</span>
        </a>
        <a href="#stok" class="nav-item <?= $page === 'stok' ? 'active' : '' ?>" data-page="stok">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span class="nav-label">Stok</span>
        </a>
        <a href="#omset" class="nav-item <?= $page === 'omset' ? 'active' : '' ?>" data-page="omset">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
                <line x1="2" y1="20" x2="22" y2="20"/>
            </svg>
            <span class="nav-label">Omset</span>
        </a>
    </nav>

    <script src="assets/js/chart.umd.js?v=2"></script>
    <script src="assets/js/app.js?v=6"></script>
    <script>
        IM.isAdmin = <?php echo !empty($_SESSION['is_admin']) ? 'true' : 'false'; ?>;
        IM.init();
    </script>
<?php endif; ?>

</body>
</html>