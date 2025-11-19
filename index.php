<?php
/**
 * EduMart - Digital Learning Resources Marketplace
 * Main Entry Point
 */

// Check if running on server or locally
$isServer = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '169.239.251.102') !== false);

if ($isServer) {
    // Server configuration
    define('BASE_URL', 'http://169.239.251.102:442/~hassan.yakubu/EduMart');
    define('BASE_PATH', '/~hassan.yakubu/EduMart');
    header('Location: ' . BASE_URL . '/app/views/home/index.php');
} else {
    // Local configuration
    define('BASE_URL', 'http://localhost:8000');
    define('BASE_PATH', '');
    header('Location: /app/views/home/index.php');
}

exit;
?>
