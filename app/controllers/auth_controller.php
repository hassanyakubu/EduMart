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
            $user_type = $_POST['user_type'] ?? 'student';
            
            require_once __DIR__ . '/../config/config.php';
            
            if ($this->userModel->register($name, $email, $password, $country, $city, $contact, $user_type)) {
                $_SESSION['success'] = 'Registration successful! Please login.';
                header('Location: ' . url('app/views/auth/login.php'));
                exit;
            } else {
                $_SESSION['error'] = 'Registration failed. Email may already exist.';
                header('Location: ' . url('app/views/auth/register.php'));
                exit;
            }
        }
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->userModel->login($email, $password);
            
            require_once __DIR__ . '/../config/config.php';
            
            if ($user) {
                $_SESSION['user_id'] = $user['customer_id'];
                $_SESSION['user_name'] = $user['customer_name'];
                $_SESSION['user_email'] = $user['customer_email'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['user_type'] = $user['user_type'] ?? 'student';
                
                // Check if there's a redirect URL stored
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect);
                } else if ($user['user_role'] == 1) {
                    header('Location: ' . url('app/views/admin/dashboard.php'));
                } else {
                    header('Location: ' . url('app/views/profile/dashboard.php'));
                }
                exit;
            } else {
                $_SESSION['error'] = 'Invalid email or password.';
                header('Location: ' . url('app/views/auth/login.php'));
                exit;
            }
        }
    }
    
    public function logout() {
        session_destroy();
        require_once __DIR__ . '/../config/config.php';
        header('Location: ' . url('app/views/home/index.php'));
        exit;
    }
}
?>
