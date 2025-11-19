<?php
require_once __DIR__ . '/../config/database.php';

class creator_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $result = $this->db->query("SELECT * FROM creators ORDER BY creator_name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM creators WHERE creator_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function create($name, $created_by) {
        $stmt = $this->db->prepare("INSERT INTO creators (creator_name, created_by) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $created_by);
        return $stmt->execute();
    }
}
?>
