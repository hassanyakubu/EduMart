<?php
session_start();
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/download_model.php';
require_once __DIR__ . '/../models/order_model.php';

class profile_controller {
    private $userModel;
    private $downloadModel;
    private $orderModel;
    
    public function __construct() {
        $this->userModel = new user_model();
        $this->downloadModel = new download_model();
        $this->orderModel = new order_model();
    }
    
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $user = $this->userModel->getById($_SESSION['user_id']);
        $downloads = $this->downloadModel->getUserDownloads($_SESSION['user_id']);
        $orders = $this->orderModel->getOrdersByUser($_SESSION['user_id']);
        
        require_once __DIR__ . '/../views/profile/dashboard.php';
    }
    
    public function update() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/auth/login.php'));
            exit;
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $country = $_POST['country'] ?? '';
        $city = $_POST['city'] ?? '';
        $contact = $_POST['contact'] ?? '';
        
        if ($this->userModel->updateProfile($_SESSION['user_id'], $name, $email, $country, $city, $contact)) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['success'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update profile. Email may already be in use.';
        }
        
        require_once __DIR__ . '/../config/config.php';
        header('Location: ' . url('app/views/profile/dashboard.php'));
        exit;
    }
}
?>
