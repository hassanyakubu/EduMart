<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/resource_model.php';
require_once __DIR__ . '/../../models/category_model.php';
require_once __DIR__ . '/../../models/creator_model.php';

$resourceModel = new resource_model();
$categoryModel = new category_model();
$creatorModel = new creator_model();

$keyword = $_GET['search'] ?? '';
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$creator = isset($_GET['creator']) && $_GET['creator'] !== '' ? $_GET['creator'] : null;
$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? $_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? $_GET['max_price'] : null;

// Check if any filter is applied
$hasFilters = !empty($keyword) || $category !== null || $creator !== null || $minPrice !== null || $maxPrice !== null;

// Debug: Log search parameters
error_log("Search - Keyword: '$keyword', Category: '$category', Creator: '$creator', MinPrice: '$minPrice', MaxPrice: '$maxPrice', HasFilters: " . ($hasFilters ? 'yes' : 'no'));

if ($hasFilters) {
    $resources = $resourceModel->search($keyword, $category, $minPrice, $maxPrice, $creator);
    error_log("Search returned " . count($resources) . " resources");
} else {
    $resources = $resourceModel->getAll();
    error_log("GetAll returned " . count($resources) . " resources");
}

$categories = $categoryModel->getAll();
$creators = $creatorModel->getAll();

$page_title = 'Browse Resources';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0 1rem;">Browse Resources</h1>
    
    <div style="background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form action="" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <input type="text" name="search" placeholder="Search resources..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px;">
            
            <select name="category" style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px;">
                <option value="">📚 All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['cat_id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['cat_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['cat_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="creator" style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px;">
                <option value="">All Creators</option>
                <?php foreach ($creators as $cr): ?>
                    <option value="<?php echo $cr['creator_id']; ?>" <?php echo (isset($_GET['creator']) && $_GET['creator'] == $cr['creator_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cr['creator_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="number" name="min_price" placeholder="₵ Min Price" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>" style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px;">
            
            <input type="number" name="max_price" placeholder="₵ Max Price" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>" style="padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px;">
            
            <button type="submit" class="btn btn-primary">🔎 Filter</button>
        </form>
    </div>
    
    <div class="card-grid">
        <?php if (empty($resources)): ?>
            <p style="grid-column: 1/-1; text-align: center; padding: 3rem;">No resources found.</p>
        <?php else: ?>
            <?php foreach ($resources as $resource): ?>
                <div class="card">
                    <img src="<?php echo asset($resource['resource_image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($resource['resource_title']); ?>" 
                         class="card-image">
                    <div class="card-content">
                        <h3 class="card-title"><?php echo htmlspecialchars($resource['resource_title']); ?></h3>
                        <p style="color: #666; font-size: 0.9rem; margin: 0.5rem 0;">
                            📚 <?php echo htmlspecialchars($resource['cat_name']); ?>
                        </p>
                        <p style="color: #888; font-size: 0.85rem; margin: 0.25rem 0;">
                            By <?php echo htmlspecialchars($resource['creator_name']); ?>
                        </p>
                        <div class="card-price">₵<?php echo number_format($resource['resource_price'], 2); ?></div>
                        <div class="card-meta">
                            <div class="rating">
                                ⭐ <?php echo number_format($resource['avg_rating'], 1); ?> 
                                (<?php echo $resource['review_count']; ?>)
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <a href="<?php echo url('app/views/resources/details.php?id=' . $resource['resource_id']); ?>" 
                               class="btn btn-secondary" style="flex: 1; text-align: center; text-decoration: none; color: white;">
                                View
                            </a>
                            <a href="<?php echo url('app/views/cart/add.php?id=' . $resource['resource_id']); ?>" 
                               class="btn btn-primary" style="flex: 1; text-align: center; text-decoration: none;">
                                Add to Cart
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
