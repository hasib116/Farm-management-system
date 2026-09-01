<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('buyer');
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/Product.php';

$orderModel = new Order();
$productModel = new Product();

$myOrders = $orderModel->getByBuyer($_SESSION['user_id']);
$availableProducts = $productModel->getAvailable();

$pageTitle = "Buyer Dashboard";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Welcome to SmartFarm Marketplace</h1>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-value"><?php echo count($myOrders); ?></span>
        <span class="stat-label">My Orders</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo count($availableProducts); ?></span>
        <span class="stat-label">Products Available</span>
    </div>
</div>

<div class="quick-links">
    <a href="/smartfarm/buyer/marketplace.php" class="card link-card"> Browse Marketplace</a>
    <a href="/smartfarm/buyer/orders.php" class="card link-card"> Track My Orders</a>
</div>

<div class="card">
    <h2>Freshly Listed Produce</h2>
    <div class="product-grid">
        <?php foreach (array_slice($availableProducts, 0, 4) as $p): ?>
            <div class="product-card">
                <?php if ($p['image']): ?>
                    <img src="/smartfarm/assets/uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                <?php else: ?>
                    <div class="product-placeholder"></div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                <p>৳<?php echo number_format($p['price'], 2); ?> / <?php echo htmlspecialchars($p['unit']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>