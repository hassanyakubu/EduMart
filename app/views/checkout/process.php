<?php
require_once __DIR__ . '/../../controllers/checkout_controller.php';

$controller = new checkout_controller();
$controller->processPayment();
?>
