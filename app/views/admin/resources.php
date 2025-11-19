<?php
session_start();
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$controller = new admin_controller();
$controller->manageResources();

$page_title = 'Manage Resources';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">Manage Resources</h1>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Creator</th>
                    <th>Price</th>
                    <th>Rating</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resources as $resource): ?>
                    <tr>
                        <td><?php echo $resource['resource_id']; ?></td>
                        <td><?php echo htmlspecialchars($resource['resource_title']); ?></td>
                        <td><?php echo htmlspecialchars($resource['cat_name']); ?></td>
                        <td><?php echo htmlspecialchars($resource['creator_name']); ?></td>
                        <td>₵<?php echo number_format($resource['resource_price'], 2); ?></td>
                        <td>⭐ <?php echo number_format($resource['avg_rating'], 1); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="resource_id" value="<?php echo $resource['resource_id']; ?>">
                                <button type="submit" name="delete_resource" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this resource?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
