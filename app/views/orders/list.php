<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/order_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$orderModel = new order_model();
$orders = $orderModel->getOrdersByUser($_SESSION['user_id']);

$page_title = 'My Orders';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">My Orders</h1>
    
    <div class="table-container">
        <?php if (empty($orders)): ?>
            <p style="text-align: center; color: #666;">No orders found.</p>
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
                    <?php foreach ($orders as $order): ?>
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
                                <a href="<?php echo url('app/views/profile/my_resources.php'); ?>" 
                                   class="btn btn-secondary" style="text-decoration: none; color: white;">
                                    View Resources
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
