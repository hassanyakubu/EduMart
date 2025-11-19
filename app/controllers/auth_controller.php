<?php
session_start();
require_once __DIR__ . '/../models/user_model.php';

class auth_controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new user_model();
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $country = $_POST['country'] ?? '';
            $city = $_POST['city'] ?? '';
            $contact = $_POST['contact'] ?? '';
            
            if ($this->userModel->register($name, $email, $password, $country, $city, $contact)) {
                $_SESSION['success'] = 'Registration successful! Please login.';
                header('Location: /app/views/auth/login.php');
                exit;
            } else {
                $_SESSION['error'] = 'Registration failed. Email may already exist.';
            }
        }
        
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->userModel->login($email, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['customer_id'];
                $_SESSION['user_name'] = $user['customer_name'];
                $_SESSION['user_email'] = $user['customer_email'];
                $_SESSION['user_role'] = $user['user_role'];
                
                if ($user['user_role'] == 1) {
                    header('Location: /app/views/admin/dashboard.php');
                } else {
                    header('Location: /app/views/home/index.php');
                }
                exit;
            } else {
                $_SESSION['error'] = 'Invalid email or password.';
            }
        }
        
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function logout() {
        session_destroy();
        header('Location: /app/views/auth/login.php');
        exit;
    }
}
?>
