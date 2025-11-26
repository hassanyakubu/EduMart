<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/sales_model.php';
require_once __DIR__ . '/../../models/creator_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

// Check if user is creator or admin
$isCreator = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'creator';
$isAdmin = $_SESSION['user_role'] == 1;

if (!$isCreator && !$isAdmin) {
    $_SESSION['error'] = 'Access denied. Only creators can view earnings.';
    header('Location: ' . url('app/views/profile/dashboard.php'));
    exit;
}

$salesModel = new sales_model();
$creatorModel = new creator_model();

// Get creator ID for this user
$creators = $creatorModel->getAll();
$creator_id = null;

foreach ($creators as $creator) {
    if ($creator['created_by'] == $_SESSION['user_id']) {
        $creator_id = $creator['creator_id'];
        break;
    }
}

if (!$creator_id) {
    $_SESSION['error'] = 'Creator profile not found.';
    header('Location: ' . url('app/views/profile/dashboard.php'));
    exit;
}

$sales = $salesModel->getCreatorSales($creator_id);
$earnings = $salesModel->getCreatorEarnings($creator_id, $isAdmin);

$page_title = 'My Earnings';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container" style="margin: 3rem auto;">
    <h1 style="margin-bottom: 2rem;">My Earnings</h1>
    
    <?php if (!$isAdmin): ?>
    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 1.5rem; margin-bottom: 2rem; border-radius: 8px;">
        <strong style="color: #856404;">Commission Structure:</strong>
        <p style="margin: 0.5rem 0 0 0; color: #856404;">
            You receive <strong>80%</strong> of each sale. EduMart retains 20% as platform commission.
        </p>
    </div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #666; margin-bottom: 1rem;">Total Sales</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #667eea;"><?php echo $earnings['total_sales'] ?? 0; ?></p>
        </div>
        
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #666; margin-bottom: 1rem;">Gross Revenue</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #FFD947;">₵<?php echo number_format($earnings['gross_revenue'] ?? 0, 2); ?></p>
        </div>
        
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #666; margin-bottom: 1rem;">
                <?php echo $isAdmin ? 'Your Earnings (100%)' : 'Your Earnings (80%)'; ?>
            </h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #4CAF50;">₵<?php echo number_format($earnings['net_earnings'] ?? 0, 2); ?></p>
        </div>
    </div>
    
    <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 1.5rem;">Sales History</h2>
        
        <?php if (empty($sales)): ?>
            <p style="color: #666; text-align: center; padding: 2rem;">No sales yet. Keep creating great content!</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th>Resource</th>
                        <th>Buyer</th>
                        <th>Price</th>
                        <th>Your Earnings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): 
                        $creator_earning = $isAdmin ? $sale['resource_price'] : ($sale['resource_price'] * 0.8);
                    ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($sale['purchase_date'])); ?></td>
                            <td>#<?php echo $sale['invoice_no']; ?></td>
                            <td><?php echo htmlspecialchars($sale['resource_title']); ?></td>
                            <td><?php echo htmlspecialchars($sale['buyer_name']); ?></td>
                            <td>₵<?php echo number_format($sale['resource_price'], 2); ?></td>
                            <td style="color: #4CAF50; font-weight: 600;">
                                ₵<?php echo number_format($creator_earning, 2); ?>
                                <?php if (!$isAdmin): ?>
                                    <small style="color: #666;">(80%)</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
