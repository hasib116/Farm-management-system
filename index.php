<?php
if (session_status() === PHP_SESSION_NONE) session_start();


if (isset($_SESSION['role'])) {
    header("Location: /smartfarm/" . $_SESSION['role'] . "/dashboard.php");
    exit;
}

$pageTitle = "Welcome";
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <h1> SmartFarm</h1>
    <p class="tagline">A Web Based Farm Management &amp; Farmer-to-Buyer Marketplace System</p>
    <p>Digitize crop planning, task management, inventory, harvest tracking, and direct-to-consumer sales — all in one platform.</p>
    <div class="hero-actions">
        <a href="/smartfarm/auth/login.php" class="btn">Login</a>
        <a href="/smartfarm/auth/register.php" class="btn btn-outline">Register</a>
    </div>
</section>

<section class="feature-grid">
    <div class="feature-card">
        <h3> Farm Owner</h3>
        <p>Assign tasks, manage inventory, list products, and view financial &amp; yield reports.</p>
    </div>
    <div class="feature-card">
        <h3> Farm Worker</h3>
        <p>View daily tasks, log field data, and record harvests directly from the field.</p>
    </div>
    <div class="feature-card">
        <h3> Buyer</h3>
        <p>Browse fresh produce, place orders, pay online, and track deliveries in real time.</p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>