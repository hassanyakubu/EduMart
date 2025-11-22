<?php
session_start();
require_once __DIR__ . '/../../controllers/cart_controller.php';

if (!isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../config/config.php';
    $_SESSION['error'] = 'Please log in to view your cart.';
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$controller = new cart_controller();
$controller->view();

$page_title = 'Shopping Cart';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div style="background: white; padding: 3rem; border-radius: 12px; text-align: center;">
            <p style="font-size: 1.2rem; color: #666;">Your cart is empty</p>
            <a href="<?php echo url('app/views/resources/list.php'); ?>" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">
                Browse Resources
            </a>
        </div>
    <?php else: ?>
        <div style="background: white; border-radius: 12px; padding: 2rem;">
            <table>
                <thead>
                    <tr>
                        <th>Resource</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <img src="<?php echo asset($item['resource_image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($item['resource_title']); ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    <span><?php echo htmlspecialchars($item['resource_title']); ?></span>
                                </div>
                            </td>
                            <td>₵<?php echo number_format($item['resource_price'], 2); ?></td>
                            <td><?php echo $item['qty']; ?></td>
                            <td>₵<?php echo number_format($item['resource_price'] * $item['qty'], 2); ?></td>
                            <td>
                                <a href="<?php echo url('app/views/cart/remove.php?id=' . $item['resource_id']); ?>" 
                                   class="btn btn-danger" style="text-decoration: none; color: white;">
                                    Remove
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 2rem; text-align: right;">
                <h2>Total: ₵<?php echo number_format($total, 2); ?></h2>
                <a href="<?php echo url('app/views/checkout/payment.php'); ?>" class="btn btn-primary" style="margin-top: 1rem; display: inline-block; text-decoration: none;">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
