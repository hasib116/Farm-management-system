<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('buyer');
require_once __DIR__ . '/../classes/Order.php';

$orderModel = new Order();
$orders = $orderModel->getByBuyer($_SESSION['user_id']);

$pageTitle = "My Orders";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>My Orders &amp; Delivery Tracking</h1>

<?php if (empty($orders)): ?>
    <div class="card">
        <p>You haven't placed any orders yet. <a href="/smartfarm/buyer/marketplace.php">Browse the marketplace</a>.</p>
    </div>
<?php endif; ?>

<?php foreach ($orders as $o): ?>
    <div class="card">
        <div class="order-header">
            <h2>Order #<?php echo $o['id']; ?></h2>
            <span class="badge badge-<?php echo $o['status']; ?>"><?php echo $o['status']; ?></span>
        </div>
        <p>Placed on: <?php echo $o['order_date']; ?></p>
        <p>Payment Method: <?php echo str_replace('_', ' ', $o['payment_method']); ?></p>
        <p>Total: ৳<?php echo number_format($o['total_amount'], 2); ?></p>

        <div class="tracking-box">
            <strong>Delivery Status:</strong> <?php echo str_replace('_', ' ', $o['delivery_status'] ?? 'processing'); ?><br>
            <strong>Tracking ID:</strong> <?php echo htmlspecialchars($o['tracking_info'] ?? 'N/A'); ?>
            <div class="tracking-progress">
                <?php
                $stages = ['processing', 'shipped', 'out_for_delivery', 'delivered'];
                $currentIndex = array_search($o['delivery_status'] ?? 'processing', $stages);
                foreach ($stages as $i => $stage):
                ?>
                    <span class="stage <?php echo $i <= $currentIndex ? 'stage-active' : ''; ?>">
                        <?php echo ucwords(str_replace('_', ' ', $stage)); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <table class="data-table">
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($orderModel->getOrderItems($o['id']) as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>৳<?php echo number_format($item['price'], 2); ?></td>
                    <td>৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endforeach; ?>