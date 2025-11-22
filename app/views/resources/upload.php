<?php
session_start();
require_once __DIR__ . '/../../controllers/resource_controller.php';

if (!isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../config/config.php';
    $_SESSION['error'] = 'Please log in to upload resources.';
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$controller = new resource_controller();
$controller->upload();

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
                <label for="creator">Creator</label>
                <select id="creator" name="creator" required>
                    <option value="">Select Creator</option>
                    <?php foreach ($creators as $creator): ?>
                        <option value="<?php echo $creator['creator_id']; ?>"><?php echo htmlspecialchars($creator['creator_name']); ?></option>
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
                <label for="image">Thumbnail Image</label>
                <input type="file" id="image" name="image" accept="image/*" required>
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
