<?php
/**
 * Cart Controller Helper Functions
 * These are wrapper functions for backward compatibility
 */

require_once __DIR__ . '/../app/models/cart_model.php';

/**
 * Get user cart items
 */
function get_user_cart_ctr($customer_id) {
    $cartModel = new cart_model();
    $cart_items = $cartModel->getUserCart($customer_id);
    
    // Transform data to match expected format
    $formatted_items = [];
    foreach ($cart_items as $item) {
        $formatted_items[] = [
            'p_id' => $item['resource_id'],
            'product_id' => $item['resource_id'],
            'product_title' => $item['resource_title'],
            'product_price' => $item['resource_price'],
            'product_image' => $item['resource_image'],
            'qty' => $item['qty'],
            'subtotal' => $item['resource_price'] * $item['qty']
        ];
    }
    
    return $formatted_items;
}

/**
 * Empty user cart
 */
function empty_cart_ctr($customer_id) {
    $cartModel = new cart_model();
    return $cartModel->clearCart($customer_id);
}
?>
