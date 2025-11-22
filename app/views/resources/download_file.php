<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/download_model.php';
require_once __DIR__ . '/../../models/resource_model.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: ' . url('app/views/home/index.php'));
    exit;
}

$resource_id = intval($_GET['id']);
$downloadModel = new download_model();

// Check if user has access to this resource
if (!$downloadModel->hasAccess($_SESSION['user_id'], $resource_id)) {
    $_SESSION['error'] = 'You must purchase this resource before downloading.';
    header('Location: ' . url('app/views/resources/details.php?id=' . $resource_id));
    exit;
}

// Get resource details
$resourceModel = new resource_model();
$resource = $resourceModel->getById($resource_id);

if (!$resource || !$resource['resource_file']) {
    $_SESSION['error'] = 'Resource file not found.';
    header('Location: ' . url('app/views/profile/my_resources.php'));
    exit;
}

// Build file path
$file_path = __DIR__ . '/../../../public/' . $resource['resource_file'];

if (!file_exists($file_path)) {
    $_SESSION['error'] = 'File not found on server.';
    header('Location: ' . url('app/views/profile/my_resources.php'));
    exit;
}

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
?>
