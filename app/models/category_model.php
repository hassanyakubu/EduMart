<?php
require_once __DIR__ . '/../config/database.php';

class category_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $result = $this->db->query("SELECT * FROM categories ORDER BY cat_name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function create($name) {
        $stmt = $this->db->prepare("INSERT INTO categories (cat_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }
    
    public function update($id, $name) {
        $stmt = $this->db->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
