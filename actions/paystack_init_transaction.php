<?php
header('Content-Type: application/json');

// Include core and Paystack configuration
require_once '../settings/core.php';
require_once '../settings/paystack_config.php';

error_log("=== PAYSTACK INITIALIZE TRANSACTION ===");

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to complete payment'
    ]);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$amount = isset($input['amount']) ? floatval($input['amount']) : 0;
$customer_email = isset($input['email']) ? trim($input['email']) : '';

if (!$amount || !$customer_email) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid amount or email'
    ]);
    exit();
}

// Validate amount
if ($amount <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Amount must be greater than 0'
    ]);
    exit();
}

// Validate email
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email address'
    ]);
    exit();
}

try {
    // Generate unique reference
    $customer_id = get_user_id();
    $reference = 'AYA-' . $customer_id . '-' . time();
    
    // IMPORTANT: Store cart items in session BEFORE redirecting to Paystack
    // This ensures cart data is available when payment verification runs
    require_once __DIR__ . '/../controllers/cart_controller.php';
    $cart_items = get_user_cart_ctr($customer_id);
    
    if (!$cart_items || count($cart_items) == 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Your cart is empty. Please add items before checkout.'
        ]);
        exit();
    }
    
    // Store in session as backup
    $_SESSION['checkout_cart'] = $cart_items;
    $_SESSION['checkout_total'] = $amount;
    $_SESSION['checkout_customer_id'] = $customer_id;
    
    error_log("Initializing transaction - Customer: $customer_id, Amount: $amount GHS, Email: $customer_email");
    error_log("Cart items stored in session: " . count($cart_items) . " items");
    
    // Initialize Paystack transaction
    $paystack_response = paystack_initialize_transaction($amount, $customer_email, $reference);
    
    if (!$paystack_response) {
        throw new Exception("No response from Paystack API");
    }
    
    if (isset($paystack_response['status']) && $paystack_response['status'] === true) {
        // Store transaction reference in session for verification later
        $_SESSION['paystack_ref'] = $reference;
        $_SESSION['paystack_amount'] = $amount;
        $_SESSION['paystack_timestamp'] = time();
        
        error_log("Paystack transaction initialized successfully - Reference: $reference");
        
        echo json_encode([
            'status' => 'success',
            'authorization_url' => $paystack_response['data']['authorization_url'],
            'reference' => $reference,
            'access_code' => $paystack_response['data']['access_code'],
            'message' => 'Redirecting to payment gateway...'
        ]);
    } else {
        error_log("Paystack API error: " . json_encode($paystack_response));
        
        $error_message = $paystack_response['message'] ?? 'Payment gateway error';
        throw new Exception($error_message);
    }
    
} catch (Exception $e) {
    error_log("Error initializing Paystack transaction: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to initialize payment: ' . $e->getMessage()
    ]);
}
?>
