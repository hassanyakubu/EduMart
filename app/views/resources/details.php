<?php
session_start();
require_once __DIR__ . '/../../controllers/resource_controller.php';

if (!isset($_GET['id'])) {
    header('Location: /app/views/resources/list.php');
    exit;
}

$controller = new resource_controller();
$controller->details($_GET['id']);

$page_title = $resource['resource_title'] ?? 'Resource Details';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div style="background: white; border-radius: 12px; padding: 2rem; margin: 2rem 0;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            <div>
                <img src="<?php echo asset($resource['resource_image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($resource['resource_title']); ?>" 
                     style="width: 100%; border-radius: 12px;">
            </div>
            <div>
                <h1><?php echo htmlspecialchars($resource['resource_title']); ?></h1>
                <p style="color: #666; margin: 0.5rem 0;">Category: <?php echo htmlspecialchars($resource['cat_name']); ?></p>
                <p style="color: #666; margin: 0.5rem 0;">Creator: <?php echo htmlspecialchars($resource['creator_name']); ?></p>
                
                <div style="display: flex; align-items: center; gap: 1rem; margin: 1rem 0;">
                    <div class="rating">
                        ⭐ <?php echo number_format($resource['avg_rating'], 1); ?> 
                        (<?php echo $resource['review_count']; ?> reviews)
                    </div>
                </div>
                
                <div class="card-price" style="margin: 1rem 0;">₵<?php echo number_format($resource['resource_price'], 2); ?></div>
                
                <p style="margin: 1rem 0; line-height: 1.8;"><?php echo nl2br(htmlspecialchars($resource['resource_desc'])); ?></p>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="<?php echo url('app/views/cart/add.php?id=' . $resource['resource_id']); ?>" 
                       class="btn btn-primary" style="text-decoration: none;">
                        Add to Cart
                    </a>
                    <a href="<?php echo url('app/views/resources/list.php'); ?>" 
                       class="btn btn-secondary" style="text-decoration: none; color: white;">
                        Back to Browse
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div style="background: white; border-radius: 12px; padding: 2rem; margin: 2rem 0;">
        <h2 style="margin-bottom: 1rem;">Reviews</h2>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="<?php echo url('app/views/resources/add_review.php'); ?>" method="POST" style="margin-bottom: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                <input type="hidden" name="resource_id" value="<?php echo $resource['resource_id']; ?>">
                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" required style="padding: 0.5rem; border-radius: 8px; border: 1px solid #ddd;">
                        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4">⭐⭐⭐⭐ (4)</option>
                        <option value="3">⭐⭐⭐ (3)</option>
                        <option value="2">⭐⭐ (2)</option>
                        <option value="1">⭐ (1)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea name="comment" rows="3" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        <?php endif; ?>
        
        <?php if (empty($reviews)): ?>
            <p style="color: #666;">No reviews yet. Be the first to review!</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div style="border-bottom: 1px solid #eee; padding: 1rem 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <strong><?php echo htmlspecialchars($review['customer_name']); ?></strong>
                        <span style="color: #FFD947;">
                            <?php echo str_repeat('⭐', $review['rating']); ?>
                        </span>
                    </div>
                    <p style="color: #666; margin-top: 0.5rem;"><?php echo htmlspecialchars($review['comment']); ?></p>
                    <small style="color: #999;"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
