# Test Real Payment - Debugging Guide

## The Problem

All payments from today (Nov 27) have **NO order_items**. This means:
- Purchases are being created ✓
- But order_items are NOT being created ✗

## Most Likely Cause

The cart is **empty** when `paystack_verify_payment.php` runs. This could happen if:

1. **Cart cleared too early** - Something clears the cart before verification
2. **Session lost** - User's session expires during payment
3. **Wrong customer ID** - Verification uses wrong customer_id to get cart

## How to Debug

### Step 1: Check PHP Error Log

Look for errors during payment:
```bash
tail -f /Applications/XAMPP/xamppfiles/logs/php_error_log
```

Or check on server:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/check_payment_logs.php
```

### Step 2: Add Debug Logging

Add this to `actions/paystack_verify_payment.php` right before line 171 (where it loops through cart items):

```php
// DEBUG: Log cart items
error_log("DEBUG: Cart items count: " . count($cart_items));
error_log("DEBUG: Cart items: " . print_r($cart_items, true));
error_log("DEBUG: Customer ID: $customer_id");
```

### Step 3: Make a Test Payment

1. Log in as a student
2. Add ONE item to cart
3. Go to checkout
4. Complete payment
5. Immediately check the error log

Look for these log messages:
- "Cart items count: X" - Should be > 0
- "Order created - ID: X"
- "Order detail added - Product: X"

If you see "Cart items count: 0", that's the problem!

### Step 4: Check Cart Before Payment

Before clicking "Pay Now", open browser console and run:
```javascript
fetch('../actions/get_cart_action.php', {method: 'POST'})
  .then(r => r.json())
  .then(d => console.log('Cart:', d));
```

This shows what's in the cart before payment.

## Quick Fix Options

### Option 1: Store Cart in Session

Modify `js/checkout.js` to store cart items in session before payment:

```javascript
// Before redirecting to Paystack
sessionStorage.setItem('checkout_cart', JSON.stringify(cartItems));
```

Then in `paystack_verify_payment.php`, retrieve from session if cart is empty.

### Option 2: Pass Cart in Payment Metadata

Modify `actions/paystack_init_transaction.php` to include cart items in Paystack metadata.

### Option 3: Don't Clear Cart Until After Verification

The safest option - keep cart items until AFTER order_items are created.

## Manual Fix for Today's Purchases

For the 9 purchases from today that have no order_items, you need to manually add them if you know what was purchased.

**Check what's in the cart_items table:**
```sql
SELECT ci.*, r.resource_title, c.customer_name
FROM cart_items ci
JOIN carts ca ON ci.cart_id = ca.cart_id
JOIN customer c ON ca.user_id = c.customer_id
JOIN resources r ON ci.resource_id = r.resource_id;
```

**If cart is empty, check downloads table:**
```sql
-- See if downloads were created (they shouldn't be without order_items)
SELECT d.*, r.resource_title, c.customer_name
FROM downloads d
JOIN resources r ON d.resource_id = r.resource_id
JOIN customer c ON d.customer_id = c.customer_id
WHERE d.purchase_id >= 9
ORDER BY d.download_id DESC;
```

**Manually add order_items for a purchase:**
```sql
-- Example: Purchase 17 bought resource 3
INSERT INTO order_items (purchase_id, resource_id, qty, price)
VALUES (17, 3, 1, 15.00);

-- Also add download
INSERT INTO downloads (customer_id, resource_id, purchase_id)
SELECT customer_id, 3, 17 FROM purchases WHERE purchase_id = 17;
```

## Test the Functions Work

Run this to verify the functions themselves work:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_order_creation.php
```

If this succeeds, the functions are fine - the issue is in the payment flow.

## Next Steps

1. **Add debug logging** to paystack_verify_payment.php
2. **Make a test payment** and watch the logs
3. **Check if cart is empty** when verification runs
4. **Fix the root cause** based on what you find
5. **Manually fix** today's purchases if needed

The functions are working (test_payment_flow.php passed), so the issue is that the cart is empty when verification runs!
