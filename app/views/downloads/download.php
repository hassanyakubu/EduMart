<?php
session_start();
require_once __DIR__ . '/../../models/Download.php';
require_once __DIR__ . '/../../models/Resource.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !isset($_GET['order'])) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$downloadModel = new Download();
$resourceModel = new Resource();

$resource_id = $_GET['id'];
$order_id = $_GET['order'];

// Verify access
if (!$downloadModel->hasAccess($_SESSION['user_id'], $resource_id)) {
    $_SESSION['error'] = 'You do not have access to this resource.';
    header('Location: /app/views/profile/dashboard.php');
    exit;
}

$resource = $resourceModel->getById($resource_id);

if (!$resource || !$resource['resource_file']) {
    $_SESSION['error'] = 'Resource file not found.';
    header('Location: /app/views/profile/dashboard.php');
    exit;
}

// Log the download
$downloadModel->logDownload($_SESSION['user_id'], $resource_id, $order_id);

// Serve the file
$file_path = __DIR__ . '/../../../public/' . $resource['resource_file'];

if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    $_SESSION['error'] = 'File not found on server.';
    header('Location: /app/views/profile/dashboard.php');
    exit;
}
?>
