<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/category_model.php';
require_once __DIR__ . '/../../models/creator_model.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to upload resources.';
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

// Check if user is creator or admin
if ($_SESSION['user_role'] != 1 && (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'creator')) {
    $_SESSION['error'] = 'Only creators and admins can upload resources.';
    header('Location: ' . url('app/views/home/index.php'));
    exit;
}

$categoryModel = new category_model();
$creatorModel = new creator_model();

$categories = $categoryModel->getAll();
$creators = $creatorModel->getAll();

$page_title = 'Upload Resource';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="form-container" style="max-width: 700px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Upload Resource</h2>
        <form action="<?php echo url('app/views/resources/upload_process.php'); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Resource Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['cat_id']; ?>"><?php echo htmlspecialchars($cat['cat_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price">Price (₵)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="keywords">Keywords (comma separated)</label>
                <input type="text" id="keywords" name="keywords" placeholder="e.g. math, algebra, shs">
            </div>
            
            <div class="form-group">
                <label for="image">Thumbnail Image (Optional)</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Upload a cover image for your resource (JPG, PNG, GIF)
                </small>
            </div>
            
            <div class="form-group">
                <label for="file">Resource File (PDF, DOCX, MP4, ZIP)</label>
                <input type="file" id="file" name="file" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Upload Resource</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
