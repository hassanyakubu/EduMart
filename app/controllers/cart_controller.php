<?php
session_start();
require_once __DIR__ . '/../models/cart_model.php';

class cart_controller {
    private $cartModel;
    
    public function __construct() {
        $this->cartModel = new cart_model();
    }
    
    public function add($resource_id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to add items to cart.';
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $this->cartModel->addItem($_SESSION['user_id'], $resource_id);
        $_SESSION['success'] = 'Item added to cart!';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/app/views/resources/list.php'));
        exit;
    }
    
    public function remove($resource_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $this->cartModel->removeItem($_SESSION['user_id'], $resource_id);
        $_SESSION['success'] = 'Item removed from cart.';
        header('Location: /app/views/cart/view.php');
        exit;
    }
    
    public function view() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $cart_items = $this->cartModel->getUserCart($_SESSION['user_id']);
        $total = $this->cartModel->getTotal($_SESSION['user_id']);
        
        require_once __DIR__ . '/../views/cart/view.php';
    }
}
?>
