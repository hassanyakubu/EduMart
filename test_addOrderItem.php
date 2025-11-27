<?php
/**
 * Test if addOrderItem actually works
 */

require_once 'app/config/database.php';
require_once 'app/models/order_model.php';

echo "<pre>";
echo "=== Testing addOrderItem Method ===\n\n";

try {
    $orderModel = new order_model();
    
    // Get the latest purchase
    $db = Database::getInstance()->getConnection();
    $result = $db->query("SELECT purchase_id, customer_id FROM purchases ORDER BY purchase_id DESC LIMIT 1");
    $purchase = $result->fetch_assoc();
    
    if (!$purchase) {
        die("No purchases found!\n");
    }
    
    $purchase_id = $purchase['purchase_id'];
    $customer_id = $purchase['customer_id'];
    
    echo "Latest purchase: $purchase_id (customer: $customer_id)\n";
    
    // Check current order_items
    $result = $db->query("SELECT COUNT(*) as count FROM order_items WHERE purchase_id = $purchase_id");
    $before = $result->fetch_assoc()['count'];
    echo "Order items BEFORE: $before\n\n";
    
    // Try to add an order item
    echo "Attempting to add order item...\n";
    $test_resource_id = 3; // BECE English
    $test_qty = 1;
    $test_price = 15.00;
    
    $result = $orderModel->addOrderItem($purchase_id, $test_resource_id, $test_qty, $test_price);
    
    if ($result) {
        echo "✓ addOrderItem returned TRUE\n";
    } else {
        echo "✗ addOrderItem returned FALSE\n";
    }
    
    // Check order_items again
    $result = $db->query("SELECT COUNT(*) as count FROM order_items WHERE purchase_id = $purchase_id");
    $after = $result->fetch_assoc()['count'];
    echo "Order items AFTER: $after\n\n";
    
    if ($after > $before) {
        echo "✓ SUCCESS! Order item was added.\n";
        
        // Show the added item
        $result = $db->query("SELECT * FROM order_items WHERE purchase_id = $purchase_id ORDER BY order_item_id DESC LIMIT 1");
        $item = $result->fetch_assoc();
        echo "\nAdded item:\n";
        print_r($item);
        
        // Clean up test data
        $db->query("DELETE FROM order_items WHERE order_item_id = {$item['order_item_id']}");
        echo "\n✓ Test data cleaned up\n";
    } else {
        echo "✗ FAILED! Order item was NOT added.\n";
        echo "\nPossible reasons:\n";
        echo "1. Database error\n";
        echo "2. Method not working\n";
        echo "3. Permissions issue\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "</pre>";
?>
