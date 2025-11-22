<?php
// Script to fix upload directory permissions

$base_dir = __DIR__ . '/public/uploads';
$dirs = [
    $base_dir,
    $base_dir . '/files',
    $base_dir . '/images'
];

echo "<h2>Fixing Upload Directory Permissions</h2>";
echo "<pre>";

foreach ($dirs as $dir) {
    echo "\nDirectory: $dir\n";
    
    if (!is_dir($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "✅ Created directory\n";
        } else {
            echo "❌ Failed to create directory\n";
        }
    } else {
        echo "✅ Directory exists\n";
    }
    
    if (chmod($dir, 0777)) {
        echo "✅ Set permissions to 777\n";
    } else {
        echo "❌ Failed to set permissions\n";
    }
    
    echo "Writable: " . (is_writable($dir) ? "YES ✅" : "NO ❌") . "\n";
}

echo "\n\n=== Testing File Creation ===\n";
$test_file = $base_dir . '/files/test.txt';
if (file_put_contents($test_file, 'test')) {
    echo "✅ Successfully created test file: $test_file\n";
    unlink($test_file);
    echo "✅ Test file deleted\n";
} else {
    echo "❌ Failed to create test file\n";
}

echo "\n\n=== Summary ===\n";
echo "If you see ❌ errors above, you need to run this command on the server:\n";
echo "chmod 777 ~/public_html/EduMart/public/uploads -R\n";

echo "</pre>";

echo '<br><a href="test_upload.php" style="padding: 1rem 2rem; background: #FFD947; color: #333; text-decoration: none; border-radius: 8px; display: inline-block;">Test Upload Again</a>';
?>
