<?php
require_once __DIR__ . '/../config/database.php';

class review_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function addReview($user_id, $resource_id, $rating, $comment) {
        // Check if user already reviewed
        $stmt = $this->db->prepare("SELECT review_id FROM reviews WHERE user_id = ? AND resource_id = ?");
        $stmt->bind_param("ii", $user_id, $resource_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            return false; // Already reviewed
        }
        
        $stmt = $this->db->prepare("INSERT INTO reviews (user_id, resource_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $resource_id, $rating, $comment);
        return $stmt->execute();
    }
    
    public function getByResource($resource_id) {
        $stmt = $this->db->prepare("SELECT r.*, c.customer_name, c.customer_image
                FROM reviews r
                JOIN customer c ON r.user_id = c.customer_id
                WHERE r.resource_id = ?
                ORDER BY r.created_at DESC");
        $stmt->bind_param("i", $resource_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAverageRating($resource_id) {
        $stmt = $this->db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE resource_id = ?");
        $stmt->bind_param("i", $resource_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
