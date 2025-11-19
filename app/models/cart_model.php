<?php
require_once __DIR__ . '/../config/database.php';

class cart_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getOrCreateCart($user_id) {
        $stmt = $this->db->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['cart_id'];
        }
        
        $stmt = $this->db->prepare("INSERT INTO carts (user_id) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $this->db->insert_id;
    }
    
    public function getUserCart($user_id) {
        $cart_id = $this->getOrCreateCart($user_id);
        
        $stmt = $this->db->prepare("SELECT ci.*, r.resource_title, r.resource_price, r.resource_image
                FROM cart_items ci
                JOIN resources r ON ci.resource_id = r.resource_id
                WHERE ci.cart_id = ?");
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function addItem($user_id, $resource_id, $qty = 1) {
        $cart_id = $this->getOrCreateCart($user_id);
        
        // Check if item already exists
        $stmt = $this->db->prepare("SELECT item_id, qty FROM cart_items WHERE cart_id = ? AND resource_id = ?");
        $stmt->bind_param("ii", $cart_id, $resource_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
            $new_qty = $item['qty'] + $qty;
            $stmt = $this->db->prepare("UPDATE cart_items SET qty = ? WHERE item_id = ?");
            $stmt->bind_param("ii", $new_qty, $item['item_id']);
            return $stmt->execute();
        }
        
        $stmt = $this->db->prepare("INSERT INTO cart_items (cart_id, resource_id, qty) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $cart_id, $resource_id, $qty);
        return $stmt->execute();
    }
    
    public function removeItem($user_id, $resource_id) {
        $cart_id = $this->getOrCreateCart($user_id);
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = ? AND resource_id = ?");
        $stmt->bind_param("ii", $cart_id, $resource_id);
        return $stmt->execute();
    }
    
    public function getTotal($user_id) {
        $cart_id = $this->getOrCreateCart($user_id);
        $stmt = $this->db->prepare("SELECT SUM(r.resource_price * ci.qty) as total
                FROM cart_items ci
                JOIN resources r ON ci.resource_id = r.resource_id
                WHERE ci.cart_id = ?");
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
    
    public function clearCart($user_id) {
        $cart_id = $this->getOrCreateCart($user_id);
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->bind_param("i", $cart_id);
        return $stmt->execute();
    }
}
?>
