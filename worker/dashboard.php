<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('worker');
require_once __DIR__ . '/../classes/Task.php';

$taskModel = new Task();
$myTasks = $taskModel->getByWorker($_SESSION['user_id']);
$pending = array_filter($myTasks, fn($t) => $t['status'] !== 'completed');

$pageTitle = "Worker Dashboard";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Farm Worker Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-value"><?php echo count($myTasks); ?></span>
        <span class="stat-label">Total Assigned Tasks</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?php echo count($pending); ?></span>
        <span class="stat-label">Tasks Pending</span>
    </div>
</div>

<div class="card">
    <h2>Today's / Upcoming Tasks</h2>
    <table class="data-table">
        <tr><th>Title</th><th>Crop</th><th>Due Date</th><th>Status</th></tr>
        <?php foreach (array_slice($myTasks, 0, 5) as $t): ?>
        <tr>
            <td><?php echo htmlspecialchars($t['title']); ?></td>
            <td><?php echo htmlspecialchars($t['crop_name'] ?? '-'); ?></td>
            <td><?php echo $t['due_date']; ?></td>
            <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo str_replace('_',' ',$t['status']); ?></span></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="quick-links">
    <a href="/smartfarm/worker/tasks.php" class="card link-card">📋 My Tasks</a>
    <a href="/smartfarm/worker/field_logs.php" class="card link-card">📝 Field Data Logs</a>
    <a href="/smartfarm/worker/harvest.php" class="card link-card">🌾 Record Harvest</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
