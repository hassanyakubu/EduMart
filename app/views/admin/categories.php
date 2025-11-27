<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/category_model.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$categoryModel = new category_model();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $categoryModel->create($_POST['cat_name']);
        $_SESSION['success'] = 'Category added successfully!';
    } elseif (isset($_POST['delete_category'])) {
        $categoryModel->delete($_POST['cat_id']);
        $_SESSION['success'] = 'Category deleted successfully!';
    }
    header('Location: ' . url('app/views/admin/categories.php'));
    exit;
}

$categories = $categoryModel->getAll();

$page_title = 'Manage Categories';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">Manage Categories</h1>
    
    <div style="background: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1rem;">Add New Category</h2>
        <form method="POST" style="display: flex; gap: 1rem;">
            <input type="text" name="cat_name" placeholder="Category Name" required 
                   style="flex: 1; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px;">
            <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
        </form>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['cat_id']; ?></td>
                        <td><?php echo htmlspecialchars($category['cat_name']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="cat_id" value="<?php echo $category['cat_id']; ?>">
                                <button type="submit" name="delete_category" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure? This will delete all resources in this category.')">
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
