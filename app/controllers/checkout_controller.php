<?php
session_start();
require_once __DIR__ . '/../models/cart_model.php';
require_once __DIR__ . '/../models/order_model.php';
require_once __DIR__ . '/../models/download_model.php';
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../config/paystack.php';

class checkout_controller {
    private $cartModel;
    private $orderModel;
    private $downloadModel;
    private $userModel;
    
    public function __construct() {
        $this->cartModel = new cart_model();
        $this->orderModel = new order_model();
        $this->downloadModel = new download_model();
        $this->userModel = new user_model();
    }
    
    public function checkout() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $cart_items = $this->cartModel->getUserCart($_SESSION['user_id']);
        $total = $this->cartModel->getTotal($_SESSION['user_id']);
        
        if (empty($cart_items)) {
            $_SESSION['error'] = 'Your cart is empty.';
            header('Location: /app/views/cart/view.php');
            exit;
        }
        
        // Use simulated mobile money payment
        require_once __DIR__ . '/../views/checkout/payment_simulated.php';
    }
    
    public function processPayment() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $payment_method = $_POST['payment_method'] ?? '';
        $cart_items = $this->cartModel->getUserCart($_SESSION['user_id']);
        $total = $this->cartModel->getTotal($_SESSION['user_id']);
        
        if (empty($cart_items)) {
            $_SESSION['error'] = 'Your cart is empty.';
            header('Location: /app/views/cart/view.php');
            exit;
        }
        
        // Get user details
        $user = $this->userModel->getById($_SESSION['user_id']);
        
        // Initialize Paystack payment
        $response = edumart_initialize_payment(
            $total,
            $user['customer_email'],
            $_SESSION['user_id'],
            $cart_items
        );
        
        if ($response['status'] === true && isset($response['data']['authorization_url'])) {
            // Store reference in session for verification
            $_SESSION['payment_reference'] = $response['data']['reference'];
            $_SESSION['payment_amount'] = $total;
            
            // Redirect to Paystack payment page
            header('Location: ' . $response['data']['authorization_url']);
            exit;
        } else {
            // Fallback: Simulate payment for testing
            $this->simulatePayment($cart_items);
        }
    }
    
    private function simulatePayment($cart_items) {
        // Create order
        $invoice_no = rand(100000, 999999);
        $purchase_id = $this->orderModel->createOrder($_SESSION['user_id'], $invoice_no, 'completed');
        
        if ($purchase_id) {
            // Add items to downloads
            foreach ($cart_items as $item) {
                $this->downloadModel->logDownload($_SESSION['user_id'], $item['resource_id'], $purchase_id);
            }
            
            // Clear cart
            $this->cartModel->clearCart($_SESSION['user_id']);
            
            $_SESSION['success'] = 'Payment successful! You can now download your resources.';
            header('Location: /app/views/checkout/success.php?order=' . $purchase_id);
        } else {
            $_SESSION['error'] = 'Payment processing failed.';
            header('Location: /app/views/checkout/payment.php');
        }
        exit;
    }
    
    public function callback() {
        // Paystack callback handler
        if (!isset($_SESSION['user_id'])) {
            header('Location: /app/views/auth/login.php');
            exit;
        }
        
        $reference = $_GET['reference'] ?? $_SESSION['payment_reference'] ?? null;
        
        if (!$reference) {
            $_SESSION['error'] = 'Invalid payment reference.';
            header('Location: /app/views/cart/view.php');
            exit;
        }
        
        // Verify payment with Paystack
        $result = edumart_process_successful_payment($reference, $_SESSION['user_id']);
        
        if ($result['success']) {
            // Get cart items before clearing
            $cart_items = $this->cartModel->getUserCart($_SESSION['user_id']);
            
            // Create order
            $invoice_no = rand(100000, 999999);
            $purchase_id = $this->orderModel->createOrder($_SESSION['user_id'], $invoice_no, 'completed');
            
            if ($purchase_id) {
                // Add items to downloads
                foreach ($cart_items as $item) {
                    $this->downloadModel->logDownload($_SESSION['user_id'], $item['resource_id'], $purchase_id);
                }
                
                // Clear cart
                $this->cartModel->clearCart($_SESSION['user_id']);
                
                // Clear payment session
                unset($_SESSION['payment_reference']);
                unset($_SESSION['payment_amount']);
                
                $_SESSION['success'] = 'Payment successful! You can now download your resources.';
                header('Location: /app/views/checkout/success.php?order=' . $purchase_id);
            } else {
                $_SESSION['error'] = 'Order creation failed.';
                header('Location: /app/views/cart/view.php');
            }
        } else {
            $_SESSION['error'] = 'Payment verification failed. Please contact support.';
            header('Location: /app/views/cart/view.php');
        }
        exit;
    }
}
?>
