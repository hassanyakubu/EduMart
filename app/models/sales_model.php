<?php
require_once __DIR__ . '/../config/database.php';

class sales_model {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }
    
    public function getCreatorSales($creator_id) {
        $stmt = $this->conn->prepare("
            SELECT 
                oi.*, 
                r.resource_title,
                r.resource_price,
                p.purchase_date,
                p.invoice_no,
                c.customer_name as buyer_name
            FROM order_items oi
            JOIN resources r ON oi.resource_id = r.resource_id
            JOIN purchases p ON oi.purchase_id = p.purchase_id
            JOIN customer c ON p.customer_id = c.customer_id
            WHERE r.creator_id = ?
            ORDER BY p.purchase_date DESC
        ");
        $stmt->bind_param("i", $creator_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getCreatorEarnings($creator_id, $is_admin = false) {
        $commission_rate = $is_admin ? 1.0 : 0.8; // Admin gets 100%, others get 80%
        
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(DISTINCT oi.order_item_id) as total_sales,
                SUM(r.resource_price) as gross_revenue,
                SUM(r.resource_price * ?) as net_earnings
            FROM order_items oi
            JOIN resources r ON oi.resource_id = r.resource_id
            WHERE r.creator_id = ?
        ");
        $stmt->bind_param("di", $commission_rate, $creator_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getPlatformRevenue() {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(DISTINCT oi.order_item_id) as total_sales,
                SUM(r.resource_price) as gross_revenue,
                SUM(r.resource_price * 0.2) as platform_commission
            FROM order_items oi
            JOIN resources r ON oi.resource_id = r.resource_id
            JOIN creators cr ON r.creator_id = cr.creator_id
            WHERE cr.created_by != 1
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getTopSellingResources($limit = 10) {
        $stmt = $this->conn->prepare("
            SELECT 
                r.resource_id,
                r.resource_title,
                r.resource_price,
                cr.creator_name,
                COUNT(oi.order_item_id) as sales_count,
                SUM(r.resource_price) as total_revenue
            FROM resources r
            JOIN creators cr ON r.creator_id = cr.creator_id
            LEFT JOIN order_items oi ON r.resource_id = oi.resource_id
            GROUP BY r.resource_id
            ORDER BY sales_count DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
