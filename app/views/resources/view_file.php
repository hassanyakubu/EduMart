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
    $_SESSION['error'] = 'You must purchase this resource before viewing.';
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

// Build file URL
$file_url = asset($resource['resource_file']);
$file_extension = strtolower(pathinfo($resource['resource_file'], PATHINFO_EXTENSION));

$page_title = $resource['resource_title'];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div style="margin: 2rem 0;">
        <a href="<?php echo url('app/views/profile/my_resources.php'); ?>" class="btn btn-secondary" style="text-decoration: none; color: white;">
            ← Back to My Resources
        </a>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 12px;">
        <h1><?php echo htmlspecialchars($resource['resource_title']); ?></h1>
        
        <div style="margin: 2rem 0;">
            <?php if (in_array($file_extension, ['pdf'])): ?>
                <iframe src="<?php echo $file_url; ?>" 
                        style="width: 100%; height: 800px; border: 1px solid #ddd; border-radius: 8px;">
                </iframe>
            <?php elseif (in_array($file_extension, ['mp4', 'webm', 'ogg'])): ?>
                <video controls style="width: 100%; max-height: 600px; border-radius: 8px;">
                    <source src="<?php echo $file_url; ?>" type="video/<?php echo $file_extension; ?>">
                    Your browser does not support the video tag.
                </video>
            <?php elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                <img src="<?php echo $file_url; ?>" 
                     alt="<?php echo htmlspecialchars($resource['resource_title']); ?>"
                     style="max-width: 100%; border-radius: 8px;">
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 8px;">
                    <p style="font-size: 1.2rem; margin-bottom: 1rem;">
                        This file type cannot be previewed in the browser.
                    </p>
                    <a href="<?php echo url('app/views/resources/download_file.php?id=' . $resource_id); ?>" 
                       class="btn btn-primary" style="text-decoration: none;">
                        ⬇️ Download File
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 2rem;">
            <a href="<?php echo url('app/views/resources/download_file.php?id=' . $resource_id); ?>" 
               class="btn btn-primary" style="text-decoration: none;">
                ⬇️ Download File
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
