<?php
/**
 * Quiz Feature Setup Test
 * Run this file to verify the quiz feature is properly installed
 */

require_once __DIR__ . '/app/config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Quiz Feature Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Quiz Feature Setup Test</h1>
    <p>Testing quiz feature installation on server: <strong>http://169.239.251.102:442/~hassan.yakubu/EduMart</strong></p>
";

$conn = Database::getInstance()->getConnection();
$allPassed = true;

// Test 1: Database Connection
echo "<div class='test-section'>";
echo "<h2>1. Database Connection</h2>";
if ($conn) {
    echo "<p class='success'>✅ Database connection successful</p>";
} else {
    echo "<p class='error'>❌ Database connection failed</p>";
    $allPassed = false;
}
echo "</div>";

// Test 2: Check Tables
echo "<div class='test-section'>";
echo "<h2>2. Database Tables</h2>";
$tables = ['quizzes', 'quiz_questions', 'quiz_attempts', 'quiz_answers'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p class='success'>✅ Table '$table' exists</p>";
    } else {
        echo "<p class='error'>❌ Table '$table' does not exist</p>";
        echo "<p class='info'>Run: php install_quiz_feature.php</p>";
        $allPassed = false;
    }
}
echo "</div>";

// Test 3: Upload Directory
echo "<div class='test-section'>";
echo "<h2>3. Upload Directory</h2>";
$upload_dir = __DIR__ . '/public/uploads/quiz_resources/';
if (is_dir($upload_dir)) {
    echo "<p class='success'>✅ Upload directory exists</p>";
    if (is_writable($upload_dir)) {
        echo "<p class='success'>✅ Upload directory is writable</p>";
    } else {
        echo "<p class='error'>❌ Upload directory is NOT writable</p>";
        echo "<p class='info'>Run: chmod -R 777 public/uploads/quiz_resources</p>";
        $allPassed = false;
    }
} else {
    echo "<p class='error'>❌ Upload directory does not exist</p>";
    echo "<p class='info'>Run: mkdir -p public/uploads/quiz_resources && chmod -R 777 public/uploads/quiz_resources</p>";
    $allPassed = false;
}
echo "</div>";

// Test 4: Required Files
echo "<div class='test-section'>";
echo "<h2>4. Required Files</h2>";
$files = [
    'app/models/quiz_model.php',
    'app/controllers/quiz_controller.php',
    'app/views/quiz/upload.php',
    'app/views/quiz/list.php',
    'app/views/quiz/take.php',
    'app/views/quiz/results.php',
    'app/views/quiz/submit.php',
    'app/views/quiz/process_upload.php'
];

foreach ($files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "<p class='success'>✅ $file exists</p>";
    } else {
        echo "<p class='error'>❌ $file is missing</p>";
        $allPassed = false;
    }
}
echo "</div>";

// Test 5: PHP Configuration
echo "<div class='test-section'>";
echo "<h2>5. PHP Configuration</h2>";
$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
echo "<p class='info'>Upload Max Filesize: $upload_max</p>";
echo "<p class='info'>Post Max Size: $post_max</p>";
if (intval($upload_max) >= 10) {
    echo "<p class='success'>✅ Upload size is adequate</p>";
} else {
    echo "<p class='error'>⚠️ Upload size might be too small for large files</p>";
}
echo "</div>";

// Final Summary
echo "<div class='test-section'>";
echo "<h2>📊 Test Summary</h2>";
if ($allPassed) {
    echo "<p class='success'>✅ All tests passed! Quiz feature is ready to use.</p>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/login.php'>Login to your account</a></li>";
    echo "<li><a href='http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/quiz/list.php'>Browse Quizzes</a></li>";
    echo "<li><a href='http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/quiz/upload.php'>Create a Quiz</a></li>";
    echo "<li><a href='http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/profile/dashboard.php'>View Your Dashboard</a></li>";
    echo "</ol>";
    echo "<p class='info'><strong>Remember to delete this test file after verification!</strong></p>";
} else {
    echo "<p class='error'>❌ Some tests failed. Please fix the issues above.</p>";
    echo "<p class='info'>Run: bash setup_quiz.sh</p>";
}
echo "</div>";

echo "</body></html>";
?>
