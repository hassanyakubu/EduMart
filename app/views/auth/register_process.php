<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/../../controllers/auth_controller.php';

    $controller = new auth_controller();
    $controller->register();
} catch (Exception $e) {
    die("Registration error: " . $e->getMessage() . "<br>File: " . $e->getFile() . "<br>Line: " . $e->getLine());
}
?>
