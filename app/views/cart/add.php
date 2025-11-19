<?php
require_once __DIR__ . '/../../controllers/cart_controller.php';

if (!isset($_GET['id'])) {
    header('Location: /app/views/resources/list.php');
    exit;
}

$controller = new cart_controller();
$controller->add($_GET['id']);
?>
