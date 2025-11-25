<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/cart_model.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to add items to your cart.';
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'Invalid resource ID.';
    header('Location: ' . url('app/views/resources/list.php'));
    exit;
}

$cartModel = new cart_model();
$resource_id = intval($_GET['id']);

if ($cartModel->addItem($_SESSION['user_id'], $resource_id, 1)) {
    $_SESSION['success'] = 'Resource added to cart!';
    $_SESSION['cart_updated'] = true; // Flag for cart badge update
} else {
    $_SESSION['error'] = 'Failed to add resource to cart.';
}

// Redirect back to the previous page or to resources list
$redirect_url = $_SERVER['HTTP_REFERER'] ?? url('app/views/resources/list.php');
header('Location: ' . $redirect_url);
exit;
?>
