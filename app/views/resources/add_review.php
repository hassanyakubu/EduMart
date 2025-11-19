<?php
session_start();
require_once __DIR__ . '/../../models/Review.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /app/views/auth/login.php');
    exit;
}

$reviewModel = new Review();
$resource_id = $_POST['resource_id'] ?? 0;
$rating = $_POST['rating'] ?? 0;
$comment = $_POST['comment'] ?? '';

if ($reviewModel->addReview($_SESSION['user_id'], $resource_id, $rating, $comment)) {
    $_SESSION['success'] = 'Review added successfully!';
} else {
    $_SESSION['error'] = 'You have already reviewed this resource.';
}

header('Location: /app/views/resources/details.php?id=' . $resource_id);
exit;
?>
