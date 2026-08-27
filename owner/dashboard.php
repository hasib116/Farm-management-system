<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('owner');
require_once __DIR__ . '/../classes/Task.php';
require_once __DIR__ . '/../classes/Inventory.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/Harvest.php';

$taskModel = new Task();
$inventoryModel = new Inventory();
$orderModel = new Order();
$harvestModel = new Harvest();

$pendingTasks = $taskModel->countByStatus('pending');
$completedTasks = $taskModel->countByStatus('completed');
$lowStockItems = $inventoryModel->getLowStock();
$totalRevenue = $orderModel->totalRevenue();
$totalYield = $harvestModel->totalYield();

$pageTitle = "Owner Dashboard";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Farm Owner Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-value"><?php echo $pendingTasks; ?></span>
        <span class="stat-label">Pending Tasks</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo $completedTasks; ?></span>
        <span class="stat-label">Completed Tasks</span>
    </div>
    <div class="stat-card">
        <span class="stat-value">৳<?php echo number_format($totalRevenue, 2); ?></span>
        <span class="stat-label">Total Revenue</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo number_format($totalYield, 2); ?> kg</span>
        <span class="stat-label">Total Yield</span>
    </div>
</div>

<div class="card">
    <h2>⚠️ Low Stock Alerts</h2>
    <?php if (empty($lowStockItems)): ?>
        <p>All inventory levels are healthy.</p>
    <?php else: ?>
        <table class="data-table">
            <tr><th>Item</th><th>Category</th><th>Quantity</th><th>Threshold</th></tr>
            <?php foreach ($lowStockItems as $item): ?>
            <tr class="row-warning">
                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                <td><?php echo htmlspecialchars($item['category']); ?></td>
                <td><?php echo $item['quantity'] . ' ' . htmlspecialchars($item['unit']); ?></td>
                <td><?php echo $item['low_stock_threshold']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<div class="quick-links">
    <a href="/smartfarm/owner/tasks.php" class="card link-card">📋 Manage Tasks</a>
    <a href="/smartfarm/owner/inventory.php" class="card link-card">📦 Manage Inventory</a>
    <a href="/smartfarm/owner/crops.php" class="card link-card">🌱 Manage Crops</a>
    <a href="/smartfarm/owner/products.php" class="card link-card">🛒 Marketplace</a>
    <a href="/smartfarm/owner/orders.php" class="card link-card">📦 Orders</a>
    <a href="/smartfarm/owner/reports.php" class="card link-card">📊 Reports</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
