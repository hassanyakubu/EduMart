<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Invalid user ID.';
    header('Location: ' . url('app/views/admin/dashboard.php'));
    exit;
}

$user_id = $_GET['id'];

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = 'You cannot delete your own account!';
    header('Location: ' . url('app/views/admin/dashboard.php'));
    exit;
}

$controller = new admin_controller();
if ($controller->deleteUser($user_id)) {
    $_SESSION['success'] = 'User deleted successfully!';
} else {
    $_SESSION['error'] = 'Failed to delete user.';
}

header('Location: ' . $_SERVER['HTTP_REFERER'] ?? url('app/views/admin/dashboard.php'));
exit;
?>
