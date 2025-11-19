<?php
session_start();
require_once __DIR__ . '/../../controllers/home_controller.php';

$controller = new home_controller();
$controller->index();

$page_title = 'Home';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="hero">
        <h1>Welcome to EduMart</h1>
        <p>Your trusted marketplace for digital learning resources</p>
        <div class="search-bar">
            <form action="/app/views/resources/list.php" method="GET" style="display: flex; gap: 1rem; width: 100%;">
                <input type="text" name="search" placeholder="Search for resources..." />
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
    
    <section>
        <h2 style="margin: 2rem 0 1rem;" class="gradient-text-animated">Featured Resources</h2>
        <div class="card-grid">
            <?php foreach ($featured_resources as $resource): ?>
                <div class="card stagger-item hover-lift">
                    <img src="/public/<?php echo $resource['resource_image'] ?? 'assets/images/placeholder.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($resource['resource_title']); ?>" 
                         class="card-image">
                    <div class="card-content">
                        <h3 class="card-title"><?php echo htmlspecialchars($resource['resource_title']); ?></h3>
                        <p style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($resource['cat_name']); ?></p>
                        <div class="card-price">₵<?php echo number_format($resource['resource_price'], 2); ?></div>
                        <div class="card-meta">
                            <div class="rating">
                                ⭐ <?php echo number_format($resource['avg_rating'], 1); ?> 
                                (<?php echo $resource['review_count']; ?>)
                            </div>
                        </div>
                        <a href="/app/views/resources/details.php?id=<?php echo $resource['resource_id']; ?>" 
                           class="btn btn-primary btn-block" style="margin-top: 1rem; display: block; text-align: center; text-decoration: none;">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <section style="margin: 3rem 0;">
        <h2 style="margin-bottom: 1rem;" class="gradient-text-animated">Browse by Category</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <?php foreach ($categories as $category): ?>
                <a href="/app/views/resources/list.php?category=<?php echo $category['cat_id']; ?>" 
                   class="category-card hover-lift stagger-item"
                   style="background: white; padding: 2rem; border-radius: 12px; text-align: center; text-decoration: none; color: #333; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                    <?php echo htmlspecialchars($category['cat_name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
