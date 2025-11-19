<?php
session_start();
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$controller = new admin_controller();
$controller->dashboard();

$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">Admin Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #FFD947 0%, #FFC107 100%); padding: 2rem; border-radius: 12px; text-align: center; color: #333;">
            <h3>Total Users</h3>
            <p style="font-size: 3rem; font-weight: 700;"><?php echo $total_users; ?></p>
        </div>
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; text-align: center; color: white;">
            <h3>Total Resources</h3>
            <p style="font-size: 3rem; font-weight: 700;"><?php echo $total_resources; ?></p>
        </div>
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 2rem; border-radius: 12px; text-align: center; color: white;">
            <h3>Total Orders</h3>
            <p style="font-size: 3rem; font-weight: 700;"><?php echo $total_orders; ?></p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="/app/views/admin/users.php" style="background: white; padding: 2rem; border-radius: 12px; text-align: center; text-decoration: none; color: #333; font-weight: 600; transition: all 0.3s;">
            Manage Users
        </a>
        <a href="/app/views/admin/resources.php" style="background: white; padding: 2rem; border-radius: 12px; text-align: center; text-decoration: none; color: #333; font-weight: 600; transition: all 0.3s;">
            Manage Resources
        </a>
        <a href="/app/views/admin/categories.php" style="background: white; padding: 2rem; border-radius: 12px; text-align: center; text-decoration: none; color: #333; font-weight: 600; transition: all 0.3s;">
            Manage Categories
        </a>
        <a href="/app/views/admin/orders.php" style="background: white; padding: 2rem; border-radius: 12px; text-align: center; text-decoration: none; color: #333; font-weight: 600; transition: all 0.3s;">
            Manage Orders
        </a>
        <a href="/app/views/admin/settings.php" style="background: white; padding: 2rem; border-radius: 12px; text-align: center; text-decoration: none; color: #333; font-weight: 600; transition: all 0.3s;">
            Settings
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
