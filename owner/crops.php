<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('owner');
require_once __DIR__ . '/../classes/Crop.php';

$cropModel = new Crop();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_crop'])) {
    $cropModel->create(
        trim($_POST['name']),
        trim($_POST['variety']),
        $_POST['planted_date'],
        $_POST['expected_harvest_date'],
        $_SESSION['user_id']
    );
    $message = "Crop added successfully.";
}

$crops = $cropModel->getAll();
$pageTitle = "Manage Crops";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Crop Planning</h1>
<?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

<div class="card">
    <h2>Add New Crop Cycle</h2>
    <form method="POST" class="inline-form">
        <input type="text" name="name" placeholder="Crop Name (e.g. Rice)" required>
        <input type="text" name="variety" placeholder="Variety (e.g. BRRI-29)">
        <label>Planted Date <input type="date" name="planted_date" required></label>
        <label>Expected Harvest <input type="date" name="expected_harvest_date" required></label>
        <button type="submit" name="add_crop" class="btn">Add Crop</button>
    </form>
</div>

<div class="card">
    <h2>All Crops</h2>
    <table class="data-table">
        <tr><th>Name</th><th>Variety</th><th>Planted</th><th>Expected Harvest</th><th>Status</th></tr>
        <?php foreach ($crops as $c): ?>
        <tr>
            <td><?php echo htmlspecialchars($c['name']); ?></td>
            <td><?php echo htmlspecialchars($c['variety']); ?></td>
            <td><?php echo $c['planted_date']; ?></td>
            <td><?php echo $c['expected_harvest_date']; ?></td>
            <td><span class="badge badge-<?php echo $c['status']; ?>"><?php echo $c['status']; ?></span></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
