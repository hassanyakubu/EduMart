<?php
session_start();
require_once __DIR__ . '/../../controllers/profile_controller.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$controller = new profile_controller();
$controller->dashboard();

$page_title = 'My Dashboard';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">My Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center;">
            <h3 style="color: #666;">Total Orders</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #FFD947;"><?php echo count($orders); ?></p>
        </div>
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center;">
            <h3 style="color: #666;">Downloads</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #FFD947;"><?php echo count($downloads); ?></p>
        </div>
    </div>
    
    <div style="background: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
        <h2 style="margin-bottom: 1rem;">Profile Information</h2>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
            <div>
                <strong>Name:</strong> <?php echo htmlspecialchars($user['customer_name']); ?>
            </div>
            <div>
                <strong>Email:</strong> <?php echo htmlspecialchars($user['customer_email']); ?>
            </div>
            <div>
                <strong>Country:</strong> <?php echo htmlspecialchars($user['customer_country']); ?>
            </div>
            <div>
                <strong>City:</strong> <?php echo htmlspecialchars($user['customer_city']); ?>
            </div>
            <div>
                <strong>Contact:</strong> <?php echo htmlspecialchars($user['customer_contact']); ?>
            </div>
        </div>
    </div>
    
    <div style="background: white; border-radius: 12px; padding: 2rem;">
        <h2 style="margin-bottom: 1rem;">Recent Orders</h2>
        <?php if (empty($orders)): ?>
            <p style="color: #666;">No orders yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                        <tr>
                            <td>#<?php echo $order['invoice_no']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['purchase_date'])); ?></td>
                            <td><?php echo $order['resource_count']; ?></td>
                            <td>₵<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span style="padding: 0.3rem 0.8rem; border-radius: 20px; background: <?php echo $order['order_status'] == 'completed' ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $order['order_status'] == 'completed' ? '#155724' : '#856404'; ?>;">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="/app/views/orders/invoice.php?id=<?php echo $order['purchase_id']; ?>" 
                                   class="btn btn-secondary" style="text-decoration: none; color: white;">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
