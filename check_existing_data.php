<?php
/**
 * Check Existing Payment Data
 * This script shows what data exists and what's missing
 */

require_once 'app/config/database.php';

echo "=== Checking Existing Payment Data ===\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Check purchases
    echo "1. PURCHASES TABLE:\n";
    $result = $db->query("SELECT COUNT(*) as count FROM purchases");
    $count = $result->fetch_assoc()['count'];
    echo "   Total purchases: $count\n";
    
    if ($count > 0) {
        echo "\n   Recent purchases:\n";
        $result = $db->query("SELECT purchase_id, customer_id, invoice_no, purchase_date, order_status FROM purchases ORDER BY purchase_id DESC LIMIT 5");
        while ($row = $result->fetch_assoc()) {
            echo "   - ID: {$row['purchase_id']}, Customer: {$row['customer_id']}, Invoice: {$row['invoice_no']}, Date: {$row['purchase_date']}, Status: {$row['order_status']}\n";
        }
    }
    
    // Check order_items
    echo "\n2. ORDER_ITEMS TABLE:\n";
    $result = $db->query("SELECT COUNT(*) as count FROM order_items");
    $count = $result->fetch_assoc()['count'];
    echo "   Total order items: $count\n";
    
    if ($count > 0) {
        echo "\n   Recent order items:\n";
        $result = $db->query("
            SELECT oi.order_item_id, oi.purchase_id, r.resource_title, oi.qty, oi.price 
            FROM order_items oi
            JOIN resources r ON oi.resource_id = r.resource_id
            ORDER BY oi.order_item_id DESC LIMIT 5
        ");
        while ($row = $result->fetch_assoc()) {
            echo "   - Item ID: {$row['order_item_id']}, Purchase: {$row['purchase_id']}, Resource: {$row['resource_title']}, Qty: {$row['qty']}, Price: {$row['price']}\n";
        }
    }
    
    // Check downloads
    echo "\n3. DOWNLOADS TABLE:\n";
    $result = $db->query("SELECT COUNT(*) as count FROM downloads");
    $count = $result->fetch_assoc()['count'];
    echo "   Total downloads: $count\n";
    
    if ($count > 0) {
        echo "\n   Recent downloads:\n";
        $result = $db->query("
            SELECT d.download_id, d.customer_id, r.resource_title, d.purchase_id 
            FROM downloads d
            JOIN resources r ON d.resource_id = r.resource_id
            ORDER BY d.download_id DESC LIMIT 5
        ");
        while ($row = $result->fetch_assoc()) {
            echo "   - Download ID: {$row['download_id']}, Customer: {$row['customer_id']}, Resource: {$row['resource_title']}, Purchase: {$row['purchase_id']}\n";
        }
    }
    
    // Check for orphaned purchases (purchases without order_items)
    echo "\n4. ORPHANED PURCHASES (purchases without order_items):\n";
    $result = $db->query("
        SELECT p.purchase_id, p.customer_id, c.customer_name, p.invoice_no, p.purchase_date, p.order_status
        FROM purchases p
        JOIN customer c ON p.customer_id = c.customer_id
        LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
        WHERE oi.order_item_id IS NULL
        ORDER BY p.purchase_id DESC
    ");
    
    $orphan_count = $result->num_rows;
    echo "   Found $orphan_count orphaned purchases\n";
    
    if ($orphan_count > 0) {
        echo "\n   ⚠️  These purchases have NO order_items:\n";
        while ($row = $result->fetch_assoc()) {
            echo "   - Purchase ID: {$row['purchase_id']}, Customer: {$row['customer_name']}, Invoice: {$row['invoice_no']}, Date: {$row['purchase_date']}\n";
        }
        
        echo "\n   ❌ PROBLEM: These students cannot access quizzes because there are no order_items!\n";
        echo "   💡 SOLUTION: You need to manually add order_items for these purchases if you know what they bought.\n";
    } else {
        echo "   ✓ All purchases have order_items!\n";
    }
    
    // Check payments table
    echo "\n5. PAYMENTS TABLE:\n";
    $result = $db->query("SHOW TABLES LIKE 'payments'");
    if ($result->num_rows > 0) {
        $result = $db->query("SELECT COUNT(*) as count FROM payments");
        $count = $result->fetch_assoc()['count'];
        echo "   Total payments: $count\n";
        
        if ($count > 0) {
            echo "\n   Recent payments:\n";
            $result = $db->query("
                SELECT payment_id, purchase_id, customer_id, amount, payment_reference, payment_date 
                FROM payments 
                ORDER BY payment_id DESC LIMIT 5
            ");
            while ($row = $result->fetch_assoc()) {
                echo "   - Payment ID: {$row['payment_id']}, Purchase: {$row['purchase_id']}, Customer: {$row['customer_id']}, Amount: {$row['amount']}, Ref: {$row['payment_reference']}\n";
            }
        }
    } else {
        echo "   ⚠️  Payments table does NOT exist yet!\n";
        echo "   💡 Run db/fix_purchases_table.sql to create it.\n";
    }
    
    // Summary
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "SUMMARY:\n";
    echo str_repeat("=", 70) . "\n";
    
    $purchases_result = $db->query("SELECT COUNT(*) as count FROM purchases");
    $purchases_count = $purchases_result->fetch_assoc()['count'];
    
    $items_result = $db->query("SELECT COUNT(*) as count FROM order_items");
    $items_count = $items_result->fetch_assoc()['count'];
    
    $orphan_result = $db->query("
        SELECT COUNT(*) as count 
        FROM purchases p 
        LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id 
        WHERE oi.order_item_id IS NULL
    ");
    $orphan_count = $orphan_result->fetch_assoc()['count'];
    
    echo "\nTotal Purchases: $purchases_count\n";
    echo "Purchases with order_items: " . ($purchases_count - $orphan_count) . "\n";
    echo "Purchases WITHOUT order_items: $orphan_count\n";
    
    if ($orphan_count > 0) {
        echo "\n⚠️  ACTION REQUIRED:\n";
        echo "You have $orphan_count purchases that need order_items added.\n";
        echo "Without order_items, students cannot access quizzes for these purchases.\n";
        echo "\nOptions:\n";
        echo "1. If you know what they bought, run backfill_missing_orders.php\n";
        echo "2. If you don't know, these purchases are lost (students need to repurchase)\n";
        echo "3. Check Paystack dashboard for transaction history\n";
    } else {
        echo "\n✓ All purchases have order_items - system is healthy!\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
