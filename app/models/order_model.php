<?php
require_once __DIR__ . '/../config/database.php';

class order_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function createOrder($customer_id, $invoice_no, $order_status = 'pending') {
        $purchase_date = date('Y-m-d');
        $stmt = $this->db->prepare("INSERT INTO purchases (customer_id, invoice_no, purchase_date, order_status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $customer_id, $invoice_no, $purchase_date, $order_status);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    public function getOrdersByUser($customer_id) {
        $stmt = $this->db->prepare("SELECT p.*, 
                COUNT(DISTINCT d.resource_id) as resource_count,
                SUM(r.resource_price) as total_amount
                FROM purchases p
                LEFT JOIN downloads d ON p.purchase_id = d.purchase_id
                LEFT JOIN resources r ON d.resource_id = r.resource_id
                WHERE p.customer_id = ?
                GROUP BY p.purchase_id
                ORDER BY p.purchase_date DESC");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getOrderById($purchase_id) {
        $stmt = $this->db->prepare("SELECT * FROM purchases WHERE purchase_id = ?");
        $stmt->bind_param("i", $purchase_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getOrderItems($purchase_id) {
        $stmt = $this->db->prepare("SELECT d.*, r.resource_title, r.resource_price, r.resource_file, r.resource_image
                FROM downloads d
                JOIN resources r ON d.resource_id = r.resource_id
                WHERE d.purchase_id = ?");
        $stmt->bind_param("i", $purchase_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function updateStatus($purchase_id, $status) {
        $stmt = $this->db->prepare("UPDATE purchases SET order_status = ? WHERE purchase_id = ?");
        $stmt->bind_param("si", $status, $purchase_id);
        return $stmt->execute();
    }
    
    public function getAllOrders() {
        $result = $this->db->query("SELECT p.*, c.customer_name, c.customer_email,
                COUNT(DISTINCT d.resource_id) as resource_count
                FROM purchases p
                JOIN customer c ON p.customer_id = c.customer_id
                LEFT JOIN downloads d ON p.purchase_id = d.purchase_id
                GROUP BY p.purchase_id
                ORDER BY p.purchase_date DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
