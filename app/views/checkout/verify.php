<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../../settings/paystack_config.php';
require_once __DIR__ . '/../../models/cart_model.php';
require_once __DIR__ . '/../../models/order_model.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['reference'])) {
    header('Location: ' . url('app/views/cart/view.php'));
    exit;
}

$reference = $_GET['reference'];

// Verify payment with Paystack using the config function
$result = paystack_verify_transaction($reference);

if ($result['status'] && $result['data']['status'] == 'success') {
    // Payment successful - Create order
    $cartModel = new cart_model();
    $orderModel = new order_model();
    
    $cart_items = $cartModel->getUserCart($_SESSION['user_id']);
    
    if (!empty($cart_items)) {
        // Create order
        $invoice_no = $reference; // Use Paystack reference as invoice number
        $order_id = $orderModel->createOrder(
            $_SESSION['user_id'],
            $invoice_no,
            'completed'
        );
        
        if ($order_id) {
            // Add items to downloads table (this gives user access to resources)
            require_once __DIR__ . '/../../models/download_model.php';
            $downloadModel = new download_model();
            
            foreach ($cart_items as $item) {
                $downloadModel->addDownload($order_id, $item['resource_id']);
            }
            
            // Clear cart
            $cartModel->clearCart($_SESSION['user_id']);
            
            // Redirect to success page
            $_SESSION['success'] = 'Payment successful! Your order has been placed.';
            header('Location: ' . url('app/views/checkout/success.php?order=' . $order_id));
            exit;
        }
    }
}

// Payment failed
$_SESSION['error'] = 'Payment verification failed. Please try again.';
header('Location: ' . url('app/views/cart/view.php'));
exit;
?>
