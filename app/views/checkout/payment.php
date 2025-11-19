<?php
session_start();
require_once __DIR__ . '/../../controllers/checkout_controller.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$controller = new checkout_controller();
$controller->checkout();

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
            
            <form action="/app/views/checkout/process.php" method="POST">
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Paystack Payment Gateway</h3>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="radio" name="payment_method" value="paystack" checked required>
                            <span>Pay with Paystack (MTN MoMo, Vodafone, AirtelTigo, Card)</span>
                        </label>
                    </div>
                    <p style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">
                        You will be redirected to Paystack to complete your payment securely.
                    </p>
                </div>
                
                <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #FFD947;">
                    <p style="font-size: 0.9rem; color: #856404;">
                        <strong>Note:</strong> This is a test environment. Use Paystack test cards for payment.
                    </p>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 2rem;">
                    Proceed to Payment
                </button>
            </form>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
