<?php
/**
 * Base URL Configuration
 * Auto-detects server or local environment
 */

// Check if running on server
$isServer = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '169.239.251.102') !== false);

if ($isServer) {
    // Server configuration
    define('BASE_URL', 'http://169.239.251.102:442/~hassan.yakubu');
    define('BASE_PATH', '/~hassan.yakubu');
} else {
    // Local configuration
    define('BASE_URL', 'http://localhost:8000');
    define('BASE_PATH', '');
}

/**
 * Helper function to generate URLs
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Helper function for assets
 */
function asset($path = '') {
    return BASE_URL . '/public/assets/' . ltrim($path, '/');
}

/**
 * Helper function for views
 */
function view_url($path = '') {
    return BASE_URL . '/app/views/' . ltrim($path, '/');
}
?>
