<?php
// Base configuration for EduMart

// Base URL for the application (subdirectory deployment)
define('BASE_URL', '/~hassan.yakubu/EduMart');

// Asset paths
define('CSS_PATH', BASE_URL . '/public/assets/css');
define('JS_PATH', BASE_URL . '/public/assets/js');
define('IMG_PATH', BASE_URL . '/public/assets/images');

// Application paths
define('APP_PATH', BASE_URL . '/app');
define('VIEW_PATH', APP_PATH . '/views');

// Helper function to generate URLs
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Helper function for asset URLs
function asset($path = '') {
    return BASE_URL . '/public/' . ltrim($path, '/');
}
?>
