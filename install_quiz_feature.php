<?php
/**
 * Quiz Feature Installation Script
 * Run this file once to set up the quiz database tables
 */

require_once __DIR__ . '/app/config/database.php';

echo "Installing Quiz Feature...\n\n";

$conn = Database::getInstance()->getConnection();

// Read SQL file
$sql = file_get_contents(__DIR__ . '/db/quiz_tables.sql');

// Split into individual queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

$success = true;
foreach ($queries as $query) {
    if (empty($query)) continue;
    
    echo "Executing: " . substr($query, 0, 50) . "...\n";
    
    if (!$conn->query($query)) {
        echo "ERROR: " . $conn->error . "\n";
        $success = false;
    } else {
        echo "✓ Success\n";
    }
}

if ($success) {
    echo "\n✅ Quiz feature installed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Create upload directory: mkdir -p public/uploads/quiz_resources\n";
    echo "2. Set permissions: chmod -R 777 public/uploads/quiz_resources\n";
    echo "3. Access quizzes at: http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/quiz/list.php\n";
    echo "\nOr run: bash setup_quiz.sh\n";
} else {
    echo "\n❌ Installation failed. Please check the errors above.\n";
}
?>
