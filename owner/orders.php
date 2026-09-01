<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('owner');
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/Delivery.php';

$orderModel = new Order();
$deliveryModel = new Delivery();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderModel->updateStatus($_POST['order_id'], $_POST['status']);
    $deliveryModel->updateStatus($_POST['order_id'], $_POST['delivery_status']);
    $message = "Order status updated.";
}

$orders = $orderModel->getAll();
$pageTitle = "Orders";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Customer Orders</h1>
<?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

<div class="card">
    <table class="data-table">
        <tr>
            <th>Order #</th>
            <th>Buyer</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Order Status</th>
            <th>Delivery Status</th>
            <th>Update</th>
        </tr>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td><?php echo htmlspecialchars($o['buyer_name']); ?></td>
                <td>৳<?php echo number_format($o['total_amount'], 2); ?></td>
                <td><?php echo str_replace('_', ' ', $o['payment_method']); ?></td>
                <td><span class="badge badge-<?php echo $o['status']; ?>"><?php echo $o['status']; ?></span></td>
                <td><?php echo str_replace('_', ' ', $o['delivery_status'] ?? '-'); ?></td>
                <td>
                    <form method="POST" class="tiny-inline-form">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <select name="status">
                            <option value="pending" <?php echo $o['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $o['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="delivered" <?php echo $o['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $o['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <select name="delivery_status">
                            <option value="processing" <?php echo $o['delivery_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $o['delivery_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="out_for_delivery" <?php echo $o['delivery_status'] == 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                            <option value="delivered" <?php echo $o['delivery_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-small">Save</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>