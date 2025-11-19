<?php
require_once __DIR__ . '/../config/database.php';

class download_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function logDownload($customer_id, $resource_id, $purchase_id) {
        $stmt = $this->db->prepare("INSERT INTO downloads (customer_id, resource_id, purchase_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $customer_id, $resource_id, $purchase_id);
        return $stmt->execute();
    }
    
    public function hasAccess($customer_id, $resource_id) {
        $stmt = $this->db->prepare("SELECT d.download_id FROM downloads d
                JOIN purchases p ON d.purchase_id = p.purchase_id
                WHERE d.customer_id = ? AND d.resource_id = ? AND p.order_status = 'completed'");
        $stmt->bind_param("ii", $customer_id, $resource_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
    
    public function getUserDownloads($customer_id) {
        $stmt = $this->db->prepare("SELECT d.*, r.resource_title, r.resource_file, r.resource_image
                FROM downloads d
                JOIN resources r ON d.resource_id = r.resource_id
                WHERE d.customer_id = ?
                ORDER BY d.download_date DESC");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
