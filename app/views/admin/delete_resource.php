<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Invalid resource ID.';
    header('Location: ' . url('app/views/admin/resources.php'));
    exit;
}

$resource_id = $_GET['id'];

$controller = new admin_controller();
if ($controller->deleteResource($resource_id)) {
    $_SESSION['success'] = 'Resource deleted successfully!';
} else {
    $_SESSION['error'] = 'Failed to delete resource.';
}

header('Location: ' . url('app/views/admin/resources.php'));
exit;
?>
