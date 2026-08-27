<?php
// Starts the session (if not already) and protects a page for a specific role.
// Usage at the top of a page: require_once '../includes/auth_check.php'; requireRole('owner');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /smartfarm/auth/login.php");
        exit;
    }
}

function requireRole($role)
{
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        header("Location: /smartfarm/auth/login.php?error=unauthorized");
        exit;
    }
}
