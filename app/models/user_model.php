<?php
require_once __DIR__ . '/../config/database.php';

class user_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($name, $email, $password, $country, $city, $contact) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $user_role = 2; // Regular user
        
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $email, $hashed_password, $country, $city, $contact, $user_role);
        
        return $stmt->execute();
    }
    
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['customer_pass'])) {
                return $user;
            }
        }
        return false;
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function update($id, $name, $country, $city, $contact) {
        $stmt = $this->db->prepare("UPDATE customer SET customer_name = ?, customer_country = ?, customer_city = ?, customer_contact = ? WHERE customer_id = ?");
        $stmt->bind_param("ssssi", $name, $country, $city, $contact, $id);
        return $stmt->execute();
    }
    
    public function updateProfile($id, $name, $email, $country, $city, $contact) {
        // Check if email is already used by another user
        $stmt = $this->db->prepare("SELECT customer_id FROM customer WHERE customer_email = ? AND customer_id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return false; // Email already in use
        }
        
        $stmt = $this->db->prepare("UPDATE customer SET customer_name = ?, customer_email = ?, customer_country = ?, customer_city = ?, customer_contact = ? WHERE customer_id = ?");
        $stmt->bind_param("sssssi", $name, $email, $country, $city, $contact, $id);
        return $stmt->execute();
    }
    
    public function getAll() {
        $result = $this->db->query("SELECT * FROM customer ORDER BY customer_id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
