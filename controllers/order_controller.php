<?php
/**
 * Order Controller Helper Functions
 * These are wrapper functions for backward compatibility
 */

require_once __DIR__ . '/../app/models/order_model.php';
require_once __DIR__ . '/../app/config/database.php';

/**
 * Create a new order
 */
function create_order_ctr($customer_id, $invoice_no, $order_date, $order_status = 'Paid') {
    $orderModel = new order_model();
    
    // Create order with proper invoice number
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("INSERT INTO purchases (customer_id, invoice_no, purchase_date, order_status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $customer_id, $invoice_no, $order_date, $order_status);
    
    if ($stmt->execute()) {
        $purchase_id = $db->insert_id;
        error_log("Order created successfully - ID: $purchase_id, Invoice: $invoice_no");
        return $purchase_id;
    }
    
    error_log("Failed to create order: " . $stmt->error);
    return false;
}

/**
 * Add order details (order items)
 */
function add_order_details_ctr($purchase_id, $resource_id, $qty = 1) {
    $db = Database::getInstance()->getConnection();
    
    // Get resource price
    $stmt = $db->prepare("SELECT resource_price FROM resources WHERE resource_id = ?");
    $stmt->bind_param("i", $resource_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("Resource not found: $resource_id");
        return false;
    }
    
    $resource = $result->fetch_assoc();
    $price = $resource['resource_price'];
    
    // Insert order item
    $stmt = $db->prepare("INSERT INTO order_items (purchase_id, resource_id, qty, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $purchase_id, $resource_id, $qty, $price);
    
    if ($stmt->execute()) {
        error_log("Order item added - Purchase: $purchase_id, Resource: $resource_id, Qty: $qty, Price: $price");
        
        // Also add to downloads table for access control
        $stmt2 = $db->prepare("INSERT INTO downloads (customer_id, resource_id, purchase_id) 
                               SELECT customer_id, ?, ? FROM purchases WHERE purchase_id = ?");
        $stmt2->bind_param("iii", $resource_id, $purchase_id, $purchase_id);
        $stmt2->execute();
        
        return true;
    }
    
    error_log("Failed to add order item: " . $stmt->error);
    return false;
}

/**
 * Record payment details
 */
function record_payment_ctr($amount, $customer_id, $purchase_id, $currency, $payment_date, $payment_method, $reference, $authorization_code = null, $channel = null) {
    $db = Database::getInstance()->getConnection();
    
    // Check if payments table exists, if not create it
    $table_check = $db->query("SHOW TABLES LIKE 'payments'");
    if ($table_check->num_rows === 0) {
        // Create payments table
        $create_table = "CREATE TABLE `payments` (
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
        
        if ($db->query($create_table)) {
            error_log("Payments table created successfully");
        } else {
            error_log("Failed to create payments table: " . $db->error);
        }
    }
    
    // Insert payment record
    $stmt = $db->prepare("INSERT INTO payments (purchase_id, customer_id, amount, currency, payment_method, payment_reference, authorization_code, payment_channel, payment_date, payment_status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'success')");
    $stmt->bind_param("iidssssss", $purchase_id, $customer_id, $amount, $currency, $payment_method, $reference, $authorization_code, $channel, $payment_date);
    
    if ($stmt->execute()) {
        $payment_id = $db->insert_id;
        error_log("Payment recorded - ID: $payment_id, Reference: $reference, Amount: $amount $currency");
        return $payment_id;
    }
    
    error_log("Failed to record payment: " . $stmt->error);
    return false;
}
?>
