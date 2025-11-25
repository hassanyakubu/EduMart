<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/user_model.php';
require_once __DIR__ . '/../../models/resource_model.php';
require_once __DIR__ . '/../../models/order_model.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

// Get statistics
$userModel = new user_model();
$resourceModel = new resource_model();
$orderModel = new order_model();

$total_users = count($userModel->getAll());
$total_resources = count($resourceModel->getAll());
$total_orders = count($orderModel->getAllOrders());

$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; margin: 2rem 0; color: white;">
        <h1 style="margin: 0; font-size: 2.5rem;">⚙️ Admin Dashboard</h1>
        <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem;">Platform Overview & Management</p>
    </div>
    
    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        <div style="background: linear-gradient(135deg, #FFD947 0%, #FFC107 100%); padding: 2rem; border-radius: 12px; text-align: center; color: #333; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem;">👥 Total Users</h3>
            <p style="font-size: 3rem; font-weight: 700; margin: 0;"><?php echo $total_users; ?></p>
        </div>
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem;">📚 Total Resources</h3>
            <p style="font-size: 3rem; font-weight: 700; margin: 0;"><?php echo $total_resources; ?></p>
        </div>
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 2rem; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem;">🛒 Total Orders</h3>
            <p style="font-size: 3rem; font-weight: 700; margin: 0;"><?php echo $total_orders; ?></p>
        </div>
    </div>
    
    <!-- Management Sections -->
    <div style="display: grid; gap: 2rem;">
        
        <!-- Upload Resources -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">📤 Upload Resources</h2>
                <a href="<?php echo url('app/views/resources/upload.php'); ?>" class="btn btn-primary" style="text-decoration: none;">
                    ➕ Upload New Resource
                </a>
            </div>
            <p style="color: #666; margin: 0;">Upload educational content, past questions, notes, and other learning materials.</p>
        </div>
        
        <!-- Manage Students -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">🎓 Manage Students</h2>
                <a href="<?php echo url('app/views/admin/students.php'); ?>" class="btn btn-secondary" style="text-decoration: none; color: white;">
                    All Students
                </a>
            </div>
            <p style="color: #666; margin: 0;">View, edit, and delete student accounts. Monitor student purchases and activity.</p>
        </div>
        
        <!-- Manage Creators -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">✍️ Manage Creators</h2>
                <a href="<?php echo url('app/views/admin/creators.php'); ?>" class="btn btn-secondary" style="text-decoration: none; color: white;">
                    All Creators
                </a>
            </div>
            <p style="color: #666; margin: 0;">View, edit, and delete creator accounts. Monitor uploaded content and sales.</p>
        </div>
        
        <!-- Manage Content -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">📚 Manage Content</h2>
                <a href="<?php echo url('app/views/admin/resources.php'); ?>" class="btn btn-secondary" style="text-decoration: none; color: white;">
                    All Resources
                </a>
            </div>
            <p style="color: #666; margin: 0;">View, edit, and delete all resources on the platform. Moderate content quality.</p>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
