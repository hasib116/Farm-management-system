<?php
require_once __DIR__ . '/auth_check.php';
requireLogin();
require_once __DIR__ . '/../classes/User.php';

$userModel = new User();
$message = "";

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($userModel->updateProfile($_SESSION['user_id'], $name, $phone, $address)) {
        $_SESSION['name'] = $name;
        $message = "Profile updated successfully.";
    } else {
        $message = "Failed to update profile.";
    }
}

// Change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $newPassword = $_POST['new_password'];
    if (strlen($newPassword) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {
        $userModel->changePassword($_SESSION['user_id'], $newPassword);
        $message = "Password changed successfully.";
    }
}

// Delete account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $userModel->deleteAccount($_SESSION['user_id']);
    session_destroy();
    header("Location: /smartfarm/auth/login.php?msg=account_deleted");
    exit;
}

$user = $userModel->findById($_SESSION['user_id']);
$pageTitle = "My Profile";
require_once __DIR__ . '/header.php';
?>

<h1>My Profile</h1>
<?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

<div class="card-grid">
    <div class="card">
        <h2>Account Information</h2>
        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label>Email (cannot be changed)</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

            <label>Address</label>
            <textarea name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>

            <button type="submit" name="update_profile" class="btn">Save Changes</button>
        </form>
    </div>

    <div class="card">
        <h2>Change Password</h2>
        <form method="POST">
            <label>New Password</label>
            <input type="password" name="new_password" minlength="6" required>
            <button type="submit" name="change_password" class="btn">Update Password</button>
        </form>
    </div>

    <div class="card danger-card">
        <h2>Delete Account</h2>
        <p>This action is permanent and cannot be undone.</p>
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
            <button type="submit" name="delete_account" class="btn btn-danger">Delete My Account</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>