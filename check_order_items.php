<?php
require_once __DIR__ . '/app/config/database.php';

$conn = Database::getInstance()->getConnection();

echo "<h2>Order Items Check</h2>";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'order_items'");
if ($result->num_rows == 0) {
    echo "<p style='color: red;'><strong>ERROR:</strong> order_items table does NOT exist!</p>";
    echo "<p>You need to create it. Run this SQL:</p>";
    echo "<pre>CREATE TABLE `order_items` (
  `order_item_id` INT NOT NULL AUTO_INCREMENT,
  `purchase_id` INT NOT NULL,
  `resource_id` INT NOT NULL,
  `qty` INT DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_item_id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `resource_id` (`resource_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</pre>";
} else {
    echo "<p style='color: green;'><strong>✓</strong> order_items table exists</p>";
    
    // Check how many items
    $count = $conn->query("SELECT COUNT(*) as total FROM order_items")->fetch_assoc();
    echo "<p>Total order_items: <strong>{$count['total']}</strong></p>";
    
    if ($count['total'] == 0) {
        echo "<p style='color: orange;'><strong>WARNING:</strong> No order items exist! Purchases are not being recorded.</p>";
        
        // Check if purchases exist
        $purchases = $conn->query("SELECT COUNT(*) as total FROM purchases")->fetch_assoc();
        echo "<p>Total purchases: <strong>{$purchases['total']}</strong></p>";
        
        if ($purchases['total'] > 0) {
            echo "<p style='color: red;'><strong>PROBLEM:</strong> You have purchases but no order_items. The checkout process is broken!</p>";
        }
    } else {
        // Show recent order items
        $items = $conn->query("SELECT oi.*, p.customer_id, r.resource_title, r.cat_id 
                               FROM order_items oi 
                               JOIN purchases p ON oi.purchase_id = p.purchase_id
                               JOIN resources r ON oi.resource_id = r.resource_id
                               ORDER BY oi.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
        
        echo "<h3>Recent Order Items:</h3>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Order Item ID</th><th>Purchase ID</th><th>Customer ID</th><th>Resource</th><th>Category ID</th><th>Price</th><th>Date</th></tr>";
        foreach ($items as $item) {
            echo "<tr>";
            echo "<td>{$item['order_item_id']}</td>";
            echo "<td>{$item['purchase_id']}</td>";
            echo "<td>{$item['customer_id']}</td>";
            echo "<td>{$item['resource_title']}</td>";
            echo "<td>{$item['cat_id']}</td>";
            echo "<td>₵{$item['price']}</td>";
            echo "<td>{$item['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
?>
