<?php
// Resource Model - handles past questions and study materials
require_once __DIR__ . '/../config/database.php';

class resource_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Get all resources with their ratings and creator info
    public function getAll($limit = null) {
        $sql = "SELECT r.*, c.cat_name, cr.creator_name, 
                COALESCE(AVG(rv.rating), 0) as avg_rating,
                COUNT(DISTINCT rv.review_id) as review_count
                FROM resources r
                LEFT JOIN categories c ON r.cat_id = c.cat_id
                LEFT JOIN creators cr ON r.creator_id = cr.creator_id
                LEFT JOIN reviews rv ON r.resource_id = rv.resource_id
                GROUP BY r.resource_id
                ORDER BY r.resource_id DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get one resource by its ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT r.*, c.cat_name, cr.creator_name,
                COALESCE(AVG(rv.rating), 0) as avg_rating,
                COUNT(DISTINCT rv.review_id) as review_count
                FROM resources r
                LEFT JOIN categories c ON r.cat_id = c.cat_id
                LEFT JOIN creators cr ON r.creator_id = cr.creator_id
                LEFT JOIN reviews rv ON r.resource_id = rv.resource_id
                WHERE r.resource_id = ?
                GROUP BY r.resource_id");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Search resources with filters (keyword, category, price range, creator)
    public function search($keyword = '', $category = null, $minPrice = null, $maxPrice = null, $creator = null) {
        $sql = "SELECT r.*, c.cat_name, cr.creator_name,
                COALESCE(AVG(rv.rating), 0) as avg_rating,
                COUNT(DISTINCT rv.review_id) as review_count
                FROM resources r
                LEFT JOIN categories c ON r.cat_id = c.cat_id
                LEFT JOIN creators cr ON r.creator_id = cr.creator_id
                LEFT JOIN reviews rv ON r.resource_id = rv.resource_id
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Search by keyword in title, keywords, description, or creator name
        if (!empty($keyword)) {
            $sql .= " AND (r.resource_title LIKE ? OR r.resource_keywords LIKE ? OR r.resource_desc LIKE ? OR cr.creator_name LIKE ?)";
            $params = array_merge($params, ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"]);
            $types .= "ssss";
        }
        
        if ($category !== null && $category !== '') {
            $sql .= " AND r.cat_id = ?";
            $params[] = $category;
            $types .= "i";
        }
        
        if ($creator !== null && $creator !== '') {
            $sql .= " AND r.creator_id = ?";
            $params[] = $creator;
            $types .= "i";
        }
        
        if ($minPrice !== null && $minPrice !== '') {
            $sql .= " AND r.resource_price >= ?";
            $params[] = floatval($minPrice);
            $types .= "d";
        }
        
        if ($maxPrice !== null && $maxPrice !== '') {
            $sql .= " AND r.resource_price <= ?";
            $params[] = floatval($maxPrice);
            $types .= "d";
        }
        
        $sql .= " GROUP BY r.resource_id ORDER BY r.resource_id DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("SQL Error: " . $this->db->error);
            return [];
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            error_log("Execute Error: " . $stmt->error);
            return [];
        }
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Create a new resource
    public function create($cat_id, $creator_id, $title, $price, $desc, $image, $keywords, $file) {
        $stmt = $this->db->prepare("INSERT INTO resources (cat_id, creator_id, resource_title, resource_price, resource_desc, resource_image, resource_keywords, resource_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidsssss", $cat_id, $creator_id, $title, $price, $desc, $image, $keywords, $file);
        return $stmt->execute();
    }
    
    // Delete a resource
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM resources WHERE resource_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    // Track downloads (can add download counter later if needed)
    public function updateDownloads($id) {
        return true;
    }
}
?>
