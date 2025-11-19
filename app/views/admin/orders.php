<?php
session_start();
require_once __DIR__ . '/../../controllers/admin_controller.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$controller = new admin_controller();
$controller->manageOrders();

$page_title = 'Manage Orders';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">Manage Orders</h1>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['invoice_no']; ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($order['purchase_date'])); ?></td>
                        <td><?php echo $order['resource_count']; ?></td>
                        <td>
                            <span style="padding: 0.3rem 0.8rem; border-radius: 20px; background: <?php echo $order['order_status'] == 'completed' ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $order['order_status'] == 'completed' ? '#155724' : '#856404'; ?>;">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
