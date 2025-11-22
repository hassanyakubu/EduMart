<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/download_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$downloadModel = new download_model();
$my_resources = $downloadModel->getUserDownloads($_SESSION['user_id']);

$page_title = 'My Resources';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; margin: 2rem 0; color: white;">
        <h1 style="margin: 0; font-size: 2.5rem;">📚 My Resources</h1>
        <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem;">Access your purchased learning materials</p>
    </div>
    
    <?php if (empty($my_resources)): ?>
        <div style="background: white; padding: 3rem; border-radius: 12px; text-align: center;">
            <p style="font-size: 1.2rem; color: #666; margin-bottom: 1.5rem;">You haven't purchased any resources yet.</p>
            <a href="<?php echo url('app/views/resources/list.php'); ?>" class="btn btn-primary" style="text-decoration: none;">
                Browse Resources
            </a>
        </div>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($my_resources as $resource): ?>
                <div class="card">
                    <img src="<?php echo asset($resource['resource_image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($resource['resource_title']); ?>" 
                         class="card-image">
                    <div class="card-content">
                        <h3 class="card-title"><?php echo htmlspecialchars($resource['resource_title']); ?></h3>
                        <p style="color: #666; font-size: 0.9rem; margin: 0.5rem 0;">
                            📅 Purchased: <?php echo date('M d, Y', strtotime($resource['download_date'])); ?>
                        </p>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <a href="<?php echo url('app/views/resources/view_file.php?id=' . $resource['resource_id']); ?>" 
                               class="btn btn-secondary" 
                               style="flex: 1; text-align: center; text-decoration: none; color: white;"
                               target="_blank">
                                👁️ View
                            </a>
                            <a href="<?php echo url('app/views/resources/download_file.php?id=' . $resource['resource_id']); ?>" 
                               class="btn btn-primary" 
                               style="flex: 1; text-align: center; text-decoration: none;">
                                ⬇️ Download
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
