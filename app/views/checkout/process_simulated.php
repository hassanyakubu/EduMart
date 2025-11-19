<?php
session_start();
require_once __DIR__ . '/../../models/Cart.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Download.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /app/views/auth/login.php');
    exit;
}

$cartModel = new Cart();
$orderModel = new Order();
$downloadModel = new Download();

$payment_method = $_POST['payment_method'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$account_name = $_POST['account_name'] ?? '';

// Validate inputs
if (empty($payment_method) || empty($phone_number) || empty($account_name)) {
    $_SESSION['error'] = 'Please fill all payment details.';
    header('Location: /app/views/checkout/payment_simulated.php');
    exit;
}

// Validate phone number
if (!preg_match('/^[0-9]{10}$/', $phone_number)) {
    $_SESSION['error'] = 'Invalid phone number format. Please enter 10 digits.';
    header('Location: /app/views/checkout/payment_simulated.php');
    exit;
}

$cart_items = $cartModel->getUserCart($_SESSION['user_id']);
$total = $cartModel->getTotal($_SESSION['user_id']);

if (empty($cart_items)) {
    $_SESSION['error'] = 'Your cart is empty.';
    header('Location: /app/views/cart/view.php');
    exit;
}

// Simulate payment processing delay
sleep(2);

// Simulate payment success (90% success rate for realism)
$payment_successful = (rand(1, 100) <= 90);

if ($payment_successful) {
    // Create order
    $invoice_no = rand(100000, 999999);
    $purchase_id = $orderModel->createOrder($_SESSION['user_id'], $invoice_no, 'completed');
    
    if ($purchase_id) {
        // Add items to downloads
        foreach ($cart_items as $item) {
            $downloadModel->logDownload($_SESSION['user_id'], $item['resource_id'], $purchase_id);
        }
        
        // Clear cart
        $cartModel->clearCart($_SESSION['user_id']);
        
        // Store payment details in session for success page
        $_SESSION['payment_details'] = [
            'method' => $payment_method,
            'phone' => $phone_number,
            'amount' => $total,
            'reference' => 'MOMO_' . time() . '_' . $purchase_id
        ];
        
        $_SESSION['success'] = 'Payment successful! Your resources are ready for download.';
        header('Location: /app/views/checkout/success.php?order=' . $purchase_id);
    } else {
        $_SESSION['error'] = 'Order creation failed. Please contact support.';
        header('Location: /app/views/checkout/payment_simulated.php');
    }
} else {
    // Simulate payment failure
    $_SESSION['error'] = 'Payment failed. Please check your mobile money account and try again.';
    header('Location: /app/views/checkout/payment_simulated.php');
}

exit;
?>
