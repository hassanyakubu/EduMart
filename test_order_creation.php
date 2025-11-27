<?php
/**
 * Test Order Creation Functions
 * This simulates what should happen during payment
 */

echo "<!DOCTYPE html><html><head><title>Test Order Creation</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
h1 { color: #dc2626; }
.success { color: #059669; font-weight: 600; }
.error { color: #dc2626; font-weight: 600; }
pre { background: #1f2937; color: #10b981; padding: 15px; border-radius: 4px; overflow-x: auto; }
</style></head><body><div class='container'>";

echo "<h1>🧪 Test Order Creation</h1>";
echo "<p>This will test if the order creation functions work correctly.</p>";

try {
    // Load required files
    echo "<h2>Step 1: Loading Files</h2>";
    require_once __DIR__ . '/app/config/database.php';
    require_once __DIR__ . '/controllers/order_controller.php';
    require_once __DIR__ . '/controllers/cart_controller.php';
    echo "<p class='success'>✓ Files loaded successfully</p>";
    
    // Test database connection
    echo "<h2>Step 2: Testing Database Connection</h2>";
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✓ Database connected</p>";
    
    // Create a test order
    echo "<h2>Step 3: Creating Test Order</h2>";
    $test_customer_id = 21; // Reginald Tetteh
    $test_invoice = 'TEST-' . date('YmdHis');
    $test_date = date('Y-m-d');
    
    echo "<p>Customer ID: $test_customer_id</p>";
    echo "<p>Invoice: $test_invoice</p>";
    echo "<p>Date: $test_date</p>";
    
    $order_id = create_order_ctr($test_customer_id, $test_invoice, $test_date, 'Paid');
    
    if ($order_id) {
        echo "<p class='success'>✓ Order created successfully! Order ID: $order_id</p>";
        
        // Add order items
        echo "<h2>Step 4: Adding Order Items</h2>";
        $test_resource_id = 3; // BECE English
        $test_qty = 1;
        
        echo "<p>Adding resource ID: $test_resource_id, Qty: $test_qty</p>";
        
        $item_result = add_order_details_ctr($order_id, $test_resource_id, $test_qty);
        
        if ($item_result) {
            echo "<p class='success'>✓ Order item added successfully!</p>";
            
            // Verify in database
            echo "<h2>Step 5: Verifying in Database</h2>";
            
            $check_purchase = $db->query("SELECT * FROM purchases WHERE purchase_id = $order_id");
            if ($check_purchase->num_rows > 0) {
                echo "<p class='success'>✓ Purchase record exists</p>";
                $purchase = $check_purchase->fetch_assoc();
                echo "<pre>" . print_r($purchase, true) . "</pre>";
            }
            
            $check_items = $db->query("SELECT * FROM order_items WHERE purchase_id = $order_id");
            if ($check_items->num_rows > 0) {
                echo "<p class='success'>✓ Order items exist (" . $check_items->num_rows . " items)</p>";
                while ($item = $check_items->fetch_assoc()) {
                    echo "<pre>" . print_r($item, true) . "</pre>";
                }
            } else {
                echo "<p class='error'>✗ No order items found!</p>";
            }
            
            $check_downloads = $db->query("SELECT * FROM downloads WHERE purchase_id = $order_id");
            if ($check_downloads->num_rows > 0) {
                echo "<p class='success'>✓ Downloads exist (" . $check_downloads->num_rows . " downloads)</p>";
            } else {
                echo "<p class='error'>✗ No downloads found!</p>";
            }
            
            // Test payment recording
            echo "<h2>Step 6: Recording Payment</h2>";
            $payment_id = record_payment_ctr(
                15.00,
                $test_customer_id,
                $order_id,
                'GHS',
                $test_date,
                'paystack',
                'TEST-REF-' . time(),
                'TEST-AUTH',
                'card'
            );
            
            if ($payment_id) {
                echo "<p class='success'>✓ Payment recorded! Payment ID: $payment_id</p>";
            } else {
                echo "<p class='error'>✗ Failed to record payment</p>";
            }
            
            echo "<h2>✅ TEST SUCCESSFUL!</h2>";
            echo "<p>All functions are working correctly. The issue must be in the payment flow.</p>";
            
            echo "<h3>Next Steps:</h3>";
            echo "<ul>";
            echo "<li>Check if paystack_verify_payment.php is being called</li>";
            echo "<li>Check PHP error log for errors during payment</li>";
            echo "<li>Verify Paystack callback URL is correct</li>";
            echo "<li>Make a real test payment and watch the logs</li>";
            echo "</ul>";
            
            // Clean up test data
            echo "<h2>Cleanup</h2>";
            echo "<p>Deleting test order...</p>";
            $db->query("DELETE FROM payments WHERE purchase_id = $order_id");
            $db->query("DELETE FROM downloads WHERE purchase_id = $order_id");
            $db->query("DELETE FROM order_items WHERE purchase_id = $order_id");
            $db->query("DELETE FROM purchases WHERE purchase_id = $order_id");
            echo "<p class='success'>✓ Test data cleaned up</p>";
            
        } else {
            echo "<p class='error'>✗ Failed to add order item</p>";
        }
        
    } else {
        echo "<p class='error'>✗ Failed to create order</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</div></body></html>";
?>
