<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('owner');
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/Harvest.php';
require_once __DIR__ . '/../classes/Task.php';

$orderModel = new Order();
$harvestModel = new Harvest();
$taskModel = new Task();

$totalRevenue = $orderModel->totalRevenue();
$totalYield = $harvestModel->totalYield();
$harvests = $harvestModel->getAll();
$orders = $orderModel->getAll();

$pageTitle = "Reports";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Analytics &amp; Reports</h1>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-value">৳<?php echo number_format($totalRevenue, 2); ?></span>
        <span class="stat-label">Total Revenue</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo number_format($totalYield, 2); ?> kg</span>
        <span class="stat-label">Total Yield Harvested</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo count($orders); ?></span>
        <span class="stat-label">Total Orders</span>
    </div>
</div>

<div class="card">
    <h2>Harvest Report</h2>
    <table class="data-table">
        <tr><th>Crop</th><th>Worker</th><th>Quantity</th><th>Grade</th><th>Date</th></tr>
        <?php foreach ($harvests as $h): ?>
        <tr>
            <td><?php echo htmlspecialchars($h['crop_name']); ?></td>
            <td><?php echo htmlspecialchars($h['worker_name']); ?></td>
            <td><?php echo $h['quantity'] . ' ' . htmlspecialchars($h['unit']); ?></td>
            <td><?php echo $h['quality_grade']; ?></td>
            <td><?php echo $h['harvest_date']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h2>Financial Report (Orders)</h2>
    <table class="data-table">
        <tr><th>Order #</th><th>Buyer</th><th>Amount</th><th>Status</th><th>Date</th></tr>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td>#<?php echo $o['id']; ?></td>
            <td><?php echo htmlspecialchars($o['buyer_name']); ?></td>
            <td>৳<?php echo number_format($o['total_amount'], 2); ?></td>
            <td><?php echo $o['status']; ?></td>
            <td><?php echo $o['order_date']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
