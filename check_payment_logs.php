<?php
/**
 * Check Recent Payment Attempts
 * This shows what happened during recent payment attempts
 */

echo "<!DOCTYPE html><html><head><title>Payment Logs</title>";
echo "<style>
body { font-family: monospace; margin: 20px; background: #1a1a1a; color: #0f0; }
.container { max-width: 1400px; margin: 0 auto; background: #000; padding: 30px; border: 2px solid #0f0; }
h1 { color: #0f0; }
.error { color: #f00; }
.warning { color: #ff0; }
.success { color: #0f0; }
pre { background: #1a1a1a; padding: 15px; overflow-x: auto; border: 1px solid #0f0; }
</style></head><body><div class='container'>";

echo "<h1>🔍 Payment System Logs</h1>";

// Check if error log exists
$error_log_paths = [
    '/Applications/XAMPP/xamppfiles/logs/php_error_log',
    __DIR__ . '/error_log',
    ini_get('error_log')
];

echo "<h2>Checking Error Logs:</h2>";
foreach ($error_log_paths as $path) {
    if (file_exists($path)) {
        echo "<p class='success'>✓ Found: $path</p>";
        
        // Get last 100 lines
        $lines = file($path);
        $recent_lines = array_slice($lines, -100);
        
        // Filter for payment-related logs
        $payment_logs = array_filter($recent_lines, function($line) {
            return stripos($line, 'paystack') !== false || 
                   stripos($line, 'payment') !== false ||
                   stripos($line, 'order') !== false ||
                   stripos($line, 'create_order') !== false;
        });
        
        if (!empty($payment_logs)) {
            echo "<h3>Recent Payment Logs:</h3>";
            echo "<pre>";
            foreach ($payment_logs as $log) {
                if (stripos($log, 'error') !== false || stripos($log, 'fail') !== false) {
                    echo "<span class='error'>" . htmlspecialchars($log) . "</span>";
                } elseif (stripos($log, 'warning') !== false) {
                    echo "<span class='warning'>" . htmlspecialchars($log) . "</span>";
                } else {
                    echo htmlspecialchars($log);
                }
            }
            echo "</pre>";
        } else {
            echo "<p class='warning'>No payment-related logs found in last 100 lines</p>";
        }
    }
}

// Check if functions are being called
echo "<h2>Testing Controller Functions:</h2>";

try {
    require_once __DIR__ . '/controllers/order_controller.php';
    require_once __DIR__ . '/controllers/cart_controller.php';
    
    echo "<p class='success'>✓ Controller files loaded successfully</p>";
    
    // Test if functions exist
    $functions = ['create_order_ctr', 'add_order_details_ctr', 'record_payment_ctr', 'get_user_cart_ctr', 'empty_cart_ctr'];
    foreach ($functions as $func) {
        if (function_exists($func)) {
            echo "<p class='success'>✓ Function exists: $func()</p>";
        } else {
            echo "<p class='error'>✗ Function missing: $func()</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error loading controllers: " . $e->getMessage() . "</p>";
}

// Check recent purchases and their order_items
echo "<h2>Recent Purchases (Last 10):</h2>";

try {
    require_once __DIR__ . '/app/config/database.php';
    $db = Database::getInstance()->getConnection();
    
    $result = $db->query("
        SELECT 
            p.purchase_id,
            p.customer_id,
            c.customer_name,
            p.invoice_no,
            p.purchase_date,
            p.order_status,
            COUNT(oi.order_item_id) as item_count,
            GROUP_CONCAT(r.resource_title SEPARATOR ', ') as resources
        FROM purchases p
        JOIN customer c ON p.customer_id = c.customer_id
        LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
        LEFT JOIN resources r ON oi.resource_id = r.resource_id
        GROUP BY p.purchase_id
        ORDER BY p.purchase_id DESC
        LIMIT 10
    ");
    
    echo "<pre>";
    echo str_pad("ID", 5) . str_pad("Customer", 20) . str_pad("Invoice", 25) . str_pad("Date", 12) . str_pad("Items", 8) . "Resources\n";
    echo str_repeat("-", 120) . "\n";
    
    while ($row = $result->fetch_assoc()) {
        $color = $row['item_count'] > 0 ? 'success' : 'error';
        $items = $row['item_count'] > 0 ? $row['item_count'] : "NONE!";
        $resources = $row['resources'] ?? "NO ITEMS";
        
        echo "<span class='$color'>";
        echo str_pad($row['purchase_id'], 5);
        echo str_pad($row['customer_name'], 20);
        echo str_pad($row['invoice_no'], 25);
        echo str_pad($row['purchase_date'], 12);
        echo str_pad($items, 8);
        echo $resources;
        echo "</span>\n";
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
}

// Check if paystack_verify_payment.php is being called
echo "<h2>Checking Payment Flow:</h2>";
echo "<p>When a payment is made, the flow should be:</p>";
echo "<pre>";
echo "1. User completes payment on Paystack\n";
echo "2. Paystack redirects to: view/paystack_callback.php\n";
echo "3. Callback calls: actions/paystack_verify_payment.php\n";
echo "4. Verification calls: create_order_ctr(), add_order_details_ctr(), etc.\n";
echo "5. Order items should be created\n";
echo "</pre>";

echo "<p class='warning'>⚠️ If order_items are not being created, check:</p>";
echo "<ul>";
echo "<li>Is paystack_verify_payment.php being called?</li>";
echo "<li>Are there errors in the PHP error log?</li>";
echo "<li>Is the payment actually completing on Paystack?</li>";
echo "<li>Is the callback URL correct in Paystack settings?</li>";
echo "</ul>";

echo "</div></body></html>";
?>
