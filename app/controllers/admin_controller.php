<?php
session_start();
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/resource_model.php';
require_once __DIR__ . '/../models/category_model.php';
require_once __DIR__ . '/../models/order_model.php';

class admin_controller {
    private $userModel;
    private $resourceModel;
    private $categoryModel;
    private $orderModel;
    
    public function __construct() {
        $this->userModel = new user_model();
        $this->resourceModel = new resource_model();
        $this->categoryModel = new category_model();
        $this->orderModel = new order_model();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/auth/login.php'));
            exit;
        }
    }
    
    public function dashboard() {
        $total_users = count($this->userModel->getAll());
        $total_resources = count($this->resourceModel->getAll());
        $total_orders = count($this->orderModel->getAllOrders());
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function manageUsers() {
        $users = $this->userModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
            $this->userModel->delete($_POST['user_id']);
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/admin/users.php'));
            exit;
        }
        
        require_once __DIR__ . '/../views/admin/users.php';
    }
    
    public function manageResources() {
        $resources = $this->resourceModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_resource'])) {
            $this->resourceModel->delete($_POST['resource_id']);
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/admin/resources.php'));
            exit;
        }
        
        require_once __DIR__ . '/../views/admin/resources.php';
    }
    
    public function manageCategories() {
        $categories = $this->categoryModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_category'])) {
                $this->categoryModel->create($_POST['cat_name']);
            } elseif (isset($_POST['delete_category'])) {
                $this->categoryModel->delete($_POST['cat_id']);
            }
            require_once __DIR__ . '/../config/config.php';
            header('Location: ' . url('app/views/admin/categories.php'));
            exit;
        }
        
        require_once __DIR__ . '/../views/admin/categories.php';
    }
    
    public function manageOrders() {
        $orders = $this->orderModel->getAllOrders();
        
        require_once __DIR__ . '/../views/admin/orders.php';
    }
    
    public function settings() {
        require_once __DIR__ . '/../views/admin/settings.php';
    }
    
    public function getStudents() {
        $all_users = $this->userModel->getAll();
        $students = array_filter($all_users, function($user) {
            return $user['user_role'] == 2 && ($user['user_type'] ?? 'student') == 'student';
        });
        return $students;
    }
    
    public function getCreators() {
        $all_users = $this->userModel->getAll();
        $creators = array_filter($all_users, function($user) {
            return $user['user_role'] == 2 && ($user['user_type'] ?? 'student') == 'creator';
        });
        return $creators;
    }
    
    public function getAllResources() {
        return $this->resourceModel->getAll();
    }
    
    public function deleteUser($user_id) {
        return $this->userModel->delete($user_id);
    }
    
    public function deleteResource($resource_id) {
        return $this->resourceModel->delete($resource_id);
    }
}
?>
