<?php
require_once __DIR__ . '/../../controllers/cart_controller.php';

if (!isset($_GET['id'])) {
    header('Location: /app/views/cart/view.php');
    exit;
}

$controller = new cart_controller();
$controller->remove($_GET['id']);
?>
