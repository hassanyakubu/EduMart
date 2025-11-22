<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/cart_model.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . url('app/views/auth/login.php'));
    exit;
}

$cartModel = new cart_model();
$cart_items = $cartModel->getUserCart($_SESSION['user_id']);
$total = $cartModel->getTotal($_SESSION['user_id']);

if (empty($cart_items)) {
    $_SESSION['error'] = 'Your cart is empty.';
    header('Location: ' . url('app/views/cart/view.php'));
    exit;
}

$page_title = 'Checkout';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0;">Checkout</h1>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div style="background: white; border-radius: 12px; padding: 2rem;">
            <h2 style="margin-bottom: 1rem;">Payment Method</h2>
            <p style="color: #666; margin-bottom: 1.5rem;">
                Pay securely with Paystack. Supports MTN MoMo, Vodafone Cash, AirtelTigo, and Card payments.
            </p>
            
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">💳 Paystack Payment Gateway</h3>
                <p style="font-size: 0.9rem; color: #666;">
                    Pay securely with Paystack - Supports Mobile Money, Cards, and Bank Transfer
                </p>
            </div>
            
            <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #FFD947;">
                <p style="font-size: 0.9rem; color: #856404; margin-bottom: 0.5rem;">
                    <strong>📱 Test Mobile Money Numbers:</strong>
                </p>
                <ul style="font-size: 0.85rem; color: #856404; margin: 0; padding-left: 1.5rem;">
                    <li>MTN: 0241234567 or 0551234567</li>
                    <li>Vodafone: 0201234567</li>
                    <li>AirtelTigo: 0271234567</li>
                </ul>
            </div>
            
            <button onclick="payWithPaystack()" class="btn btn-primary btn-block" style="margin-top: 2rem;">
                💳 Pay ₵<?php echo number_format($total, 2); ?> with Paystack
            </button>
        </div>
        
        <div style="background: white; border-radius: 12px; padding: 2rem;">
            <h2 style="margin-bottom: 1rem;">Order Summary</h2>
            <?php foreach ($cart_items as $item): ?>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                    <span><?php echo htmlspecialchars($item['resource_title']); ?></span>
                    <span>₵<?php echo number_format($item['resource_price'] * $item['qty'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            
            <div style="display: flex; justify-content: space-between; padding: 1rem 0; margin-top: 1rem; font-weight: 700; font-size: 1.2rem;">
                <span>Total</span>
                <span style="color: #FFD947;">₵<?php echo number_format($total, 2); ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../settings/paystack_config.php'; ?>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
function payWithPaystack() {
    var handler = PaystackPop.setup({
        key: '<?php echo PAYSTACK_PUBLIC_KEY; ?>',
        email: '<?php echo $_SESSION['user_email']; ?>',
        amount: <?php echo $total * 100; ?>,
        currency: 'GHS',
        ref: 'EDUMART_' + Date.now() + '_' + <?php echo $_SESSION['user_id']; ?>,
        callback: function(response) {
            window.location = '<?php echo url('app/views/checkout/verify.php?reference='); ?>' + response.reference;
        },
        onClose: function() {
            alert('Payment cancelled');
        }
    });
    handler.openIframe();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
