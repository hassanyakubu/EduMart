<?php
/**
 * Diagnose Quiz Access Issues
 * Run this to see why students can't access quizzes
 */

session_start();
require_once 'app/config/database.php';

echo "<!DOCTYPE html><html><head><title>Quiz Access Diagnostic</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h1 { color: #dc2626; }
h2 { color: #374151; margin-top: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { padding: 12px; text-align: left; border: 1px solid #e5e7eb; }
th { background: #f9fafb; font-weight: 600; }
.success { color: #059669; font-weight: 600; }
.error { color: #dc2626; font-weight: 600; }
.warning { color: #f59e0b; font-weight: 600; }
.info { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; }
.code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 4px; overflow-x: auto; font-family: monospace; margin: 10px 0; }
</style></head><body><div class='container'>";

echo "<h1>🔍 Quiz Access Diagnostic Tool</h1>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Get all students
    echo "<h2>1. Students in System</h2>";
    $result = $db->query("SELECT customer_id, customer_name, customer_email, user_type FROM customer WHERE user_type = 'student' ORDER BY customer_id");
    
    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Purchases</th><th>Categories Purchased</th></tr>";
        while ($student = $result->fetch_assoc()) {
            $student_id = $student['customer_id'];
            
            // Count purchases
            $purchase_result = $db->query("SELECT COUNT(*) as count FROM purchases WHERE customer_id = $student_id");
            $purchase_count = $purchase_result->fetch_assoc()['count'];
            
            // Get purchased categories
            $cat_result = $db->query("
                SELECT DISTINCT c.cat_name 
                FROM order_items oi
                JOIN resources r ON oi.resource_id = r.resource_id
                JOIN purchases p ON oi.purchase_id = p.purchase_id
                JOIN categories c ON r.cat_id = c.cat_id
                WHERE p.customer_id = $student_id
            ");
            
            $categories = [];
            while ($cat = $cat_result->fetch_assoc()) {
                $categories[] = $cat['cat_name'];
            }
            $cat_list = count($categories) > 0 ? implode(', ', $categories) : '<span class="warning">None</span>';
            
            echo "<tr>";
            echo "<td>{$student['customer_id']}</td>";
            echo "<td>{$student['customer_name']}</td>";
            echo "<td>{$student['customer_email']}</td>";
            echo "<td>$purchase_count</td>";
            echo "<td>$cat_list</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>No students found in the system.</p>";
    }
    
    // Check purchases and order_items relationship
    echo "<h2>2. Recent Purchases & Order Items</h2>";
    $result = $db->query("
        SELECT 
            p.purchase_id,
            p.customer_id,
            c.customer_name,
            p.invoice_no,
            p.purchase_date,
            p.order_status,
            COUNT(oi.order_item_id) as item_count
        FROM purchases p
        JOIN customer c ON p.customer_id = c.customer_id
        LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
        GROUP BY p.purchase_id
        ORDER BY p.purchase_id DESC
        LIMIT 10
    ");
    
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Purchase ID</th><th>Customer</th><th>Invoice</th><th>Date</th><th>Status</th><th>Items</th><th>Details</th></tr>";
        while ($purchase = $result->fetch_assoc()) {
            $item_count = $purchase['item_count'];
            $item_status = $item_count > 0 ? "<span class='success'>$item_count items</span>" : "<span class='error'>0 items (PROBLEM!)</span>";
            
            // Get order items details
            $items_result = $db->query("
                SELECT r.resource_title, r.resource_price, c.cat_name
                FROM order_items oi
                JOIN resources r ON oi.resource_id = r.resource_id
                JOIN categories c ON r.cat_id = c.cat_id
                WHERE oi.purchase_id = {$purchase['purchase_id']}
            ");
            
            $items_detail = [];
            while ($item = $items_result->fetch_assoc()) {
                $items_detail[] = "{$item['resource_title']} ({$item['cat_name']})";
            }
            $details = count($items_detail) > 0 ? implode('<br>', $items_detail) : '<span class="error">No items!</span>';
            
            echo "<tr>";
            echo "<td>{$purchase['purchase_id']}</td>";
            echo "<td>{$purchase['customer_name']}</td>";
            echo "<td>{$purchase['invoice_no']}</td>";
            echo "<td>{$purchase['purchase_date']}</td>";
            echo "<td>{$purchase['order_status']}</td>";
            echo "<td>$item_status</td>";
            echo "<td>$details</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>No purchases found.</p>";
    }
    
    // Check quizzes
    echo "<h2>3. Published Quizzes</h2>";
    $result = $db->query("
        SELECT q.quiz_id, q.quiz_title, q.category_id, c.cat_name, q.is_published,
               (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.quiz_id) as question_count
        FROM quizzes q
        LEFT JOIN categories c ON q.category_id = c.cat_id
        ORDER BY q.quiz_id DESC
    ");
    
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Quiz ID</th><th>Title</th><th>Category</th><th>Published</th><th>Questions</th><th>Who Can Access</th></tr>";
        while ($quiz = $result->fetch_assoc()) {
            $published = $quiz['is_published'] ? "<span class='success'>Yes</span>" : "<span class='warning'>No</span>";
            $cat_name = $quiz['cat_name'] ?? '<span class="error">No category!</span>';
            
            // Find students who can access this quiz
            if ($quiz['category_id']) {
                $access_result = $db->query("
                    SELECT DISTINCT c.customer_name
                    FROM order_items oi
                    JOIN resources r ON oi.resource_id = r.resource_id
                    JOIN purchases p ON oi.purchase_id = p.purchase_id
                    JOIN customer c ON p.customer_id = c.customer_id
                    WHERE r.cat_id = {$quiz['category_id']}
                ");
                
                $students = [];
                while ($student = $access_result->fetch_assoc()) {
                    $students[] = $student['customer_name'];
                }
                $access_list = count($students) > 0 ? implode(', ', $students) : '<span class="warning">No one yet</span>';
            } else {
                $access_list = '<span class="error">No category set!</span>';
            }
            
            echo "<tr>";
            echo "<td>{$quiz['quiz_id']}</td>";
            echo "<td>{$quiz['quiz_title']}</td>";
            echo "<td>$cat_name</td>";
            echo "<td>$published</td>";
            echo "<td>{$quiz['question_count']}</td>";
            echo "<td>$access_list</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>No quizzes found.</p>";
    }
    
    // Diagnosis summary
    echo "<h2>4. Diagnosis Summary</h2>";
    
    // Check for purchases without order_items
    $orphan_result = $db->query("
        SELECT COUNT(*) as count 
        FROM purchases p 
        LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id 
        WHERE oi.order_item_id IS NULL
    ");
    $orphan_count = $orphan_result->fetch_assoc()['count'];
    
    if ($orphan_count > 0) {
        echo "<div class='info'>";
        echo "<strong class='error'>⚠️ PROBLEM FOUND:</strong><br>";
        echo "There are <strong>$orphan_count purchases without order_items</strong>. This means:<br>";
        echo "• Students who made these purchases cannot access quizzes<br>";
        echo "• Analytics won't show these sales<br>";
        echo "• Creators won't see earnings from these purchases<br><br>";
        echo "<strong>Solution:</strong> The payment system needs to create order_items when processing payments.<br>";
        echo "The fix has been applied in <code>controllers/order_controller.php</code> - test with a new payment.";
        echo "</div>";
    } else {
        echo "<div class='info'>";
        echo "<strong class='success'>✓ All purchases have order_items</strong><br>";
        echo "The payment system is working correctly!";
        echo "</div>";
    }
    
    // Check for unpublished quizzes
    $unpublished_result = $db->query("SELECT COUNT(*) as count FROM quizzes WHERE is_published = 0");
    $unpublished_count = $unpublished_result->fetch_assoc()['count'];
    
    if ($unpublished_count > 0) {
        echo "<div class='info'>";
        echo "<strong class='warning'>ℹ️ NOTE:</strong><br>";
        echo "There are <strong>$unpublished_count unpublished quizzes</strong>.<br>";
        echo "Students cannot see unpublished quizzes even if they purchased the category.<br>";
        echo "Creators need to publish their quizzes for students to access them.";
        echo "</div>";
    }
    
    // Show the quiz access query
    echo "<h2>5. Quiz Access Query (for developers)</h2>";
    echo "<p>This is the query used to determine which quizzes a student can access:</p>";
    echo "<div class='code'>";
    echo "SELECT DISTINCT q.*, c.customer_name as creator_name, cat.cat_name as category_name<br>";
    echo "FROM quizzes q<br>";
    echo "JOIN customer c ON q.user_id = c.customer_id<br>";
    echo "JOIN categories cat ON q.category_id = cat.cat_id<br>";
    echo "WHERE q.is_published = 1<br>";
    echo "AND q.category_id IN (<br>";
    echo "&nbsp;&nbsp;SELECT DISTINCT r.cat_id<br>";
    echo "&nbsp;&nbsp;FROM order_items oi<br>";
    echo "&nbsp;&nbsp;JOIN resources r ON oi.resource_id = r.resource_id<br>";
    echo "&nbsp;&nbsp;JOIN purchases p ON oi.purchase_id = p.purchase_id<br>";
    echo "&nbsp;&nbsp;WHERE p.customer_id = ?<br>";
    echo ")<br>";
    echo "ORDER BY q.created_at DESC";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>Database Error: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";
?>
