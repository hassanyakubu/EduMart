<?php
session_start();
require_once __DIR__ . '/../../models/Cart.php';
require_once __DIR__ . '/../../models/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /app/views/auth/login.php');
    exit;
}

$cartModel = new Cart();
$userModel = new User();

$cart_items = $cartModel->getUserCart($_SESSION['user_id']);
$total = $cartModel->getTotal($_SESSION['user_id']);
$user = $userModel->getById($_SESSION['user_id']);

if (empty($cart_items)) {
    $_SESSION['error'] = 'Your cart is empty.';
    header('Location: /app/views/cart/view.php');
    exit;
}

$page_title = 'Checkout - Mobile Money Payment';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1 style="margin: 2rem 0; text-align: center;">Complete Your Payment</h1>
    
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem; max-width: 1000px; margin: 0 auto;">
        <!-- Payment Form -->
        <div class="payment-card">
            <div class="payment-header">
                <h2>📱 Mobile Money Payment</h2>
                <p>Choose your preferred mobile money provider</p>
            </div>
            
            <form action="/app/views/checkout/process_simulated.php" method="POST" id="paymentForm">
                <!-- Mobile Money Provider Selection -->
                <div class="payment-methods">
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="mtn_momo" required checked>
                        <div class="method-content">
                            <div class="method-icon" style="background: #FFCC00;">
                                <span style="font-size: 1.5rem; font-weight: bold;">MTN</span>
                            </div>
                            <div class="method-details">
                                <strong>MTN Mobile Money</strong>
                                <small>Pay with MTN MoMo</small>
                            </div>
                        </div>
                    </label>
                    
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="vodafone_cash">
                        <div class="method-content">
                            <div class="method-icon" style="background: #E60000;">
                                <span style="font-size: 1.2rem; font-weight: bold; color: white;">VOD</span>
                            </div>
                            <div class="method-details">
                                <strong>Vodafone Cash</strong>
                                <small>Pay with Vodafone</small>
                            </div>
                        </div>
                    </label>
                    
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="airteltigo">
                        <div class="method-content">
                            <div class="method-icon" style="background: #ED1C24;">
                                <span style="font-size: 1.2rem; font-weight: bold; color: white;">AT</span>
                            </div>
                            <div class="method-details">
                                <strong>AirtelTigo Money</strong>
                                <small>Pay with AirtelTigo</small>
                            </div>
                        </div>
                    </label>
                </div>
                
                <!-- Phone Number Input -->
                <div class="form-group" style="margin-top: 2rem;">
                    <label for="phone_number">📞 Mobile Money Number</label>
                    <input type="tel" 
                           id="phone_number" 
                           name="phone_number" 
                           placeholder="e.g., 0244123456" 
                           pattern="[0-9]{10}" 
                           required
                           style="font-size: 1.1rem; letter-spacing: 1px;">
                    <small style="color: #666; display: block; margin-top: 0.5rem;">
                        Enter your 10-digit mobile money number
                    </small>
                </div>
                
                <!-- Account Name -->
                <div class="form-group">
                    <label for="account_name">👤 Account Name</label>
                    <input type="text" 
                           id="account_name" 
                           name="account_name" 
                           value="<?php echo htmlspecialchars($user['customer_name']); ?>" 
                           required>
                </div>
                
                <!-- Payment Info -->
                <div class="payment-info-box">
                    <div class="info-row">
                        <span>Amount to Pay:</span>
                        <strong style="font-size: 1.5rem; color: var(--primary-yellow);">
                            ₵<?php echo number_format($total, 2); ?>
                        </strong>
                    </div>
                    <div class="info-row">
                        <span>Transaction Fee:</span>
                        <strong>FREE</strong>
                    </div>
                    <div class="info-row total-row">
                        <span>Total:</span>
                        <strong style="font-size: 1.75rem;">₵<?php echo number_format($total, 2); ?></strong>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 2rem; padding: 1.25rem;">
                    <span id="btnText">🔒 Pay ₵<?php echo number_format($total, 2); ?> Securely</span>
                    <span id="btnLoading" style="display: none;">
                        <span class="spinner"></span> Processing...
                    </span>
                </button>
                
                <p style="text-align: center; margin-top: 1rem; color: #666; font-size: 0.9rem;">
                    🔐 Your payment is secure and encrypted
                </p>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div class="order-summary-card">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.3rem;">📦 Order Summary</h3>
            
            <div class="summary-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="summary-item">
                        <img src="/public/<?php echo $item['resource_image'] ?? 'assets/images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($item['resource_title']); ?>">
                        <div class="item-details">
                            <strong><?php echo htmlspecialchars($item['resource_title']); ?></strong>
                            <span class="item-price">₵<?php echo number_format($item['resource_price'], 2); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="summary-total">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>₵<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Tax:</span>
                    <span>₵0.00</span>
                </div>
                <div class="total-row final-total">
                    <strong>Total:</strong>
                    <strong style="color: var(--primary-yellow); font-size: 1.5rem;">
                        ₵<?php echo number_format($total, 2); ?>
                    </strong>
                </div>
            </div>
            
            <div class="security-badges">
                <div class="badge">🔒 Secure Payment</div>
                <div class="badge">✓ Instant Delivery</div>
                <div class="badge">💯 Money Back Guarantee</div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.payment-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
}

.payment-header h2 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.payment-header p {
    color: #666;
    font-size: 1rem;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.payment-method-card {
    border: 3px solid #e0e0e0;
    border-radius: 12px;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: block;
}

.payment-method-card:hover {
    border-color: #FFD947;
    background: #fffef8;
    transform: translateX(5px);
}

.payment-method-card input[type="radio"] {
    display: none;
}

.payment-method-card input[type="radio"]:checked + .method-content {
    border-left: 4px solid #FFD947;
}

.payment-method-card input[type="radio"]:checked ~ * {
    background: linear-gradient(135deg, #fffef8 0%, #fff9e6 100%);
}

.method-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-left: 0.5rem;
    transition: all 0.3s ease;
}

.method-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.method-details {
    flex: 1;
}

.method-details strong {
    display: block;
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 0.25rem;
}

.method-details small {
    color: #666;
    font-size: 0.9rem;
}

.payment-info-box {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #dee2e6;
}

.info-row:last-child {
    border-bottom: none;
}

.total-row {
    padding-top: 1rem;
    margin-top: 0.5rem;
    border-top: 2px solid #FFD947 !important;
}

.order-summary-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.summary-items {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 1.5rem;
}

.summary-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    background: #f8f9fa;
}

.summary-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.item-details strong {
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
    color: #333;
}

.item-price {
    color: #FFD947;
    font-weight: 700;
    font-size: 1.1rem;
}

.summary-total {
    border-top: 2px solid #e0e0e0;
    padding-top: 1rem;
}

.summary-total .total-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 1rem;
}

.final-total {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 2px solid #FFD947 !important;
}

.security-badges {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e0e0e0;
}

.badge {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    padding: 0.75rem;
    border-radius: 8px;
    text-align: center;
    font-size: 0.9rem;
    font-weight: 600;
    color: #2e7d32;
}

.spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 3px solid rgba(255,255,255,.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 968px) {
    .container > div {
        grid-template-columns: 1fr !important;
    }
    
    .order-summary-card {
        position: static;
    }
}
</style>

<script>
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    const submitBtn = this.querySelector('button[type="submit"]');
    
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-block';
    submitBtn.disabled = true;
});

// Auto-format phone number
document.getElementById('phone_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) value = value.slice(0, 10);
    e.target.value = value;
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
