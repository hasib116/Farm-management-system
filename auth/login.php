<?php
require_once __DIR__ . '/../classes/User.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$error = "";
$msg = $_GET['msg'] ?? '';
$msgText = [
    'registered' => 'Registration successful! Please log in.',
    'account_deleted' => 'Your account has been deleted.',
    'loggedout' => 'You have been logged out.',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $userModel = new User();
    $result = $userModel->login($email, $password);

    if ($result['success']) {
        $user = $result['user'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // redirect based on role
        switch ($user['role']) {
            case 'owner':
                header("Location: /smartfarm/owner/dashboard.php");
                break;
            case 'worker':
                header("Location: /smartfarm/worker/dashboard.php");
                break;
            case 'buyer':
                header("Location: /smartfarm/buyer/dashboard.php");
                break;
        }
        exit;
    } else {
        $error = $result['message'];
    }
}

$pageTitle = "Login";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-box">
        <h1>Login to SmartFarm</h1>

        <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($msg && isset($msgText[$msg])): ?><div class="alert"><?php echo $msgText[$msg]; ?></div><?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
            <div class="alert alert-error">You are not authorized to access that page.</div>
        <?php endif; ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required autofocus>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn btn-block">Login</button>
        </form>

        <p class="auth-switch">Don't have an account? <a href="/smartfarm/auth/register.php">Register here</a></p>

        <div class="demo-accounts">
            <strong>Demo accounts</strong> (password: <code>password123</code>)
            <ul>
                <li>Owner: owner@smartfarm.com</li>
                <li>Worker: worker@smartfarm.com</li>
                <li>Buyer: buyer@smartfarm.com</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>