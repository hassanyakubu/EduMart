<?php
/**
 * Quiz Feature Database Installation
 * This script will create all quiz tables on your server database
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Quiz Database Installation</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .info { color: #0c5460; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        .warning { color: #856404; padding: 10px; background: #fff3cd; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
        h1 { color: #333; border-bottom: 3px solid #FFD947; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 30px; }
        .query-box { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; font-family: monospace; font-size: 12px; overflow-x: auto; }
        .step { background: #e9ecef; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #FFD947; }
        .btn { display: inline-block; padding: 10px 20px; background: #FFD947; color: #333; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: bold; }
        .btn:hover { background: #ffd000; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🎓 Quiz Feature Database Installation</h1>
        <p><strong>Server:</strong> http://169.239.251.102:442/~hassan.yakubu/EduMart</p>
        <p><strong>Database:</strong> ecommerce_2025A_hassan_yakubu</p>
        <hr>
";

// Include database credentials
require_once __DIR__ . '/settings/db_cred.php';

// Connect to database
$conn = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);

if ($conn->connect_error) {
    echo "<div class='error'>❌ <strong>Connection Failed:</strong> " . $conn->connect_error . "</div>";
    echo "<div class='info'>Please check your database credentials in settings/db_cred.php</div>";
    echo "</div></body></html>";
    exit;
}

echo "<div class='success'>✅ <strong>Database Connected Successfully!</strong></div>";
echo "<div class='info'>Connected to: <strong>" . DATABASE . "</strong> as <strong>" . USERNAME . "</strong></div>";

// SQL queries to create tables
$queries = [
    'quizzes' => "CREATE TABLE IF NOT EXISTS quizzes (
        quiz_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        quiz_title VARCHAR(255) NOT NULL,
        resource_filename VARCHAR(255) NOT NULL,
        resource_path VARCHAR(500) NOT NULL,
        time_limit INT NOT NULL COMMENT 'Time limit in minutes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES customer(customer_id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'quiz_questions' => "CREATE TABLE IF NOT EXISTS quiz_questions (
        question_id INT PRIMARY KEY AUTO_INCREMENT,
        quiz_id INT NOT NULL,
        question_text TEXT NOT NULL,
        option_a VARCHAR(500) NOT NULL,
        option_b VARCHAR(500) NOT NULL,
        option_c VARCHAR(500) NOT NULL,
        option_d VARCHAR(500) NOT NULL,
        correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
        FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE,
        INDEX idx_quiz_id (quiz_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'quiz_attempts' => "CREATE TABLE IF NOT EXISTS quiz_attempts (
        attempt_id INT PRIMARY KEY AUTO_INCREMENT,
        quiz_id INT NOT NULL,
        user_id INT NOT NULL,
        score INT NOT NULL,
        total_questions INT NOT NULL,
        time_taken INT COMMENT 'Time taken in seconds',
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES customer(customer_id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_quiz_id (quiz_id),
        INDEX idx_completed_at (completed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    'quiz_answers' => "CREATE TABLE IF NOT EXISTS quiz_answers (
        answer_id INT PRIMARY KEY AUTO_INCREMENT,
        attempt_id INT NOT NULL,
        question_id INT NOT NULL,
        user_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
        is_correct BOOLEAN NOT NULL,
        FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(attempt_id) ON DELETE CASCADE,
        FOREIGN KEY (question_id) REFERENCES quiz_questions(question_id) ON DELETE CASCADE,
        INDEX idx_attempt_id (attempt_id),
        INDEX idx_question_id (question_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

echo "<h2>📊 Creating Tables...</h2>";

$success_count = 0;
$error_count = 0;

foreach ($queries as $table_name => $query) {
    echo "<div class='step'>";
    echo "<strong>Creating table: $table_name</strong><br>";
    
    if ($conn->query($query)) {
        echo "<div class='success'>✅ Table '$table_name' created successfully!</div>";
        $success_count++;
    } else {
        echo "<div class='error'>❌ Error creating '$table_name': " . $conn->error . "</div>";
        $error_count++;
    }
    echo "</div>";
}

// Verify tables were created
echo "<h2>🔍 Verification</h2>";
$result = $conn->query("SHOW TABLES LIKE 'quiz%'");

if ($result && $result->num_rows > 0) {
    echo "<div class='success'>✅ Found " . $result->num_rows . " quiz tables:</div>";
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li><strong>" . $row[0] . "</strong></li>";
    }
    echo "</ul>";
} else {
    echo "<div class='error'>❌ No quiz tables found!</div>";
}

// Check table structures
echo "<h2>📋 Table Structures</h2>";
$tables = ['quizzes', 'quiz_questions', 'quiz_attempts', 'quiz_answers'];

foreach ($tables as $table) {
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        echo "<div class='step'>";
        echo "<strong>Table: $table</strong>";
        echo "<div class='query-box'>";
        echo "<table style='width:100%; border-collapse: collapse;'>";
        echo "<tr style='background:#e9ecef;'><th style='padding:8px; text-align:left;'>Field</th><th style='padding:8px; text-align:left;'>Type</th><th style='padding:8px; text-align:left;'>Key</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding:5px;'>" . $row['Field'] . "</td>";
            echo "<td style='padding:5px;'>" . $row['Type'] . "</td>";
            echo "<td style='padding:5px;'>" . $row['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        echo "</div>";
    }
}

// Count records
echo "<h2>📈 Record Counts</h2>";
echo "<div class='info'>";
echo "<table style='width:100%;'>";
foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<tr><td><strong>$table:</strong></td><td>" . $row['count'] . " records</td></tr>";
    }
}
echo "</table>";
echo "</div>";

// Final summary
echo "<h2>🎉 Installation Summary</h2>";

if ($error_count == 0) {
    echo "<div class='success'>";
    echo "<h3>✅ Installation Successful!</h3>";
    echo "<p>All $success_count tables were created successfully.</p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>📝 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Create upload directory: <code>mkdir -p public/uploads/quiz_resources && chmod -R 777 public/uploads/quiz_resources</code></li>";
    echo "<li><a href='http://169.239.251.102:442/~hassan.yakubu/EduMart/test_quiz_setup.php' class='btn'>Run Full Test</a></li>";
    echo "<li><a href='http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/quiz/list.php' class='btn'>View Quizzes</a></li>";
    echo "<li><a href='http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/quiz/upload.php' class='btn'>Create Quiz</a></li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='warning'>";
    echo "<strong>⚠️ Security Note:</strong> Delete this file after installation:<br>";
    echo "<code>rm run_quiz_installation.php</code>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "<h3>❌ Installation Failed</h3>";
    echo "<p>$error_count error(s) occurred. Please check the errors above and try again.</p>";
    echo "</div>";
}

$conn->close();

echo "</div></body></html>";
?>
