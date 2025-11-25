<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/sales_model.php';
require_once __DIR__ . '/../../models/user_model.php';
require_once __DIR__ . '/../../models/resource_model.php';
require_once __DIR__ . '/../../models/order_model.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$salesModel = new sales_model();
$userModel = new user_model();
$resourceModel = new resource_model();
$orderModel = new order_model();

$platform_revenue = $salesModel->getPlatformRevenue();
$top_resources = $salesModel->getTopSellingResources(10);
$total_users = count($userModel->getAll());
$total_resources = count($resourceModel->getAll());
$total_orders = count($orderModel->getAllOrders());

$page_title = 'Platform Analytics';
require_once __DIR__ . '/../layouts/admin_header.php';
?>

<div class="container" style="margin: 3rem auto;">
    <h1 style="margin-bottom: 2rem; color: #667eea;">📊 Platform Analytics</h1>
    
    <div style="background: #d4edda; border-left: 4px solid #28a745; padding: 1.5rem; margin-bottom: 2rem; border-radius: 8px;">
        <strong style="color: #155724;">💰 Revenue Model:</strong>
        <p style="margin: 0.5rem 0 0 0; color: #155724;">
            EduMart earns <strong>20% commission</strong> on all sales (except your own resources where you keep 100%).
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Total Users</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #667eea;"><?php echo $total_users; ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Total Resources</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #FFD947;"><?php echo $total_resources; ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Total Orders</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #f093fb;"><?php echo $total_orders; ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Platform Sales</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #4CAF50;"><?php echo $platform_revenue['total_sales'] ?? 0; ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center;">
            <h3 style="color: #667eea; margin-bottom: 1rem;">Gross Revenue</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #FFD947;">₵<?php echo number_format($platform_revenue['gross_revenue'] ?? 0, 2); ?></p>
        </div>
        
        <div class="admin-card" style="text-align: center; border: 3px solid #28a745;">
            <h3 style="color: #28a745; margin-bottom: 1rem; font-weight: 700;">Platform Revenue (20%)</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #28a745;">₵<?php echo number_format($platform_revenue['platform_commission'] ?? 0, 2); ?></p>
        </div>
    </div>
    
    <div class="admin-card">
        <h2 style="margin-bottom: 1.5rem; color: #667eea;">🏆 Top Selling Resources</h2>
        
        <?php if (empty($top_resources)): ?>
            <p style="color: #666; text-align: center; padding: 2rem;">No sales data available yet.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <th style="padding: 1rem; text-align: center; border-radius: 8px 0 0 8px;">Rank</th>
                            <th style="padding: 1rem; text-align: left;">Resource</th>
                            <th style="padding: 1rem; text-align: left;">Creator</th>
                            <th style="padding: 1rem; text-align: center;">Price</th>
                            <th style="padding: 1rem; text-align: center;">Sales Count</th>
                            <th style="padding: 1rem; text-align: center;">Total Revenue</th>
                            <th style="padding: 1rem; text-align: center; border-radius: 0 8px 8px 0;">Platform Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_resources as $index => $resource): 
                            $commission = $resource['total_revenue'] * 0.2;
                        ?>
                            <tr style="border-bottom: 1px solid #eee; transition: background 0.2s;" 
                                onmouseover="this.style.background='rgba(102, 126, 234, 0.05)'" 
                                onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #FFD947; color: #333; padding: 0.3rem 0.8rem; border-radius: 20px; font-weight: 600;">
                                        #<?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; font-weight: 600;"><?php echo htmlspecialchars($resource['resource_title']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($resource['creator_name']); ?></td>
                                <td style="padding: 1rem; text-align: center;">₵<?php echo number_format($resource['resource_price'], 2); ?></td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #667eea; color: white; padding: 0.3rem 0.8rem; border-radius: 20px; font-weight: 600;">
                                        <?php echo $resource['sales_count']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center; color: #FFD947; font-weight: 600;">₵<?php echo number_format($resource['total_revenue'], 2); ?></td>
                                <td style="padding: 1rem; text-align: center; color: #28a745; font-weight: 600;">₵<?php echo number_format($commission, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="margin-top: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div class="admin-card">
            <h3 style="margin-bottom: 1rem; color: #28a745;">💵 Revenue Breakdown</h3>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.05); border-radius: 8px;">
                    <span style="font-weight: 600;">Creators (80%):</span>
                    <span style="font-weight: 600; color: #667eea;">₵<?php echo number_format(($platform_revenue['gross_revenue'] ?? 0) * 0.8, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: rgba(40, 167, 69, 0.1); border-radius: 8px;">
                    <span style="font-weight: 600; color: #28a745;">Platform (20%):</span>
                    <span style="font-weight: 700; color: #28a745;">₵<?php echo number_format($platform_revenue['platform_commission'] ?? 0, 2); ?></span>
                </div>
            </div>
            <div style="margin-top: 1.5rem; padding: 1rem; background: #fff3cd; border-radius: 8px;">
                <p style="margin: 0; color: #856404; font-size: 0.9rem;">
                    <strong>Note:</strong> Main admin (you) receives 100% of your own resource sales.
                </p>
            </div>
        </div>
        
        <div class="admin-card">
            <h3 style="margin-bottom: 1rem; color: #667eea;">🎯 Quick Actions</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="<?php echo url('app/views/admin/orders.php'); ?>" class="admin-btn" style="text-decoration: none; text-align: center;">View All Orders</a>
                <a href="<?php echo url('app/views/admin/resources.php'); ?>" class="admin-btn" style="text-decoration: none; text-align: center;">Manage Resources</a>
                <a href="<?php echo url('app/views/admin/users.php'); ?>" class="admin-btn" style="text-decoration: none; text-align: center;">Manage Users</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
