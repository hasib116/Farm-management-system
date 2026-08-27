<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('worker');
require_once __DIR__ . '/../classes/Task.php';

$taskModel = new Task();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $taskModel->updateStatus($_POST['task_id'], $_POST['status'], $_SESSION['user_id']);
    $message = "Task status updated.";
}

$tasks = $taskModel->getByWorker($_SESSION['user_id']);
$pageTitle = "My Tasks";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>My Assigned Tasks</h1>
<?php if ($message): ?><div class="alert"><?php echo $message; ?></div><?php endif; ?>

<div class="card">
    <table class="data-table">
        <tr><th>Title</th><th>Description</th><th>Crop</th><th>Due Date</th><th>Status</th><th>Update</th></tr>
        <?php foreach ($tasks as $t): ?>
        <tr>
            <td><?php echo htmlspecialchars($t['title']); ?></td>
            <td><?php echo htmlspecialchars($t['description']); ?></td>
            <td><?php echo htmlspecialchars($t['crop_name'] ?? '-'); ?></td>
            <td><?php echo $t['due_date']; ?></td>
            <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo str_replace('_',' ',$t['status']); ?></span></td>
            <td>
                <form method="POST" class="tiny-inline-form">
                    <input type="hidden" name="task_id" value="<?php echo $t['id']; ?>">
                    <select name="status">
                        <option value="pending" <?php echo $t['status']=='pending'?'selected':''; ?>>Pending</option>
                        <option value="in_progress" <?php echo $t['status']=='in_progress'?'selected':''; ?>>In Progress</option>
                        <option value="completed" <?php echo $t['status']=='completed'?'selected':''; ?>>Completed</option>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-small">Save</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
