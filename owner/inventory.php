<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('owner');
require_once __DIR__ . '/../classes/Inventory.php';

$inventoryModel = new Inventory();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $inventoryModel->add(
        trim($_POST['item_name']),
        trim($_POST['category']),
        $_POST['quantity'],
        trim($_POST['unit']),
        $_POST['threshold'],
        $_SESSION['user_id']
    );
    $message = "Item added to inventory.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $inventoryModel->updateQuantity($_POST['item_id'], $_POST['new_quantity']);
    $message = "Quantity updated.";
}

if (isset($_GET['delete'])) {
    $inventoryModel->delete($_GET['delete']);
    header("Location: inventory.php");
    exit;
}

$items = $inventoryModel->getAll();
$pageTitle = "Inventory";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Inventory Oversight</h1>
<?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

<div class="card">
    <h2>Add Inventory Item</h2>
    <form method="POST" class="inline-form">
        <input type="text" name="item_name" placeholder="Item name (e.g. Urea Fertilizer)" required>
        <input type="text" name="category" placeholder="Category (e.g. Fertilizer)">
        <input type="number" step="0.01" name="quantity" placeholder="Quantity" required>
        <input type="text" name="unit" placeholder="Unit (kg, liter, bag)" required>
        <input type="number" step="0.01" name="threshold" placeholder="Low stock threshold" required>
        <button type="submit" name="add_item" class="btn">Add Item</button>
    </form>
</div>

<div class="card">
    <h2>Current Stock</h2>
    <table class="data-table">
        <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Threshold</th>
            <th>Status</th>
            <th>Update Qty</th>
            <th>Action</th>
        </tr>
        <?php foreach ($items as $item): ?>
            <tr class="<?php echo $item['quantity'] <= $item['low_stock_threshold'] ? 'row-warning' : ''; ?>">
                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                <td><?php echo htmlspecialchars($item['category']); ?></td>
                <td><?php echo $item['quantity'] . ' ' . htmlspecialchars($item['unit']); ?></td>
                <td><?php echo $item['low_stock_threshold']; ?></td>
                <td><?php echo $item['quantity'] <= $item['low_stock_threshold'] ? ' Low Stock' : ' OK'; ?></td>
                <td>
                    <form method="POST" class="tiny-inline-form">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        <input type="number" step="0.01" name="new_quantity" value="<?php echo $item['quantity']; ?>" style="width:80px;">
                        <button type="submit" name="update_qty" class="btn btn-small">Update</button>
                    </form>
                </td>
                <td><a href="?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete this item?')" class="link-danger">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>