<?php
/**
 * Paystack Callback Handler
 * This page is called by Paystack after payment
 */
session_start();
require_once __DIR__ . '/../../controllers/checkout_controller.php';

$controller = new checkout_controller();
$controller->callback();
?>
