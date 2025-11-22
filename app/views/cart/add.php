<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/cart_controller.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to add items to your cart.';
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ' . url('app/views/resources/list.php'));
    exit;
}

$controller = new cart_controller();
$controller->add($_GET['id']);
?>
