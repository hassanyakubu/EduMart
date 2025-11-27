<?php
/**
 * Check Detailed Logs
 */

echo "<pre style='background: #000; color: #0f0; padding: 20px; font-family: monospace;'>";
echo "=== CHECKING PAYMENT LOGS ===\n\n";

// Check if error log exists
$log_paths = [
    '/Applications/XAMPP/xamppfiles/logs/php_error_log',
    __DIR__ . '/error_log',
    ini_get('error_log'),
    '/var/log/apache2/error.log',
    '/var/log/httpd/error_log'
];

$found = false;
foreach ($log_paths as $path) {
    if (file_exists($path) && is_readable($path)) {
        echo "Found log: $path\n\n";
        $found = true;
        
        // Get last 200 lines
        $lines = file($path);
        $recent = array_slice($lines, -200);
        
        // Filter for today's logs
        $today = date('Y-m-d');
        $payment_logs = array_filter($recent, function($line) use ($today) {
            return (stripos($line, 'paystack') !== false || 
                    stripos($line, 'cart') !== false ||
                    stripos($line, 'order') !== false ||
                    stripos($line, 'checkout') !== false) &&
                   stripos($line, $today) !== false;
        });
        
        if (!empty($payment_logs)) {
            echo "=== PAYMENT-RELATED LOGS FROM TODAY ===\n";
            foreach ($payment_logs as $log) {
                echo $log;
            }
        } else {
            echo "No payment logs found for today.\n";
        }
        break;
    }
}

if (!$found) {
    echo "No error log found. Logs might be disabled or in a different location.\n";
}

// Check session
echo "\n=== SESSION CHECK ===\n";
session_start();
echo "Session ID: " . session_id() . "\n";
echo "Session data:\n";
print_r($_SESSION);

// Check if paystack_verify_payment.php is even being called
echo "\n=== CHECKING IF VERIFICATION IS CALLED ===\n";
echo "To test: Make a payment and immediately check this page.\n";
echo "You should see log entries with timestamps from just now.\n";

// Check database for latest purchase
echo "\n=== LATEST PURCHASE ===\n";
try {
    require_once __DIR__ . '/app/config/database.php';
    $db = Database::getInstance()->getConnection();
    
    $result = $db->query("
        SELECT p.*, 
               COUNT(oi.order_item_id) as items,
               GROUP_CONCAT(r.resource_title) as resources
        FROM purchases p
        LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
        LEFT JOIN resources r ON oi.resource_id = r.resource_id
        GROUP BY p.purchase_id
        ORDER BY p.purchase_id DESC
        LIMIT 1
    ");
    
    if ($row = $result->fetch_assoc()) {
        echo "Purchase ID: {$row['purchase_id']}\n";
        echo "Customer ID: {$row['customer_id']}\n";
        echo "Invoice: {$row['invoice_no']}\n";
        echo "Date: {$row['purchase_date']}\n";
        echo "Status: {$row['order_status']}\n";
        echo "Items: {$row['items']}\n";
        echo "Resources: " . ($row['resources'] ?? 'NONE') . "\n";
        
        if ($row['items'] == 0) {
            echo "\n❌ NO ORDER ITEMS! This is the problem.\n";
        } else {
            echo "\n✓ Has order items!\n";
        }
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Make a test payment\n";
echo "2. Immediately refresh this page\n";
echo "3. Look for log entries with current timestamp\n";
echo "4. Check if 'Cart items stored in session' appears\n";
echo "5. Check if 'Cart retrieved from session' appears\n";

echo "</pre>";
?>
