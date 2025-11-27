<?php
/**
 * Debug Quiz Access Issue
 * Run this to find out exactly why quizzes aren't showing
 */

session_start();
require_once __DIR__ . '/app/config/database.php';

echo "<!DOCTYPE html><html><head><title>Quiz Access Debug</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
h1 { color: #dc2626; }
h2 { color: #374151; margin-top: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
.success { color: #059669; font-weight: 600; }
.error { color: #dc2626; font-weight: 600; }
.warning { color: #f59e0b; font-weight: 600; }
.info { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { padding: 12px; text-align: left; border: 1px solid #e5e7eb; }
th { background: #f9fafb; font-weight: 600; }
.code { background: #1f2937; color: #10b981; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
</style></head><body><div class='container'>";

echo "<h1>🔍 Quiz Access Debug Tool</h1>";

// Get current user
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? 'Not logged in';

echo "<div class='info'>";
echo "<strong>Current User:</strong> $user_name (ID: $user_id)<br>";
if (!$user_id) {
    echo "<span class='error'>⚠️ You are not logged in! Please log in first.</span>";
    echo "</div></div></body></html>";
    exit;
}
echo "</div>";

try {
    $db = Database::getInstance()->getConnection();
    
    // CHECK 1: Are there ANY published quizzes?
    echo "<h2>Check 1: Published Quizzes in System</h2>";
    $result = $db->query("
        SELECT q.quiz_id, q.quiz_title, q.category_id, c.cat_name, q.is_published,
               (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.quiz_id) as question_count
        FROM quizzes q
        LEFT JOIN categories c ON q.category_id = c.cat_id
        WHERE q.is_published = 1
        ORDER BY q.quiz_id
    ");
    
    if ($result->num_rows > 0) {
        echo "<p class='success'>✓ Found {$result->num_rows} published quiz(es)</p>";
        echo "<table><tr><th>Quiz ID</th><th>Title</th><th>Category</th><th>Questions</th></tr>";
        $published_quizzes = [];
        while ($quiz = $result->fetch_assoc()) {
            $published_quizzes[] = $quiz;
            $cat_name = $quiz['cat_name'] ?? '<span class="error">NO CATEGORY!</span>';
            echo "<tr>";
            echo "<td>{$quiz['quiz_id']}</td>";
            echo "<td>{$quiz['quiz_title']}</td>";
            echo "<td>$cat_name (ID: {$quiz['category_id']})</td>";
            echo "<td>{$quiz['question_count']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ NO PUBLISHED QUIZZES FOUND!</p>";
        echo "<div class='info'>";
        echo "<strong>Problem:</strong> There are no published quizzes in the system.<br>";
        echo "<strong>Solution:</strong> Creators need to publish their quizzes.<br>";
        echo "Go to quiz management and click 'Publish' on quizzes.";
        echo "</div>";
        
        // Check for unpublished quizzes
        $unpublished = $db->query("SELECT COUNT(*) as count FROM quizzes WHERE is_published = 0");
        $unpub_count = $unpublished->fetch_assoc()['count'];
        if ($unpub_count > 0) {
            echo "<p class='warning'>Note: There are $unpub_count unpublished quizzes that students cannot see.</p>";
        }
    }
    
    // CHECK 2: What has this user purchased?
    echo "<h2>Check 2: User's Purchases</h2>";
    $result = $db->query("
        SELECT p.purchase_id, p.invoice_no, p.purchase_date, p.order_status
        FROM purchases p
        WHERE p.customer_id = $user_id
        ORDER BY p.purchase_id DESC
    ");
    
    if ($result->num_rows > 0) {
        echo "<p class='success'>✓ User has {$result->num_rows} purchase(s)</p>";
        echo "<table><tr><th>Purchase ID</th><th>Invoice</th><th>Date</th><th>Status</th><th>Has Items?</th></tr>";
        while ($purchase = $result->fetch_assoc()) {
            $purchase_id = $purchase['purchase_id'];
            
            // Check if this purchase has order_items
            $items_check = $db->query("SELECT COUNT(*) as count FROM order_items WHERE purchase_id = $purchase_id");
            $item_count = $items_check->fetch_assoc()['count'];
            $has_items = $item_count > 0 ? "<span class='success'>Yes ($item_count items)</span>" : "<span class='error'>NO ITEMS!</span>";
            
            echo "<tr>";
            echo "<td>{$purchase['purchase_id']}</td>";
            echo "<td>{$purchase['invoice_no']}</td>";
            echo "<td>{$purchase['purchase_date']}</td>";
            echo "<td>{$purchase['order_status']}</td>";
            echo "<td>$has_items</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ User has NO purchases!</p>";
        echo "<div class='info'>";
        echo "<strong>Problem:</strong> This user has not made any purchases.<br>";
        echo "<strong>Solution:</strong> User needs to purchase resources first.";
        echo "</div>";
    }
    
    // CHECK 3: What order_items does this user have?
    echo "<h2>Check 3: User's Order Items (CRITICAL!)</h2>";
    $result = $db->query("
        SELECT oi.order_item_id, oi.purchase_id, r.resource_id, r.resource_title, r.cat_id, c.cat_name, oi.price
        FROM order_items oi
        JOIN purchases p ON oi.purchase_id = p.purchase_id
        JOIN resources r ON oi.resource_id = r.resource_id
        JOIN categories c ON r.cat_id = c.cat_id
        WHERE p.customer_id = $user_id
        ORDER BY oi.order_item_id DESC
    ");
    
    if ($result->num_rows > 0) {
        echo "<p class='success'>✓ User has {$result->num_rows} order item(s)</p>";
        echo "<table><tr><th>Item ID</th><th>Purchase ID</th><th>Resource</th><th>Category</th><th>Price</th></tr>";
        $purchased_categories = [];
        while ($item = $result->fetch_assoc()) {
            $purchased_categories[$item['cat_id']] = $item['cat_name'];
            echo "<tr>";
            echo "<td>{$item['order_item_id']}</td>";
            echo "<td>{$item['purchase_id']}</td>";
            echo "<td>{$item['resource_title']}</td>";
            echo "<td>{$item['cat_name']} (ID: {$item['cat_id']})</td>";
            echo "<td>GHS {$item['price']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<div class='info'>";
        echo "<strong>Categories User Can Access:</strong><br>";
        foreach ($purchased_categories as $cat_id => $cat_name) {
            echo "• $cat_name (ID: $cat_id)<br>";
        }
        echo "</div>";
    } else {
        echo "<p class='error'>✗ User has NO order_items!</p>";
        echo "<div class='info'>";
        echo "<strong>Problem:</strong> User's purchases have no order_items records.<br>";
        echo "<strong>This is why quizzes don't show up!</strong><br><br>";
        echo "<strong>Cause:</strong> The old payment system didn't create order_items.<br>";
        echo "<strong>Solution:</strong> Apply the payment system fix and make a new purchase.";
        echo "</div>";
        
        // Show which purchases are missing order_items
        $orphans = $db->query("
            SELECT p.purchase_id, p.invoice_no, p.purchase_date
            FROM purchases p
            LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
            WHERE p.customer_id = $user_id AND oi.order_item_id IS NULL
        ");
        
        if ($orphans->num_rows > 0) {
            echo "<p class='warning'>Purchases missing order_items:</p>";
            echo "<table><tr><th>Purchase ID</th><th>Invoice</th><th>Date</th></tr>";
            while ($orphan = $orphans->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$orphan['purchase_id']}</td>";
                echo "<td>{$orphan['invoice_no']}</td>";
                echo "<td>{$orphan['purchase_date']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    // CHECK 4: Match quizzes to purchased categories
    echo "<h2>Check 4: Quiz Access Match</h2>";
    
    if (isset($published_quizzes) && isset($purchased_categories)) {
        echo "<p>Checking if published quizzes match purchased categories...</p>";
        echo "<table><tr><th>Quiz</th><th>Category</th><th>Can Access?</th><th>Reason</th></tr>";
        
        $accessible_count = 0;
        foreach ($published_quizzes as $quiz) {
            $can_access = isset($purchased_categories[$quiz['category_id']]);
            $accessible_count += $can_access ? 1 : 0;
            
            $status = $can_access ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>";
            $reason = $can_access 
                ? "Purchased {$purchased_categories[$quiz['category_id']]}" 
                : "Haven't purchased {$quiz['cat_name']}";
            
            echo "<tr>";
            echo "<td>{$quiz['quiz_title']}</td>";
            echo "<td>{$quiz['cat_name']}</td>";
            echo "<td>$status</td>";
            echo "<td>$reason</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if ($accessible_count > 0) {
            echo "<p class='success'>✓ User should be able to access $accessible_count quiz(es)</p>";
        } else {
            echo "<p class='error'>✗ User cannot access any quizzes</p>";
            echo "<div class='info'>";
            echo "<strong>Problem:</strong> User hasn't purchased resources in categories that have published quizzes.<br>";
            echo "<strong>Solution:</strong> User needs to purchase resources in these categories.";
            echo "</div>";
        }
    } else {
        echo "<p class='warning'>Cannot perform match - missing data from previous checks</p>";
    }
    
    // CHECK 5: Run the actual quiz access query
    echo "<h2>Check 5: Actual Quiz Access Query</h2>";
    echo "<p>This is the exact query used by the system:</p>";
    echo "<div class='code'>";
    echo "SELECT DISTINCT q.*, c.customer_name as creator_name, cat.cat_name<br>";
    echo "FROM quizzes q<br>";
    echo "JOIN customer c ON q.user_id = c.customer_id<br>";
    echo "JOIN categories cat ON q.category_id = cat.cat_id<br>";
    echo "WHERE q.is_published = 1<br>";
    echo "AND q.category_id IN (<br>";
    echo "&nbsp;&nbsp;SELECT DISTINCT r.cat_id<br>";
    echo "&nbsp;&nbsp;FROM order_items oi<br>";
    echo "&nbsp;&nbsp;JOIN resources r ON oi.resource_id = r.resource_id<br>";
    echo "&nbsp;&nbsp;JOIN purchases p ON oi.purchase_id = p.purchase_id<br>";
    echo "&nbsp;&nbsp;WHERE p.customer_id = $user_id<br>";
    echo ")<br>";
    echo "ORDER BY q.created_at DESC";
    echo "</div>";
    
    $result = $db->query("
        SELECT DISTINCT q.quiz_id, q.quiz_title, cat.cat_name, c.customer_name as creator_name
        FROM quizzes q
        JOIN customer c ON q.user_id = c.customer_id
        JOIN categories cat ON q.category_id = cat.cat_id
        WHERE q.is_published = 1
        AND q.category_id IN (
            SELECT DISTINCT r.cat_id
            FROM order_items oi
            JOIN resources r ON oi.resource_id = r.resource_id
            JOIN purchases p ON oi.purchase_id = p.purchase_id
            WHERE p.customer_id = $user_id
        )
        ORDER BY q.created_at DESC
    ");
    
    if ($result->num_rows > 0) {
        echo "<p class='success'>✓ Query returned {$result->num_rows} quiz(es)</p>";
        echo "<table><tr><th>Quiz ID</th><th>Title</th><th>Category</th><th>Creator</th></tr>";
        while ($quiz = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$quiz['quiz_id']}</td>";
            echo "<td>{$quiz['quiz_title']}</td>";
            echo "<td>{$quiz['cat_name']}</td>";
            echo "<td>{$quiz['creator_name']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✓ These quizzes SHOULD be showing to the user!</p>";
    } else {
        echo "<p class='error'>✗ Query returned 0 quizzes</p>";
    }
    
    // DIAGNOSIS SUMMARY
    echo "<h2>🎯 Diagnosis Summary</h2>";
    
    $published_quiz_count = $db->query("SELECT COUNT(*) as count FROM quizzes WHERE is_published = 1")->fetch_assoc()['count'];
    $user_purchase_count = $db->query("SELECT COUNT(*) as count FROM purchases WHERE customer_id = $user_id")->fetch_assoc()['count'];
    $user_orderitem_count = $db->query("
        SELECT COUNT(*) as count FROM order_items oi
        JOIN purchases p ON oi.purchase_id = p.purchase_id
        WHERE p.customer_id = $user_id
    ")->fetch_assoc()['count'];
    
    echo "<table>";
    echo "<tr><th>Check</th><th>Status</th><th>Details</th></tr>";
    
    // Check 1: Published quizzes
    $check1_status = $published_quiz_count > 0 ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    $check1_detail = "$published_quiz_count published quiz(es)";
    echo "<tr><td>Published Quizzes Exist</td><td>$check1_status</td><td>$check1_detail</td></tr>";
    
    // Check 2: User has purchases
    $check2_status = $user_purchase_count > 0 ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    $check2_detail = "$user_purchase_count purchase(s)";
    echo "<tr><td>User Has Purchases</td><td>$check2_status</td><td>$check2_detail</td></tr>";
    
    // Check 3: User has order_items (CRITICAL!)
    $check3_status = $user_orderitem_count > 0 ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    $check3_detail = "$user_orderitem_count order item(s)";
    echo "<tr><td>User Has Order Items</td><td>$check3_status</td><td>$check3_detail</td></tr>";
    
    echo "</table>";
    
    // Final diagnosis
    echo "<div class='info' style='margin-top: 30px; font-size: 16px;'>";
    echo "<strong>🔍 ROOT CAUSE:</strong><br><br>";
    
    if ($published_quiz_count == 0) {
        echo "<span class='error'>✗ NO PUBLISHED QUIZZES</span><br>";
        echo "There are no published quizzes in the system. Creators need to publish their quizzes.<br><br>";
        echo "<strong>Solution:</strong> Go to quiz management and publish quizzes.";
    } elseif ($user_purchase_count == 0) {
        echo "<span class='error'>✗ USER HAS NO PURCHASES</span><br>";
        echo "This user has not made any purchases yet.<br><br>";
        echo "<strong>Solution:</strong> User needs to purchase resources first.";
    } elseif ($user_orderitem_count == 0) {
        echo "<span class='error'>✗ PURCHASES HAVE NO ORDER_ITEMS</span><br>";
        echo "This is the problem! User has purchases but no order_items records.<br>";
        echo "This happens when the old payment system didn't create order_items.<br><br>";
        echo "<strong>Solution:</strong><br>";
        echo "1. Apply the payment system fix (db/fix_purchases_table.sql)<br>";
        echo "2. Make a new test purchase to verify it works<br>";
        echo "3. For old purchases, manually add order_items if you know what was bought<br>";
        echo "4. Or ask user to repurchase (give them a discount/refund)";
    } else {
        echo "<span class='warning'>⚠️ CATEGORY MISMATCH</span><br>";
        echo "User has purchases with order_items, but the categories don't match published quizzes.<br><br>";
        echo "<strong>Solution:</strong><br>";
        echo "1. User needs to purchase resources in categories that have published quizzes<br>";
        echo "2. Or creators need to publish quizzes in categories user has purchased";
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>Database Error: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";
?>
