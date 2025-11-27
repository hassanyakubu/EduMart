# Payment System Fix Instructions

## Problem Summary
Payments are not being recorded in the database because:
1. Missing controller helper functions (`create_order_ctr`, `add_order_details_ctr`, `record_payment_ctr`, etc.)
2. `purchases.invoice_no` column is INT instead of VARCHAR (can't store invoice strings like "INV-20251127-ABC123")
3. No `payments` table to track payment details
4. Missing records in `order_items` table prevents quiz access (students can't see quizzes for purchased categories)

## Solution Applied

### 1. Created Missing Controller Functions
Created two new files:
- `controllers/order_controller.php` - Contains order creation and payment recording functions
- `controllers/cart_controller.php` - Contains cart helper functions

These files provide the missing `_ctr` functions that `paystack_verify_payment.php` was calling.

### 2. Database Schema Fixes Required

You need to run the SQL commands in `db/fix_purchases_table.sql` to:
- Change `purchases.invoice_no` from INT to VARCHAR(50)
- Create the `payments` table for tracking payment details

## How to Apply the Fix

### Step 1: Fix the Database Schema

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select your database: `ecommerce_2025A_hassan_yakubu`
3. Click on the "SQL" tab
4. Copy and paste the contents of `db/fix_purchases_table.sql`
5. Click "Go" to execute

**Option B: Using MySQL Command Line**
```bash
mysql -u hassan.yakubu -p ecommerce_2025A_hassan_yakubu < db/fix_purchases_table.sql
```

**Option C: Using the PHP script (if database connection works)**
```bash
/Applications/XAMPP/xamppfiles/bin/php fix_payment_system.php
```

### Step 2: Verify the Fix

After running the SQL, verify the changes:

```sql
-- Check invoice_no column type (should be VARCHAR(50))
DESCRIBE purchases;

-- Check if payments table exists
SHOW TABLES LIKE 'payments';

-- Check payments table structure
DESCRIBE payments;
```

### Step 3: Test a Payment

1. Start your XAMPP server (Apache and MySQL)
2. Log in as a student
3. Add items to cart
4. Go to checkout
5. Complete payment via Paystack
6. After payment, check the database:

```sql
-- Check if order was created
SELECT * FROM purchases ORDER BY purchase_id DESC LIMIT 1;

-- Check if order items were created
SELECT * FROM order_items ORDER BY order_item_id DESC LIMIT 5;

-- Check if payment was recorded
SELECT * FROM payments ORDER BY payment_id DESC LIMIT 1;

-- Check if downloads were created (for resource access)
SELECT * FROM downloads ORDER BY download_id DESC LIMIT 5;
```

### Step 4: Verify Quiz Access

After a successful payment:

1. Log in as the student who made the purchase
2. Navigate to the quizzes page
3. You should now see published quizzes for the categories you purchased

The quiz access is determined by this query:
```sql
SELECT DISTINCT r.cat_id 
FROM order_items oi
JOIN resources r ON oi.resource_id = r.resource_id
JOIN purchases p ON oi.purchase_id = p.purchase_id
WHERE p.customer_id = YOUR_CUSTOMER_ID
```

## Troubleshooting

### Issue: "Access denied for user 'hassan.yakubu'@'localhost'"
**Solution:** 
- Make sure MySQL is running in XAMPP
- Verify the password in `settings/db_cred.php` is correct
- Try connecting via phpMyAdmin to confirm credentials

### Issue: Orders still not being created
**Solution:**
1. Check error logs: `tail -f /Applications/XAMPP/xamppfiles/logs/php_error_log`
2. Check if the controller files are being loaded:
   - `controllers/order_controller.php`
   - `controllers/cart_controller.php`
3. Verify `paystack_verify_payment.php` is including these files

### Issue: Quiz access still not working
**Solution:**
1. Verify `order_items` table has records for the purchase
2. Check that the resource's `cat_id` matches the quiz's `category_id`
3. Verify the quiz is published (`is_published = 1`)
4. Run the test script: `/Applications/XAMPP/xamppfiles/bin/php test_quiz_access.php`

## Files Modified/Created

### New Files:
- `controllers/order_controller.php` - Order and payment helper functions
- `controllers/cart_controller.php` - Cart helper functions
- `db/fix_purchases_table.sql` - Database schema fixes
- `fix_payment_system.php` - Automated fix script
- `PAYMENT_FIX_INSTRUCTIONS.md` - This file

### Modified Files:
- `actions/paystack_verify_payment.php` - Updated require paths and database connection

## What Happens During Payment Now

1. **User completes payment on Paystack**
2. **Paystack redirects to** `view/paystack_callback.php`
3. **Callback page calls** `actions/paystack_verify_payment.php`
4. **Verification script:**
   - Verifies payment with Paystack API
   - Calls `create_order_ctr()` to create purchase record
   - Calls `add_order_details_ctr()` for each cart item (creates order_items AND downloads)
   - Calls `record_payment_ctr()` to save payment details
   - Calls `empty_cart_ctr()` to clear the cart
5. **User redirected to** `view/payment_success.php`
6. **Student can now:**
   - View their orders
   - Download purchased resources
   - Access quizzes for purchased categories
   - See updated analytics (creators see earnings)

## Analytics Update

Once payments are properly recorded in `order_items`, the analytics will automatically update because:

- `sales_model.php` queries `order_items` table for earnings
- Creator earnings = 80% of sales (unless admin, then 100%)
- Platform commission = 20% of non-admin sales

Query used:
```sql
SELECT 
    COUNT(DISTINCT oi.order_item_id) as total_sales,
    SUM(r.resource_price) as gross_revenue,
    SUM(r.resource_price * 0.8) as net_earnings
FROM order_items oi
JOIN resources r ON oi.resource_id = r.resource_id
WHERE r.creator_id = ?
```

## Need Help?

If you encounter any issues:
1. Check the PHP error log
2. Check the browser console for JavaScript errors
3. Verify all files are in the correct locations
4. Ensure XAMPP MySQL is running
5. Test database connection with phpMyAdmin
