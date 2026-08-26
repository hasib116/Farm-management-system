<?php
require_once __DIR__ . '/../classes/User.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = $_POST['role'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $errors[] = "Please fill in all required fields.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    if (!in_array($role, ['owner', 'worker', 'buyer'])) {
        $errors[] = "Invalid user type selected.";
    }

    if (empty($errors)) {
        $userModel = new User();
        $result = $userModel->register($name, $email, $password, $role, $phone, $address);
        if ($result['success']) {
            header("Location: /smartfarm/auth/login.php?msg=registered");
            exit;
        } else {
            $errors[] = $result['message'];
        }
    }
}

$pageTitle = "Register";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-box">
        <h1>Create your SmartFarm account</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label>Full Name *</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

            <label>Email *</label>
            <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">

            <label>Address</label>
            <textarea name="address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>

            <label>I am registering as *</label>
            <select name="role" required>
                <option value="">-- Select User Type --</option>
                <option value="owner">Farm Owner</option>
                <option value="worker">Farm Worker</option>
                <option value="buyer">Buyer</option>
            </select>

            <label>Password *</label>
            <input type="password" name="password" required minlength="6">

            <label>Confirm Password *</label>
            <input type="password" name="confirm_password" required minlength="6">

            <button type="submit" class="btn btn-block">Register</button>
        </form>

        <p class="auth-switch">Already have an account? <a href="/smartfarm/auth/login.php">Login here</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>