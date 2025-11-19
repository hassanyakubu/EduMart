<?php
/**
 * Paystack Configuration for EduMart
 * Integrates with the provided Paystack template
 */

// Load the Paystack configuration from settings
require_once __DIR__ . '/../../settings/paystack_config.php';
require_once __DIR__ . '/../../settings/db_cred.php';

// Update APP_BASE_URL for EduMart
if (!defined('EDUMART_BASE_URL')) {
    define('EDUMART_BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);
}

if (!defined('EDUMART_CALLBACK_URL')) {
    define('EDUMART_CALLBACK_URL', EDUMART_BASE_URL . '/app/views/checkout/paystack_callback.php');
}

/**
 * Initialize Paystack payment for EduMart
 */
function edumart_initialize_payment($amount, $email, $customer_id, $cart_items) {
    $reference = 'EDUMART_' . time() . '_' . $customer_id;
    
    // Convert GHS to pesewas (1 GHS = 100 pesewas)
    $amount_in_pesewas = round($amount * 100);
    
    $data = [
        'amount' => $amount_in_pesewas,
        'email' => $email,
        'reference' => $reference,
        'callback_url' => EDUMART_CALLBACK_URL,
        'metadata' => [
            'customer_id' => $customer_id,
            'cart_items' => json_encode($cart_items),
            'currency' => 'GHS',
            'app' => 'EduMart',
            'environment' => APP_ENVIRONMENT
        ]
    ];
    
    $response = paystack_api_request('POST', PAYSTACK_INIT_ENDPOINT, $data);
    
    return $response;
}

/**
 * Verify Paystack payment for EduMart
 */
function edumart_verify_payment($reference) {
    return paystack_verify_transaction($reference);
}

/**
 * Process successful payment
 */
function edumart_process_successful_payment($reference, $customer_id) {
    // Verify the transaction
    $verification = edumart_verify_payment($reference);
    
    if ($verification['status'] && $verification['data']['status'] === 'success') {
        return [
            'success' => true,
            'data' => $verification['data'],
            'amount' => $verification['data']['amount'] / 100, // Convert back to GHS
            'reference' => $reference
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Payment verification failed'
    ];
}
?>
