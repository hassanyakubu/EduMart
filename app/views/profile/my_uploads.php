<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

// Check if user is a creator
if ($_SESSION['user_type'] != 'creator') {
    $_SESSION['error'] = 'Access denied. Only creators can view this page.';
    header('Location: ' . url('app/views/home/index.php'));
    exit;
}

$db = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];

// Get creator's resources
$stmt = $db->prepare("
    SELECT 
        r.*,
        c.cat_name,
        cr.creator_name,
        COUNT(DISTINCT oi.order_item_id) as sales_count,
        SUM(oi.price) as total_revenue
    FROM resources r
    JOIN categories c ON r.cat_id = c.cat_id
    JOIN creators cr ON r.creator_id = cr.creator_id
    LEFT JOIN order_items oi ON r.resource_id = oi.resource_id
    WHERE cr.created_by = ?
    GROUP BY r.resource_id
    ORDER BY r.resource_id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$my_resources = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = 'My Uploads';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; margin: 2rem 0; color: white;">
        <h1 style="margin: 0; font-size: 2.5rem;">📤 My Uploads</h1>
        <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem;">Manage your uploaded resources</p>
    </div>
    
    <div style="margin: 2rem 0; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0;">Your Resources</h2>
        <a href="<?php echo url('app/views/resources/upload.php'); ?>" 
           class="btn btn-primary" 
           style="text-decoration: none; padding: 0.75rem 1.5rem; background: #667eea; color: white; border-radius: 8px;">
            Upload New Resource
        </a>
    </div>
    
    <?php if (empty($my_resources)): ?>
        <div style="background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 12px; padding: 3rem; text-align: center; margin: 2rem 0;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
            <h3 style="color: #666; margin-bottom: 1rem;">No Uploads Yet</h3>
            <p style="color: #999; margin-bottom: 2rem;">You haven't uploaded any resources yet. Start sharing your knowledge!</p>
            <a href="<?php echo url('app/views/resources/upload.php'); ?>" 
               class="btn btn-primary" 
               style="text-decoration: none; padding: 1rem 2rem; background: #667eea; color: white; border-radius: 8px; display: inline-block;">
                Upload Your First Resource
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 1.5rem;">
            <?php foreach ($my_resources as $resource): ?>
                <div style="background: white; border: 1px solid #dee2e6; border-radius: 12px; padding: 1.5rem; display: flex; gap: 1.5rem; align-items: start;">
                    <?php if ($resource['resource_image']): ?>
                        <img src="<?php echo url($resource['resource_image']); ?>" 
                             alt="<?php echo htmlspecialchars($resource['resource_title']); ?>"
                             style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                    <?php else: ?>
                        <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; flex-shrink: 0;">
                            📄
                        </div>
                    <?php endif; ?>
                    
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 0.5rem 0; color: #333;">
                            <?php echo htmlspecialchars($resource['resource_title']); ?>
                        </h3>
                        
                        <p style="color: #666; margin: 0.5rem 0; font-size: 0.95rem;">
                            <?php echo htmlspecialchars($resource['resource_desc'] ?? 'No description'); ?>
                        </p>
                        
                        <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                            <span style="background: #e3f2fd; color: #1976d2; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.9rem;">
                                📁 <?php echo htmlspecialchars($resource['cat_name']); ?>
                            </span>
                            <span style="background: #f3e5f5; color: #7b1fa2; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.9rem;">
                                💰 GHS <?php echo number_format($resource['resource_price'], 2); ?>
                            </span>
                            <span style="background: #e8f5e9; color: #388e3c; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.9rem;">
                                📊 <?php echo $resource['sales_count']; ?> sales
                            </span>
                            <?php if ($resource['total_revenue']): ?>
                                <span style="background: #fff3e0; color: #f57c00; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.9rem;">
                                    💵 GHS <?php echo number_format($resource['total_revenue'] * 0.8, 2); ?> earned
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($resource['resource_keywords']): ?>
                            <p style="color: #999; font-size: 0.85rem; margin-top: 0.75rem;">
                                🏷️ <?php echo htmlspecialchars($resource['resource_keywords']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="<?php echo url('app/views/resources/details.php?id=' . $resource['resource_id']); ?>" 
                           style="padding: 0.5rem 1rem; background: #667eea; color: white; border-radius: 6px; text-decoration: none; text-align: center; font-size: 0.9rem;">
                            View
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="background: #e3f2fd; border-left: 4px solid #1976d2; padding: 1.5rem; margin: 2rem 0; border-radius: 8px;">
            <strong style="color: #1565c0;">💡 Tip:</strong>
            <p style="margin: 0.5rem 0 0 0; color: #1565c0;">
                You earn <strong>80% commission</strong> on each sale. Keep uploading quality resources to increase your earnings!
            </p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
