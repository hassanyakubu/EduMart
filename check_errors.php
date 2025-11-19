<?php
/**
 * Error Checker - Shows PHP errors
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Error Checker</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Check if test_server.php has syntax errors
echo "<h2>Checking test_server.php...</h2>";

if (file_exists('test_server.php')) {
    $output = [];
    $return_var = 0;
    exec('php -l test_server.php 2>&1', $output, $return_var);
    
    if ($return_var === 0) {
        echo "<p style='color: green;'>✅ test_server.php has no syntax errors</p>";
    } else {
        echo "<p style='color: red;'>❌ test_server.php has syntax errors:</p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ test_server.php not found</p>";
}

// Try to include test_server.php and catch errors
echo "<h2>Trying to load test_server.php...</h2>";
try {
    ob_start();
    include 'test_server.php';
    $content = ob_get_clean();
    echo "<p style='color: green;'>✅ test_server.php loaded successfully</p>";
} catch (Exception $e) {
    ob_end_clean();
    echo "<p style='color: red;'>❌ Error loading test_server.php:</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

echo "<hr>";
echo "<p><strong>If you see errors above, that's what's causing the 500 error.</strong></p>";
echo "<p><a href='test_simple.php'>Try Simple Test Instead</a></p>";
?>
