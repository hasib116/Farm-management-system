<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('buyer');
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Order.php';

$productModel = new Product();
$orderModel = new Order();
$message = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $cart = json_decode($_POST['cart_data'], true);

    if (empty($cart)) {
        $message = "Your cart is empty.";
    } else {
        $items = [];
        foreach ($cart as $productId => $qty) {
            $product = $productModel->getById($productId);
            if ($product && $qty > 0) {
                $items[] = [
                    "product_id" => $productId,
                    "quantity" => $qty,
                    "price" => $product['price']
                ];
            }
        }

        $result = $orderModel->placeOrder($_SESSION['user_id'], $items, $_POST['payment_method']);
        $message = $result['success']
            ? "Order placed successfully! Order #" . $result['order_id']
            : "Order failed: " . $result['message'];
    }
}

$products = $productModel->getAvailable();
$pageTitle = "Marketplace";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Marketplace</h1>
<?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

<div class="marketplace-layout">
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <?php if ($p['image']): ?>
                    <img src="/smartfarm/assets/uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                <?php else: ?>
                    <div class="product-placeholder"></div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                <p><?php echo htmlspecialchars($p['description']); ?></p>
                <p class="price">৳<?php echo number_format($p['price'], 2); ?> / <?php echo htmlspecialchars($p['unit']); ?></p>
                <p class="stock-info">In stock: <?php echo $p['stock_quantity']; ?> <?php echo htmlspecialchars($p['unit']); ?></p>
                <div class="qty-control">
                    <input type="number" min="0" max="<?php echo $p['stock_quantity']; ?>" step="1" value="0"
                        class="qty-input" data-id="<?php echo $p['id']; ?>" data-price="<?php echo $p['price']; ?>" data-name="<?php echo htmlspecialchars($p['name']); ?>">
                    <button type="button" class="btn btn-small add-to-cart" data-id="<?php echo $p['id']; ?>">Add to Cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <aside class="cart-box">
        <h2> Your Cart</h2>
        <ul id="cart-list" class="cart-list">
            <li class="cart-empty">Cart is empty</li>
        </ul>
        <p class="cart-total">Total: ৳<span id="cart-total">0.00</span></p>

        <form method="POST" id="order-form">
            <label>Payment Method</label>
            <select name="payment_method" required>
                <option value="cash_on_delivery">Cash on Delivery</option>
                <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
                <option value="card">Card</option>
            </select>
            <input type="hidden" name="cart_data" id="cart_data">
            <button type="submit" name="place_order" class="btn btn-block">Place Order</button>
        </form>
    </aside>
</div>