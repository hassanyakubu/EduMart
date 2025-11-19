<?php
session_start();
require_once __DIR__ . '/../models/order_model.php';

class order_controller {
    private $orderModel;
    
    public function __construct() {
        $this->orderModel = new order_model();
    }
    
    public function list() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $orders = $this->orderModel->getOrdersByUser($_SESSION['user_id']);
        
        require_once __DIR__ . '/../views/orders/list.php';
    }
    
    public function invoice($order_id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $order = $this->orderModel->getOrderById($order_id);
        $order_items = $this->orderModel->getOrderItems($order_id);
        
        if (!$order || $order['customer_id'] != $_SESSION['user_id']) {
            header('Location: /app/views/orders/list.php');
            exit;
        }
        
        require_once __DIR__ . '/../views/orders/invoice.php';
    }
}
?>
