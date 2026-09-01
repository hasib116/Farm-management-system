<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('owner');
require_once __DIR__ . '/../classes/Product.php';

$productModel = new Product();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $imageName = "";

    // handle optional image upload
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('prod_') . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../assets/uploads/' . $imageName);
    }

    $productModel->create(
        $_SESSION['user_id'],
        trim($_POST['name']),
        trim($_POST['description']),
        $_POST['price'],
        $_POST['stock_quantity'],
        trim($_POST['unit']),
        $imageName
    );
    $message = "Product listed on marketplace.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $productModel->updatePriceStock($_POST['product_id'], $_POST['price'], $_POST['stock_quantity']);
    $message = "Product updated.";
}

if (isset($_GET['delete'])) {
    $productModel->delete($_GET['delete']);
    header("Location: products.php");
    exit;
}

$products = $productModel->getByOwner($_SESSION['user_id']);
$pageTitle = "Marketplace Products";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Marketplace Management</h1>
<?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

<div class="card">
    <h2>List a New Product</h2>
    <form method="POST" enctype="multipart/form-data" class="stacked-form">
        <label>Product Name</label>
        <input type="text" name="name" required>

        <label>Description</label>
        <textarea name="description"></textarea>

        <label>Price (per unit)</label>
        <input type="number" step="0.01" name="price" required>

        <label>Stock Quantity</label>
        <input type="number" step="0.01" name="stock_quantity" required>

        <label>Unit</label>
        <input type="text" name="unit" placeholder="kg, dozen, liter" required>

        <label>Product Image (optional)</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" name="add_product" class="btn">List Product</button>
    </form>
</div>

<div class="card">
    <h2>My Listed Products</h2>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <?php if ($p['image']): ?>
                    <img src="/smartfarm/assets/uploads/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                <?php else: ?>
                    <div class="product-placeholder">🌽</div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                <p><?php echo htmlspecialchars($p['description']); ?></p>
                <form method="POST" class="tiny-inline-form">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <label>৳ <input type="number" step="0.01" name="price" value="<?php echo $p['price']; ?>" style="width:70px;"></label>
                    <label>Stock <input type="number" step="0.01" name="stock_quantity" value="<?php echo $p['stock_quantity']; ?>" style="width:70px;"></label>
                    / <?php echo htmlspecialchars($p['unit']); ?>
                    <button type="submit" name="update_product" class="btn btn-small">Update</button>
                </form>
                <a href="?delete=<?php echo $p['id']; ?>" onclick="return confirm('Remove this product?')" class="link-danger">Remove</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>