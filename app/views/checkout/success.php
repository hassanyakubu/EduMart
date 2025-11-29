<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/order_model.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order'])) {
    header('Location: ' . url('app/views/home/index.php'));
    exit;
}

$orderModel = new order_model();
$order = $orderModel->getOrderById($_GET['order']);
$order_items = $orderModel->getOrderItems($_GET['order']);

$page_title = 'Payment Success';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="success-container">
        <div class="success-animation">
            <div class="checkmark-circle">
                <div class="checkmark"></div>
            </div>
        </div>
        
        <div class="success-badge">✓ Order Confirmed</div>
        
        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-subtitle">
            Your order #<?php echo $order['invoice_no']; ?> is ready
        </p>
        
        <?php if (isset($_SESSION['payment_details'])): 
            $payment = $_SESSION['payment_details'];
        ?>
        <div class="payment-receipt">
            <h3>📱 Payment Receipt</h3>
            <div class="receipt-row">
                <span>Payment Method:</span>
                <strong><?php echo strtoupper(str_replace('_', ' ', $payment['method'])); ?></strong>
            </div>
            <div class="receipt-row">
                <span>Phone Number:</span>
                <strong><?php echo $payment['phone']; ?></strong>
            </div>
            <div class="receipt-row">
                <span>Amount Paid:</span>
                <strong style="color: var(--primary-yellow); font-size: 1.3rem;">
                    ₵<?php echo number_format($payment['amount'], 2); ?>
                </strong>
            </div>
            <div class="receipt-row">
                <span>Reference:</span>
                <strong><?php echo $payment['reference']; ?></strong>
            </div>
        </div>
        <?php 
            unset($_SESSION['payment_details']);
        endif; 
        ?>
        
        <div class="resources-section">
            <h2>📚 Your Resources</h2>
            <div class="resources-grid">
                <?php foreach ($order_items as $item): ?>
                    <div class="resource-download-card">
                        <img src="<?php echo asset($item['resource_image'] ?? 'assets/images/placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($item['resource_title']); ?>">
                        <div class="resource-info">
                            <strong><?php echo htmlspecialchars($item['resource_title']); ?></strong>
                            <span class="resource-price">₵<?php echo number_format($item['resource_price'], 2); ?></span>
                        </div>
                        <a href="<?php echo url('app/views/resources/download_file.php?id=' . $item['resource_id']); ?>" 
                           class="btn btn-success btn-block">
                            Download Now
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="<?php echo url('app/views/profile/my_resources.php'); ?>" 
               class="btn btn-secondary">
                My Resources
            </a>
            <a href="<?php echo url('app/views/resources/list.php'); ?>" 
               class="btn btn-primary">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<style>
.success-container {
    background: white;
    border-radius: 24px;
    padding: 4rem 3rem;
    text-align: center;
    margin: 3rem auto;
    max-width: 800px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
}

.success-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #FFD947, #FFC107, #FFD947);
}

.success-animation {
    margin-bottom: 2rem;
}

.checkmark-circle {
    width: 110px;
    height: 110px;
    margin: 0 auto;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: popIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
}

@keyframes popIn {
    0% { 
        transform: scale(0) rotate(0deg); 
        opacity: 0;
    }
    50% { 
        transform: scale(1.15) rotate(180deg); 
    }
    100% { 
        transform: scale(1) rotate(360deg); 
        opacity: 1;
    }
}

.checkmark {
    width: 55px;
    height: 55px;
    border: 5px solid white;
    border-top: none;
    border-left: none;
    transform: rotate(45deg);
    animation: drawCheck 0.5s ease-out 0.4s both;
}

@keyframes drawCheck {
    0% { 
        height: 0; 
        width: 0; 
        opacity: 0;
    }
    50% {
        height: 55px;
        width: 0;
        opacity: 1;
    }
    100% { 
        height: 55px; 
        width: 27px; 
        opacity: 1;
    }
}

.success-badge {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    padding: 10px 24px;
    border-radius: 50px;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 20px;
    font-size: 14px;
    border: 2px solid #6ee7b7;
}

.success-title {
    font-size: 2.5rem;
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-weight: 800;
}

.success-subtitle {
    font-size: 1.2rem;
    color: #6b7280;
    margin-bottom: 2rem;
}

.payment-receipt {
    background: #f9fafb;
    border-radius: 16px;
    padding: 2rem;
    margin: 2rem 0;
    text-align: left;
    border: 2px solid #e5e7eb;
}

.payment-receipt h3 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #1f2937;
    font-weight: 700;
}

.receipt-row {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.receipt-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.resources-section {
    margin: 3rem 0;
}

.resources-section h2 {
    margin-bottom: 2rem;
    color: #1f2937;
    font-weight: 700;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.resource-download-card {
    background: #f9fafb;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid #e5e7eb;
}

.resource-download-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-color: #FFD947;
}

.resource-download-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.resource-info {
    margin-bottom: 1rem;
}

.resource-info strong {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
}

.resource-price {
    color: var(--primary-yellow);
    font-weight: 700;
    font-size: 1.2rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.action-buttons .btn {
    padding: 14px 32px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.action-buttons .btn-primary {
    background: linear-gradient(135deg, #FFD947 0%, #FFC107 100%);
    color: #1f2937;
}

.action-buttons .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 217, 71, 0.4);
}

.action-buttons .btn-secondary {
    background: white;
    color: #6b7280;
    border: 2px solid #e5e7eb;
}

.action-buttons .btn-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .resources-grid {
        grid-template-columns: 1fr;
    }
    
    .success-container {
        padding: 3rem 2rem;
    }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
