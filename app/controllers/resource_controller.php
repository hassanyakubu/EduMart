<?php
session_start();
require_once __DIR__ . '/../models/resource_model.php';
require_once __DIR__ . '/../models/category_model.php';
require_once __DIR__ . '/../models/creator_model.php';
require_once __DIR__ . '/../models/review_model.php';

class resource_controller {
    private $resourceModel;
    private $categoryModel;
    private $creatorModel;
    private $reviewModel;
    
    public function __construct() {
        $this->resourceModel = new resource_model();
        $this->categoryModel = new category_model();
        $this->creatorModel = new creator_model();
        $this->reviewModel = new review_model();
    }
    
    public function index() {
        $keyword = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? null;
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        
        if ($keyword || $category || $minPrice || $maxPrice) {
            $resources = $this->resourceModel->search($keyword, $category, $minPrice, $maxPrice);
        } else {
            $resources = $this->resourceModel->getAll();
        }
        
        $categories = $this->categoryModel->getAll();
        
        require_once __DIR__ . '/../views/resources/list.php';
    }
    
    public function details($id) {
        $resource = $this->resourceModel->getById($id);
        $reviews = $this->reviewModel->getByResource($id);
        
        if (!$resource) {
            header('Location: /app/views/resources/list.php');
            exit;
        }
        
        require_once __DIR__ . '/../views/resources/details.php';
    }
    
    public function upload() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $categories = $this->categoryModel->getAll();
        $creators = $this->creatorModel->getAll();
        
        require_once __DIR__ . '/../views/resources/upload.php';
    }
    
    public function create() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $cat_id = $_POST['category'] ?? 0;
        $creator_id = $_POST['creator'] ?? 0;
        $title = $_POST['title'] ?? '';
        $price = $_POST['price'] ?? 0;
        $desc = $_POST['description'] ?? '';
        $keywords = $_POST['keywords'] ?? '';
        
        // Handle file uploads
        $image = $this->uploadFile($_FILES['image'], 'images');
        $file = $this->uploadFile($_FILES['file'], 'files');
        
        if ($this->resourceModel->create($cat_id, $creator_id, $title, $price, $desc, $image, $keywords, $file)) {
            $_SESSION['success'] = 'Resource uploaded successfully!';
            header('Location: /app/views/resources/list.php');
        } else {
            $_SESSION['error'] = 'Failed to upload resource.';
            header('Location: /app/views/resources/upload.php');
        }
        exit;
    }
    
    private function uploadFile($file, $folder) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $upload_dir = __DIR__ . '/../../public/uploads/' . $folder . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return 'uploads/' . $folder . '/' . $filename;
        }
        
        return null;
    }
    
    public function search() {
        $this->index();
    }
}
?>
