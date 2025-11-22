<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/resource_model.php';
require_once __DIR__ . '/../../models/creator_model.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

// Check if user is creator or admin
if ($_SESSION['user_role'] != 1 && (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'creator')) {
    $_SESSION['error'] = 'Only creators and admins can upload resources.';
    header('Location: ' . url('app/views/home/index.php'));
    exit;
}

$cat_id = $_POST['category'] ?? 0;
$title = $_POST['title'] ?? '';
$price = $_POST['price'] ?? 0;
$desc = $_POST['description'] ?? '';
$keywords = $_POST['keywords'] ?? '';

// Get or create creator for this user
$creatorModel = new creator_model();
$user_name = $_SESSION['user_name'];

// Check if creator exists for this user
$creators = $creatorModel->getAll();
$creator_id = null;

foreach ($creators as $creator) {
    if ($creator['created_by'] == $_SESSION['user_id']) {
        $creator_id = $creator['creator_id'];
        break;
    }
}

// If no creator exists, create one
if (!$creator_id) {
    if ($creatorModel->create($user_name, $_SESSION['user_id'])) {
        // Get the newly created creator ID
        $creators = $creatorModel->getAll();
        foreach ($creators as $creator) {
            if ($creator['created_by'] == $_SESSION['user_id']) {
                $creator_id = $creator['creator_id'];
                break;
            }
        }
    }
}

if (!$creator_id) {
    $_SESSION['error'] = 'Failed to create creator profile.';
    header('Location: ' . url('app/views/resources/upload.php'));
    exit;
}

// Handle file uploads
function uploadFile($file, $folder) {
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

$image = uploadFile($_FILES['image'] ?? null, 'images');
$file = uploadFile($_FILES['file'] ?? null, 'files');

if (!$file) {
    $_SESSION['error'] = 'Resource file is required.';
    header('Location: ' . url('app/views/resources/upload.php'));
    exit;
}

$resourceModel = new resource_model();

if ($resourceModel->create($cat_id, $creator_id, $title, $price, $desc, $image, $keywords, $file)) {
    $_SESSION['success'] = 'Resource uploaded successfully!';
    header('Location: ' . url('app/views/resources/list.php'));
} else {
    $_SESSION['error'] = 'Failed to upload resource.';
    header('Location: ' . url('app/views/resources/upload.php'));
}
exit;
?>
