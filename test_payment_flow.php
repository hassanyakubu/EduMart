<?php
/**
 * Test Payment Flow
 * This script tests if all payment components are working correctly
 */

echo "=== EduMart Payment Flow Test ===\n\n";

// Test 1: Check if controller files exist
echo "1. Checking controller files...\n";
$files_to_check = [
    'controllers/order_controller.php',
    'controllers/cart_controller.php',
    'actions/paystack_verify_payment.php',
    'view/paystack_callback.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file exists\n";
    } else {
        echo "   ✗ $file is MISSING!\n";
    }
}

// Test 2: Check if functions are defined
echo "\n2. Checking if controller functions are defined...\n";
require_once __DIR__ . '/controllers/order_controller.php';
require_once __DIR__ . '/controllers/cart_controller.php';

$functions_to_check = [
    'create_order_ctr',
    'add_order_details_ctr',
    'record_payment_ctr',
    'get_user_cart_ctr',
    'empty_cart_ctr'
];

foreach ($functions_to_check as $func) {
    if (function_exists($func)) {
        echo "   ✓ $func() is defined\n";
    } else {
        echo "   ✗ $func() is NOT defined!\n";
    }
}

// Test 3: Check database connection
echo "\n3. Testing database connection...\n";
try {
    require_once __DIR__ . '/app/config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "   ✓ Database connection successful\n";
    
    // Test 4: Check table structures
    echo "\n4. Checking database tables...\n";
    
    $tables = ['purchases', 'order_items', 'downloads', 'payments', 'cart_items', 'resources'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "   ✓ Table '$table' exists\n";
        } else {
            echo "   ✗ Table '$table' is MISSING!\n";
        }
    }
    
    // Test 5: Check purchases.invoice_no column type
    echo "\n5. Checking purchases.invoice_no column type...\n";
    $result = $db->query("DESCRIBE purchases");
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] === 'invoice_no') {
            $type = $row['Type'];
            if (strpos(strtolower($type), 'varchar') !== false) {
                echo "   ✓ invoice_no is VARCHAR ($type)\n";
            } else {
                echo "   ✗ invoice_no is $type (should be VARCHAR!)\n";
                echo "   → Run: ALTER TABLE purchases MODIFY COLUMN invoice_no VARCHAR(50) NOT NULL;\n";
            }
        }
    }
    
    // Test 6: Check if payments table has correct structure
    echo "\n6. Checking payments table structure...\n";
    $result = $db->query("SHOW TABLES LIKE 'payments'");
    if ($result->num_rows > 0) {
        echo "   ✓ Payments table exists\n";
        $result = $db->query("DESCRIBE payments");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        $required_columns = ['payment_id', 'purchase_id', 'customer_id', 'amount', 'payment_reference'];
        foreach ($required_columns as $col) {
            if (in_array($col, $columns)) {
                echo "   ✓ Column '$col' exists\n";
            } else {
                echo "   ✗ Column '$col' is MISSING!\n";
            }
        }
    } else {
        echo "   ✗ Payments table does NOT exist!\n";
        echo "   → Run the SQL in db/fix_purchases_table.sql\n";
    }
    
    // Test 7: Check recent orders
    echo "\n7. Checking recent orders...\n";
    $result = $db->query("SELECT COUNT(*) as count FROM purchases");
    $row = $result->fetch_assoc();
    echo "   Total purchases: {$row['count']}\n";
    
    $result = $db->query("SELECT COUNT(*) as count FROM order_items");
    $row = $result->fetch_assoc();
    echo "   Total order items: {$row['count']}\n";
    
    $result = $db->query("SELECT COUNT(*) as count FROM downloads");
    $row = $result->fetch_assoc();
    echo "   Total downloads: {$row['count']}\n";
    
    // Test 8: Check quiz access query
    echo "\n8. Testing quiz access query...\n";
    $test_query = "SELECT DISTINCT r.cat_id, c.cat_name
                   FROM order_items oi
                   JOIN resources r ON oi.resource_id = r.resource_id
                   JOIN purchases p ON oi.purchase_id = p.purchase_id
                   JOIN categories c ON r.cat_id = c.cat_id
                   GROUP BY r.cat_id, c.cat_name";
    $result = $db->query($test_query);
    if ($result) {
        echo "   ✓ Quiz access query works\n";
        echo "   Categories with purchases:\n";
        while ($row = $result->fetch_assoc()) {
            echo "     - {$row['cat_name']} (ID: {$row['cat_id']})\n";
        }
    } else {
        echo "   ✗ Quiz access query failed: " . $db->error . "\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. XAMPP MySQL is running\n";
    echo "2. Database credentials in settings/db_cred.php are correct\n";
    echo "3. Database 'ecommerce_2025A_hassan_yakubu' exists\n";
}
?>
