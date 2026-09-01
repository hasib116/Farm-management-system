<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('worker');
require_once __DIR__ . '/../classes/FieldLog.php';
require_once __DIR__ . '/../classes/Crop.php';

$fieldLogModel = new FieldLog();
$cropModel = new Crop();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_log'])) {
    $photoName = "";
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoName = uniqid('log_') . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../assets/uploads/' . $photoName);
    }

    $fieldLogModel->create(
        $_SESSION['user_id'],
        $_POST['crop_id'],
        $_POST['entry_date'],
        trim($_POST['notes']),
        $photoName
    );
    $message = "Field log recorded.";
}

$crops = $cropModel->getAll();
$logs = $fieldLogModel->getByWorker($_SESSION['user_id']);

$pageTitle = "Field Data Logging";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Field Data Logging</h1>
<?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

<div class="card">
    <h2>Log New Field Entry</h2>
    <form method="POST" enctype="multipart/form-data" class="stacked-form">
        <label>Crop / Livestock</label>
        <select name="crop_id" required>
            <?php foreach ($crops as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Entry Date</label>
        <input type="date" name="entry_date" required>

        <label>Observational Notes (health, condition, etc.)</label>
        <textarea name="notes" required></textarea>

        <label>Photo Evidence (optional)</label>
        <input type="file" name="photo" accept="image/*">

        <button type="submit" name="add_log" class="btn">Submit Log</button>
    </form>
</div>

<div class="card">
    <h2>My Field Logs</h2>
    <table class="data-table">
        <tr>
            <th>Crop</th>
            <th>Date</th>
            <th>Notes</th>
            <th>Photo</th>
        </tr>
        <?php foreach ($logs as $l): ?>
            <tr>
                <td><?php echo htmlspecialchars($l['crop_name']); ?></td>
                <td><?php echo $l['entry_date']; ?></td>
                <td><?php echo htmlspecialchars($l['notes']); ?></td>
                <td>
                    <?php if ($l['photo']): ?>
                        <a href="/smartfarm/assets/uploads/<?php echo htmlspecialchars($l['photo']); ?>" target="_blank">View</a>
                        <?php else: ?>-<?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
