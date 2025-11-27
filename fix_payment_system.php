<?php
/**
 * Fix Payment System Database Schema
 * This script fixes the database schema to properly support payments
 */

require_once __DIR__ . '/app/config/database.php';

echo "=== EduMart Payment System Fix ===\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Fix purchases table - change invoice_no to VARCHAR
    echo "1. Fixing purchases table (invoice_no column)...\n";
    $sql1 = "ALTER TABLE `purchases` MODIFY COLUMN `invoice_no` VARCHAR(50) NOT NULL";
    
    if ($db->query($sql1)) {
        echo "   ✓ Successfully updated invoice_no column to VARCHAR(50)\n\n";
    } else {
        echo "   ⚠ Warning: " . $db->error . "\n\n";
    }
    
    // 2. Create payments table
    echo "2. Creating payments table...\n";
    $sql2 = "CREATE TABLE IF NOT EXISTS `payments` (
      `payment_id` int NOT NULL AUTO_INCREMENT,
      `purchase_id` int NOT NULL,
      `customer_id` int NOT NULL,
      `amount` decimal(10,2) NOT NULL,
      `currency` varchar(10) DEFAULT 'GHS',
      `payment_method` varchar(50) DEFAULT 'paystack',
      `payment_reference` varchar(255) NOT NULL,
      `authorization_code` varchar(255) DEFAULT NULL,
      `payment_channel` varchar(50) DEFAULT NULL,
      `payment_date` date NOT NULL,
      `payment_status` varchar(50) DEFAULT 'success',
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`payment_id`),
      KEY `purchase_id` (`purchase_id`),
      KEY `customer_id` (`customer_id`),
      KEY `payment_reference` (`payment_reference`),
      CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE,
      CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    
    if ($db->query($sql2)) {
        echo "   ✓ Successfully created payments table\n\n";
    } else {
        echo "   ⚠ Warning: " . $db->error . "\n\n";
    }
    
    // 3. Verify tables exist
    echo "3. Verifying database structure...\n";
    
    $tables_to_check = ['purchases', 'order_items', 'downloads', 'payments'];
    foreach ($tables_to_check as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "   ✓ Table '$table' exists\n";
        } else {
            echo "   ✗ Table '$table' is missing!\n";
        }
    }
    
    echo "\n4. Checking column types...\n";
    $result = $db->query("DESCRIBE purchases");
    while ($row = $result->fetch_assoc()) {
        if ($row['Field'] === 'invoice_no') {
            echo "   ✓ invoice_no type: " . $row['Type'] . "\n";
        }
    }
    
    echo "\n=== Fix Complete ===\n";
    echo "The payment system database schema has been updated.\n";
    echo "You can now process payments and they will be properly recorded.\n\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
