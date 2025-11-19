<?php
session_start();
require_once __DIR__ . '/../models/resource_model.php';
require_once __DIR__ . '/../models/category_model.php';

class home_controller {
    private $resourceModel;
    private $categoryModel;
    
    public function __construct() {
        $this->resourceModel = new resource_model();
        $this->categoryModel = new category_model();
    }
    
    public function index() {
        $featured_resources = $this->resourceModel->getAll(6);
        $categories = $this->categoryModel->getAll();
        
        require_once __DIR__ . '/../views/home/index.php';
    }
}
?>
