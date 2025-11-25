<?php
require_once __DIR__ . '/../config/database.php';

class resource_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
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
        
        // Add keyword search if provided
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
        
        // Debug: Log the SQL query
        error_log("Search SQL: " . $sql);
        error_log("Search Params: " . print_r($params, true));
        error_log("Search Types: " . $types);
        
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("SQL Prepare Error: " . $this->db->error);
            return [];
        }
        
        // Only bind params if we have any
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            error_log("SQL Execute Error: " . $stmt->error);
            return [];
        }
        
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        error_log("Search found " . count($result) . " results");
        
        return $result;
    }
    
    public function create($cat_id, $creator_id, $title, $price, $desc, $image, $keywords, $file) {
        $stmt = $this->db->prepare("INSERT INTO resources (cat_id, creator_id, resource_title, resource_price, resource_desc, resource_image, resource_keywords, resource_file) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidsssss", $cat_id, $creator_id, $title, $price, $desc, $image, $keywords, $file);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM resources WHERE resource_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function updateDownloads($id) {
        // This can be extended if you add a downloads_count column
        return true;
    }
}
?>
