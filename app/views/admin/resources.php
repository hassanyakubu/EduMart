<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$controller = new admin_controller();
$resources = $controller->getAllResources();

$page_title = 'Manage Resources';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin: 2rem 0;">
        <h1>📚 Manage Resources</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="<?php echo url('app/views/resources/upload.php'); ?>" class="btn btn-primary" style="text-decoration: none;">
                ➕ Upload New
            </a>
            <a href="<?php echo url('app/views/admin/dashboard.php'); ?>" class="btn btn-secondary" style="text-decoration: none; color: white;">
                ← Back
            </a>
        </div>
    </div>
    
    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <?php if (empty($resources)): ?>
            <p style="text-align: center; color: #666; padding: 2rem;">No resources found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Creator</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $resource): ?>
                        <tr>
                            <td><?php echo $resource['resource_id']; ?></td>
                            <td><?php echo htmlspecialchars($resource['resource_title']); ?></td>
                            <td><?php echo htmlspecialchars($resource['cat_name']); ?></td>
                            <td>₵<?php echo number_format($resource['resource_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($resource['creator_name']); ?></td>
                            <td>
                                <a href="<?php echo url('app/views/admin/delete_resource.php?id=' . $resource['resource_id']); ?>" 
                                   class="btn btn-danger" 
                                   style="text-decoration: none; color: white; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                   onclick="return confirm('Are you sure you want to delete this resource?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
