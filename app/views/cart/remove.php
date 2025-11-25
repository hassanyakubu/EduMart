<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/cart_model.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to manage your cart.';
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ' . url('app/views/cart/view.php'));
    exit;
}

$cartModel = new cart_model();
$resource_id = intval($_GET['id']);

if ($cartModel->removeItem($_SESSION['user_id'], $resource_id)) {
    $_SESSION['success'] = 'Item removed from cart.';
} else {
    $_SESSION['error'] = 'Failed to remove item from cart.';
}

header('Location: ' . url('app/views/cart/view.php'));
exit;
?>
