<?php
// Expects $pageTitle to be set by the including page.
if (!isset($pageTitle)) {
    $pageTitle = "SmartFarm";
}
$role = $_SESSION['role'] ?? null;
$name = $_SESSION['name'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | SmartFarm</title>
    <link rel="stylesheet" href="/smartfarm/assets/css/style.css">
</head>

<body>
    <header class="topbar">
        <div class="topbar-brand">
            <span class="logo">🌾</span>
            <span>SmartFarm</span>
        </div>

        <?php if ($role): ?>
            <nav class="topbar-nav">
                <?php if ($role === 'owner'): ?>
                    <a href="/smartfarm/owner/dashboard.php">Dashboard</a>
                    <a href="/smartfarm/owner/tasks.php">Tasks</a>
                    <a href="/smartfarm/owner/inventory.php">Inventory</a>
                    <a href="/smartfarm/owner/crops.php">Crops</a>
                    <a href="/smartfarm/owner/products.php">Marketplace</a>
                    <a href="/smartfarm/owner/orders.php">Orders</a>
                    <a href="/smartfarm/owner/reports.php">Reports</a>
                <?php elseif ($role === 'worker'): ?>
                    <a href="/smartfarm/worker/dashboard.php">Dashboard</a>
                    <a href="/smartfarm/worker/tasks.php">My Tasks</a>
                    <a href="/smartfarm/worker/field_logs.php">Field Logs</a>
                    <a href="/smartfarm/worker/harvest.php">Harvest</a>
                <?php elseif ($role === 'buyer'): ?>
                    <a href="/smartfarm/buyer/dashboard.php">Dashboard</a>
                    <a href="/smartfarm/buyer/marketplace.php">Marketplace</a>
                    <a href="/smartfarm/buyer/orders.php">My Orders</a>
                <?php endif; ?>
                <a href="/smartfarm/includes/profile.php">Profile</a>
            </nav>
            <div class="topbar-user">
                <span>Hi, <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($role); ?>)</span>
                <a href="/smartfarm/auth/logout.php" class="btn-logout">Logout</a>
            </div>
        <?php endif; ?>
    </header>
    <main class="container">