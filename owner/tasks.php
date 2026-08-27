<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('owner');
require_once __DIR__ . '/../classes/Task.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Crop.php';

$taskModel = new Task();
$userModel = new User();
$cropModel = new Crop();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $crop_id = !empty($_POST['crop_id']) ? $_POST['crop_id'] : null;
    $taskModel->create(
        trim($_POST['title']),
        trim($_POST['description']),
        $_POST['assigned_to'],
        $_SESSION['user_id'],
        $crop_id,
        $_POST['due_date']
    );
    $message = "Task assigned successfully.";
}

if (isset($_GET['delete'])) {
    $taskModel->delete($_GET['delete']);
    header("Location: tasks.php");
    exit;
}

$tasks = $taskModel->getAll();
$workers = $userModel->getAllWorkers();
$crops = $cropModel->getAll();

$pageTitle = "Manage Tasks";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Task &amp; Team Management</h1>
<?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

<div class="card">
    <h2>Assign a New Task</h2>
    <?php if (empty($workers)): ?>
        <p>No farm workers registered yet. Ask a worker to register first.</p>
    <?php else: ?>
    <form method="POST" class="stacked-form">
        <label>Task Title</label>
        <input type="text" name="title" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Assign To</label>
        <select name="assigned_to" required>
            <?php foreach ($workers as $w): ?>
                <option value="<?php echo $w['id']; ?>"><?php echo htmlspecialchars($w['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Related Crop (optional)</label>
        <select name="crop_id">
            <option value="">-- None --</option>
            <?php foreach ($crops as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Due Date</label>
        <input type="date" name="due_date" required>

        <button type="submit" name="add_task" class="btn">Assign Task</button>
    </form>
    <?php endif; ?>
</div>

<div class="card">
    <h2>All Tasks</h2>
    <table class="data-table">
        <tr><th>Title</th><th>Worker</th><th>Crop</th><th>Due Date</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($tasks as $t): ?>
        <tr>
            <td><?php echo htmlspecialchars($t['title']); ?></td>
            <td><?php echo htmlspecialchars($t['worker_name']); ?></td>
            <td><?php echo htmlspecialchars($t['crop_name'] ?? '-'); ?></td>
            <td><?php echo $t['due_date']; ?></td>
            <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo str_replace('_',' ',$t['status']); ?></span></td>
            <td><a href="?delete=<?php echo $t['id']; ?>" onclick="return confirm('Delete this task?')" class="link-danger">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
