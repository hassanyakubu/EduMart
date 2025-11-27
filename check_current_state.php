<?php
/**
 * Check Current Database State
 * Run this to see exactly what's in the database right now
 */

require_once __DIR__ . '/app/config/database.php';

echo "<!DOCTYPE html><html><head><title>Current Database State</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
h1 { color: #dc2626; }
h2 { color: #374151; margin-top: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
th, td { padding: 10px; text-align: left; border: 1px solid #e5e7eb; }
th { background: #f9fafb; font-weight: 600; }
.success { color: #059669; font-weight: 600; }
.error { color: #dc2626; font-weight: 600; }
.warning { color: #f59e0b; font-weight: 600; }
.info { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; }
</style></head><body><div class='container'>";

echo "<h1>🔍 Current Database State</h1>";
echo "<p>Checking what's in your database right now...</p>";

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Check Quizzes
    echo "<h2>1. Quizzes Table</h2>";
    $result = $db->query("
        SELECT q.quiz_id, q.quiz_title, q.category_id, c.cat_name, q.is_published,
               (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.quiz_id) as questions
        FROM quizzes q
        LEFT JOIN categories c ON q.category_id = c.cat_id
        ORDER BY q.quiz_id
    ");
    
    if ($result->num_rows > 0) {
        echo "<p>Total quizzes: {$result->num_rows}</p>";
        echo "<table><tr><th>ID</th><th>Title</th><th>Category</th><th>Published</th><th>Questions</th></tr>";
        $published_count = 0;
        while ($row = $result->fetch_assoc()) {
            $published = $row['is_published'] ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>";
            if ($row['is_published']) $published_count++;
            $cat = $row['cat_name'] ?? '<span class="error">NO CATEGORY</span>';
            echo "<tr>";
            echo "<td>{$row['quiz_id']}</td>";
            echo "<td>{$row['quiz_title']}</td>";
            echo "<td>$cat (ID: {$row['category_id']})</td>";
            echo "<td>$published</td>";
            echo "<td>{$row['questions']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Published quizzes: $published_count</strong></p>";
    } else {
        echo "<p class='error'>NO QUIZZES FOUND!</p>";
    }
    
    // 2. Check Purchases
    echo "<h2>2. Purchases Table</h2>";
    $result = $db->query("
        SELECT p.purchase_id, p.customer_id, c.customer_name, p.invoice_no, p.purchase_date, p.order_status
        FROM purchases p
        JOIN customer c ON p.customer_id = c.customer_id
        ORDER BY p.purchase_id DESC
        LIMIT 20
    ");
    
    if ($result->num_rows > 0) {
        echo "<p>Total purchases: " . $db->query("SELECT COUNT(*) as c FROM purchases")->fetch_assoc()['c'] . "</p>";
        echo "<table><tr><th>ID</th><th>Customer</th><th>Invoice</th><th>Date</th><th>Status</th><th>Has Items?</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $items_check = $db->query("SELECT COUNT(*) as c FROM order_items WHERE purchase_id = {$row['purchase_id']}");
            $item_count = $items_check->fetch_assoc()['c'];
            $has_items = $item_count > 0 ? "<span class='success'>$item_count items</span>" : "<span class='error'>NO ITEMS</span>";
            
            echo "<tr>";
            echo "<td>{$row['purchase_id']}</td>";
            echo "<td>{$row['customer_name']} (ID: {$row['customer_id']})</td>";
            echo "<td>{$row['invoice_no']}</td>";
            echo "<td>{$row['purchase_date']}</td>";
            echo "<td>{$row['order_status']}</td>";
            echo "<td>$has_items</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>NO PURCHASES FOUND!</p>";
    }
    
    // 3. Check Order Items
    echo "<h2>3. Order Items Table</h2>";
    $result = $db->query("
        SELECT oi.order_item_id, oi.purchase_id, oi.resource_id, r.resource_title, r.cat_id, c.cat_name, oi.price
        FROM order_items oi
        JOIN resources r ON oi.resource_id = r.resource_id
        JOIN categories c ON r.cat_id = c.cat_id
        ORDER BY oi.order_item_id DESC
        LIMIT 20
    ");
    
    $total_items = $db->query("SELECT COUNT(*) as c FROM order_items")->fetch_assoc()['c'];
    echo "<p>Total order items: $total_items</p>";
    
    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Purchase</th><th>Resource</th><th>Category</th><th>Price</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['order_item_id']}</td>";
            echo "<td>{$row['purchase_id']}</td>";
            echo "<td>{$row['resource_title']}</td>";
            echo "<td>{$row['cat_name']} (ID: {$row['cat_id']})</td>";
            echo "<td>GHS {$row['price']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>NO ORDER ITEMS FOUND!</p>";
        echo "<div class='info'><strong>PROBLEM:</strong> This is why quizzes don't show! No order_items means no quiz access.</div>";
    }
    
    // 4. Check which categories have been purchased
    echo "<h2>4. Categories with Purchases</h2>";
    $result = $db->query("
        SELECT DISTINCT c.cat_id, c.cat_name, COUNT(DISTINCT oi.order_item_id) as items
        FROM order_items oi
        JOIN resources r ON oi.resource_id = r.resource_id
        JOIN categories c ON r.cat_id = c.cat_id
        GROUP BY c.cat_id, c.cat_name
        ORDER BY c.cat_id
    ");
    
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Category ID</th><th>Category Name</th><th>Items Purchased</th></tr>";
        $purchased_cats = [];
        while ($row = $result->fetch_assoc()) {
            $purchased_cats[] = $row['cat_id'];
            echo "<tr>";
            echo "<td>{$row['cat_id']}</td>";
            echo "<td>{$row['cat_name']}</td>";
            echo "<td>{$row['items']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Students can access quizzes in categories: " . implode(', ', $purchased_cats) . "</strong></p>";
    } else {
        echo "<p class='error'>NO CATEGORIES HAVE BEEN PURCHASED!</p>";
    }
    
    // 5. Check Students
    echo "<h2>5. Students and Their Purchases</h2>";
    $result = $db->query("
        SELECT 
            c.customer_id,
            c.customer_name,
            c.customer_email,
            COUNT(DISTINCT p.purchase_id) as purchases,
            COUNT(DISTINCT oi.order_item_id) as order_items,
            GROUP_CONCAT(DISTINCT cat.cat_name SEPARATOR ', ') as categories
        FROM customer c
        LEFT JOIN purchases p ON c.customer_id = p.customer_id
        LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
        LEFT JOIN resources r ON oi.resource_id = r.resource_id
        LEFT JOIN categories cat ON r.cat_id = cat.cat_id
        WHERE c.user_type = 'student'
        GROUP BY c.customer_id, c.customer_name, c.customer_email
        ORDER BY c.customer_id
    ");
    
    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Purchases</th><th>Order Items</th><th>Can Access Categories</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $items_status = $row['order_items'] > 0 ? "<span class='success'>{$row['order_items']}</span>" : "<span class='error'>0</span>";
            $cats = $row['categories'] ?? '<span class="error">None</span>';
            
            echo "<tr>";
            echo "<td>{$row['customer_id']}</td>";
            echo "<td>{$row['customer_name']}</td>";
            echo "<td>{$row['customer_email']}</td>";
            echo "<td>{$row['purchases']}</td>";
            echo "<td>$items_status</td>";
            echo "<td>$cats</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>No students found.</p>";
    }
    
    // 6. DIAGNOSIS
    echo "<h2>6. 🎯 DIAGNOSIS</h2>";
    
    $quiz_count = $db->query("SELECT COUNT(*) as c FROM quizzes WHERE is_published = 1")->fetch_assoc()['c'];
    $purchase_count = $db->query("SELECT COUNT(*) as c FROM purchases")->fetch_assoc()['c'];
    $orderitem_count = $db->query("SELECT COUNT(*) as c FROM order_items")->fetch_assoc()['c'];
    
    echo "<table>";
    echo "<tr><th>Check</th><th>Status</th><th>Result</th></tr>";
    
    $check1 = $quiz_count > 0 ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    echo "<tr><td>Published Quizzes Exist</td><td>$check1</td><td>$quiz_count quiz(es)</td></tr>";
    
    $check2 = $purchase_count > 0 ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    echo "<tr><td>Purchases Exist</td><td>$check2</td><td>$purchase_count purchase(s)</td></tr>";
    
    $check3 = $orderitem_count > 0 ? "<span class='success'>✓ PASS</span>" : "<span class='error'>✗ FAIL</span>";
    echo "<tr><td>Order Items Exist</td><td>$check3</td><td>$orderitem_count item(s)</td></tr>";
    
    echo "</table>";
    
    echo "<div class='info' style='margin-top: 30px; font-size: 16px;'>";
    echo "<strong>🔍 ROOT CAUSE:</strong><br><br>";
    
    if ($quiz_count == 0) {
        echo "<span class='error'>✗ NO PUBLISHED QUIZZES</span><br>";
        echo "Solution: Publish quizzes by running:<br>";
        echo "<code>UPDATE quizzes SET is_published = 1;</code>";
    } elseif ($orderitem_count == 0) {
        echo "<span class='error'>✗ NO ORDER ITEMS</span><br>";
        echo "This is the problem! Purchases exist but have no order_items.<br>";
        echo "This happens when you restore an old database backup.<br><br>";
        echo "<strong>Solution:</strong><br>";
        echo "1. Make a new test purchase to verify the fix works<br>";
        echo "2. For old purchases, manually add order_items if you know what was bought";
    } elseif ($purchase_count == 0) {
        echo "<span class='error'>✗ NO PURCHASES</span><br>";
        echo "Solution: Students need to make purchases first.";
    } else {
        // Check category match
        $quiz_cats = $db->query("SELECT DISTINCT category_id FROM quizzes WHERE is_published = 1");
        $purchase_cats = $db->query("SELECT DISTINCT r.cat_id FROM order_items oi JOIN resources r ON oi.resource_id = r.resource_id");
        
        $quiz_cat_ids = [];
        while ($row = $quiz_cats->fetch_assoc()) $quiz_cat_ids[] = $row['category_id'];
        
        $purchase_cat_ids = [];
        while ($row = $purchase_cats->fetch_assoc()) $purchase_cat_ids[] = $row['cat_id'];
        
        $matching = array_intersect($quiz_cat_ids, $purchase_cat_ids);
        
        if (empty($matching)) {
            echo "<span class='warning'>⚠️ CATEGORY MISMATCH</span><br>";
            echo "Quizzes exist in categories: " . implode(', ', $quiz_cat_ids) . "<br>";
            echo "Purchases exist in categories: " . implode(', ', $purchase_cat_ids) . "<br>";
            echo "No overlap! Students need to purchase resources in categories that have quizzes.";
        } else {
            echo "<span class='success'>✓ EVERYTHING LOOKS GOOD</span><br>";
            echo "Matching categories: " . implode(', ', $matching) . "<br>";
            echo "Students should be able to see quizzes!<br><br>";
            echo "If they still can't, check:<br>";
            echo "1. Is the correct student logged in?<br>";
            echo "2. Clear browser cache and try again<br>";
            echo "3. Check PHP error log for errors";
        }
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>Database Error: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";
?>
