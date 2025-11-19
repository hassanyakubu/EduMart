<?php
session_start();
require_once __DIR__ . '/../../controllers/order_controller.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$controller = new order_controller();
$controller->invoice($_GET['id']);

$page_title = 'Invoice';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div style="background: white; border-radius: 12px; padding: 3rem; margin: 2rem 0;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1>EduMart</h1>
            <p style="color: #666;">Digital Learning Resources Marketplace</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <h3>Invoice Details</h3>
                <p><strong>Invoice #:</strong> <?php echo $order['invoice_no']; ?></p>
                <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['purchase_date'])); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($order['order_status']); ?></p>
            </div>
            <div>
                <h3>Customer Details</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            </div>
        </div>
        
        <h3 style="margin-bottom: 1rem;">Order Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Resource</th>
                    <th>Price</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($order_items as $item): 
                    $total += $item['resource_price'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['resource_title']); ?></td>
                        <td>₵<?php echo number_format($item['resource_price'], 2); ?></td>
                        <td>
                            <a href="/app/views/downloads/download.php?id=<?php echo $item['resource_id']; ?>&order=<?php echo $order['purchase_id']; ?>" 
                               class="btn btn-primary" style="text-decoration: none;">
                                Download
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>₵<?php echo number_format($total, 2); ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        
        <div style="text-align: center; margin-top: 2rem;">
            <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
