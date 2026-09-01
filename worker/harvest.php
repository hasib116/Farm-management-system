<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('worker');
require_once __DIR__ . '/../classes/Harvest.php';
require_once __DIR__ . '/../classes/Crop.php';

$harvestModel = new Harvest();
$cropModel = new Crop();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_harvest'])) {
    $harvestModel->create(
        $_POST['crop_id'],
        $_SESSION['user_id'],
        $_POST['quantity'],
        trim($_POST['unit']),
        $_POST['harvest_date'],
        $_POST['quality_grade']
    );
    $message = "Harvest recorded successfully.";
}

$crops = $cropModel->getAll();
$harvests = $harvestModel->getByWorker($_SESSION['user_id']);

$pageTitle = "Harvest Recording";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Harvest Recording</h1>
<?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

<div class="card">
    <h2>Record New Harvest</h2>
    <form method="POST" class="stacked-form">
        <label>Crop</label>
        <select name="crop_id" required>
            <?php foreach ($crops as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Quantity</label>
        <input type="number" step="0.01" name="quantity" required>

        <label>Unit</label>
        <input type="text" name="unit" value="kg" required>

        <label>Harvest Date</label>
        <input type="date" name="harvest_date" required>

        <label>Quality Grade</label>
        <select name="quality_grade">
            <option value="A">A - Excellent</option>
            <option value="B">B - Good</option>
            <option value="C">C - Average</option>
        </select>

        <button type="submit" name="add_harvest" class="btn">Record Harvest</button>
    </form>
</div>

<div class="card">
    <h2>My Harvest Records</h2>
    <table class="data-table">
        <tr>
            <th>Crop</th>
            <th>Quantity</th>
            <th>Grade</th>
            <th>Date</th>
        </tr>
        <?php foreach ($harvests as $h): ?>
            <tr>
                <td><?php echo htmlspecialchars($h['crop_name']); ?></td>
                <td><?php echo $h['quantity'] . ' ' . htmlspecialchars($h['unit']); ?></td>
                <td><?php echo $h['quality_grade']; ?></td>
                <td><?php echo $h['harvest_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
sh