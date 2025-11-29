<?php
// Cart Model - handles shopping cart stuff
require_once __DIR__ . '/../config/database.php';

class cart_model {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Get user's cart or make a new one if they don't have one yet
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
    
    // Get everything in the user's cart
    public function getUserCart($user_id) {
        $cart_id = $this->getOrCreateCart($user_id);
        
        // Get cart items with resource info
        $stmt = $this->db->prepare("SELECT ci.*, r.resource_title, r.resource_price, r.resource_image
                FROM cart_items ci
                JOIN resources r ON ci.resource_id = r.resource_id
                WHERE ci.cart_id = ?");
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Add something to cart (won't add duplicates since these are digital resources)
    public function addItem($user_id, $resource_id, $qty = 1) {
        $cart_id = $this->getOrCreateCart($user_id);
        
        // Check if it's already in the cart
        $stmt = $this->db->prepare("SELECT item_id, qty FROM cart_items WHERE cart_id = ? AND resource_id = ?");
        $stmt->bind_param("ii", $cart_id, $resource_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Already in cart, don't add again (you can only buy a digital resource once)
            return true;
        }
        
        // Add to cart with qty of 1
        $qty = 1;
        $stmt = $this->db->prepare("INSERT INTO cart_items (cart_id, resource_id, qty) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $cart_id, $resource_id, $qty);
        return $stmt->execute();
    }
    
    // Remove something from cart
    public function removeItem($user_id, $resource_id) {
        $cart_id = $this->getOrCreateCart($user_id);
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = ? AND resource_id = ?");
        $stmt->bind_param("ii", $cart_id, $resource_id);
        return $stmt->execute();
    }
    
    // Calculate how much everything in the cart costs
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
    
    // Empty the cart after checkout
    public function clearCart($user_id) {
        $cart_id = $this->getOrCreateCart($user_id);
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->bind_param("i", $cart_id);
        return $stmt->execute();
    }
}
?>
