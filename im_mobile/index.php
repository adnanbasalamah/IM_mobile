<?php
require_once 'config.php';

$page = $_GET['page'] ?? 'dashboard';
$validPages = ['dashboard', 'transfer', 'nota', 'stok', 'kasir', 'harga'];

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
    <link rel="stylesheet" href="assets/css/style.css?v=5">
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
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
            <span class="nav-label">Stock</span>
        </a>
        <a href="#harga" class="nav-item <?= $page === 'harga' ? 'active' : '' ?>" data-page="harga">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 20h9"/>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
            </svg>
            <span class="nav-label">Harga</span>
        </a>
        <a href="#kasir" class="nav-item <?= $page === 'kasir' ? 'active' : '' ?>" data-page="kasir">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
                <line x1="2" y1="10" x2="22" y2="10"/>
            </svg>
            <span class="nav-label">Kasir</span>
        </a>
        <a href="#habis" class="nav-item <?= $page === 'habis' ? 'active' : '' ?>" data-page="habis">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span class="nav-label">Habis</span>
        </a>
    </nav>

    <script src="assets/js/chart.umd.js?v=2"></script>
    <script src="assets/js/app.js?v=5"></script>
    <script>
        IM.init();
    </script>
<?php endif; ?>

</body>
</html>