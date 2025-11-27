<?php
// Test Quiz Access - Run this to debug quiz visibility
session_start();
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/models/quiz_model.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first");
}

$user_id = $_SESSION['user_id'];
$quizModel = new quiz_model();

echo "<h2>Quiz Access Debug for User ID: $user_id</h2>";

// Check what categories user has purchased
$conn = Database::getInstance()->getConnection();
$stmt = $conn->prepare("SELECT DISTINCT r.cat_id, c.cat_name, COUNT(*) as purchase_count
                        FROM order_items oi
                        JOIN resources r ON oi.resource_id = r.resource_id
                        JOIN purchases p ON oi.purchase_id = p.purchase_id
                        JOIN categories c ON r.cat_id = c.cat_id
                        WHERE p.customer_id = ?
                        GROUP BY r.cat_id");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$purchased_categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo "<h3>Categories You've Purchased:</h3>";
if (empty($purchased_categories)) {
    echo "<p style='color: red;'>No purchases found! You need to purchase resources first.</p>";
} else {
    echo "<ul>";
    foreach ($purchased_categories as $cat) {
        echo "<li>Category ID: {$cat['cat_id']} - {$cat['cat_name']} ({$cat['purchase_count']} items)</li>";
    }
    echo "</ul>";
}

// Check available quizzes
$all_quizzes = $conn->query("SELECT q.quiz_id, q.quiz_title, q.category_id, c.cat_name, q.is_published
                             FROM quizzes q
                             LEFT JOIN categories c ON q.category_id = c.cat_id
                             ORDER BY q.quiz_id");
$quizzes = $all_quizzes->fetch_all(MYSQLI_ASSOC);

echo "<h3>All Quizzes in Database:</h3>";
if (empty($quizzes)) {
    echo "<p style='color: red;'>No quizzes exist yet!</p>";
} else {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Quiz ID</th><th>Title</th><th>Category ID</th><th>Category Name</th><th>Published</th><th>Can Access?</th></tr>";
    foreach ($quizzes as $quiz) {
        $can_access = false;
        foreach ($purchased_categories as $cat) {
            if ($cat['cat_id'] == $quiz['category_id']) {
                $can_access = true;
                break;
            }
        }
        $access_text = $can_access ? "<span style='color: green;'>YES</span>" : "<span style='color: red;'>NO</span>";
        $published = $quiz['is_published'] ? "Yes" : "No";
        echo "<tr><td>{$quiz['quiz_id']}</td><td>{$quiz['quiz_title']}</td><td>{$quiz['category_id']}</td><td>{$quiz['cat_name']}</td><td>$published</td><td>$access_text</td></tr>";
    }
    echo "</table>";
}

// Check what quizzes the model returns
echo "<h3>Quizzes Returned by getQuizzesForStudent():</h3>";
$student_quizzes = $quizModel->getQuizzesForStudent($user_id);
if (empty($student_quizzes)) {
    echo "<p style='color: red;'>No quizzes returned! This is the problem.</p>";
} else {
    echo "<ul>";
    foreach ($student_quizzes as $quiz) {
        echo "<li>Quiz ID: {$quiz['quiz_id']} - {$quiz['quiz_title']} (Category: {$quiz['category_name']})</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='app/views/quiz/list.php'>Go to Quiz List</a></p>";
?>
