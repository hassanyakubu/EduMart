<?php
require_once __DIR__ . '/../config/database.php';

class user_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($name, $email, $password, $country, $city, $contact, $user_type = 'student') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Set user_role based on user_type: 2 for creator, 3 for student
        $user_role = ($user_type === 'creator') ? 2 : 3;
        
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssis", $name, $email, $hashed_password, $country, $city, $contact, $user_role, $user_type);
        
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
        
        // Update profile but NEVER change user_role or user_type
        // This prevents privilege escalation
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
    
    public function updateUserRole($user_id, $new_role) {
        // Validate role (1=Admin, 2=Creator, 3=Student)
        if (!in_array($new_role, [1, 2, 3])) {
            return false;
        }
        
        // Set user_type based on role
        $user_type = 'student'; // default
        if ($new_role == 1) {
            $user_type = 'admin';
        } elseif ($new_role == 2) {
            $user_type = 'creator';
        }
        
        // Update both user_role and user_type
        $stmt = $this->db->prepare("UPDATE customer SET user_role = ?, user_type = ? WHERE customer_id = ?");
        $stmt->bind_param("isi", $new_role, $user_type, $user_id);
        return $stmt->execute();
    }
}
?>
