-- ============================================================================
-- Fix Existing Purchases - Run this AFTER applying the main database fix
-- ============================================================================

-- This script fixes existing purchases in your database:
-- 1. Updates invoice numbers from 0 to proper format
-- 2. Shows orphaned purchases that need manual attention

-- ============================================================================
-- STEP 1: Fix Invoice Numbers
-- ============================================================================

-- Update all purchases with invoice_no = 0 to have proper invoice numbers
UPDATE purchases 
SET invoice_no = CONCAT('INV-', DATE_FORMAT(purchase_date, '%Y%m%d'), '-', LPAD(purchase_id, 6, '0'))
WHERE invoice_no = 0 OR invoice_no = '' OR CAST(invoice_no AS UNSIGNED) = 0;

-- Verify the update
SELECT purchase_id, customer_id, invoice_no, purchase_date, order_status
FROM purchases
ORDER BY purchase_id;

-- Expected result:
-- purchase_id 1 → INV-20251122-000001
-- purchase_id 2 → INV-20251122-000002
-- purchase_id 3 → INV-20251122-000003
-- etc.

-- ============================================================================
-- STEP 2: Check for Orphaned Purchases (purchases without order_items)
-- ============================================================================

-- Find purchases that have NO order_items
SELECT 
    p.purchase_id,
    p.customer_id,
    c.customer_name,
    c.customer_email,
    p.invoice_no,
    p.purchase_date,
    p.order_status,
    'MISSING ORDER_ITEMS!' as issue
FROM purchases p
JOIN customer c ON p.customer_id = c.customer_id
LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
WHERE oi.order_item_id IS NULL
ORDER BY p.purchase_id;

-- If this returns any rows, those purchases need order_items added manually
-- Students cannot access quizzes for these purchases!

-- ============================================================================
-- STEP 3: Manual Fix for Orphaned Purchases (EXAMPLE)
-- ============================================================================

-- If you know what was purchased, use this template to add order_items:

-- Example: Purchase 1 bought resource 3 (BECE English - 15.00)
-- INSERT INTO order_items (purchase_id, resource_id, qty, price)
-- VALUES (1, 3, 1, 15.00);

-- Also add download access:
-- INSERT INTO downloads (customer_id, resource_id, purchase_id)
-- SELECT customer_id, 3, 1 FROM purchases WHERE purchase_id = 1;

-- Repeat for each orphaned purchase

-- ============================================================================
-- STEP 4: Verify All Purchases Have Order Items
-- ============================================================================

-- This should return 0 rows after fixing
SELECT 
    p.purchase_id,
    p.invoice_no,
    c.customer_name,
    COUNT(oi.order_item_id) as item_count
FROM purchases p
JOIN customer c ON p.customer_id = c.customer_id
LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
GROUP BY p.purchase_id, p.invoice_no, c.customer_name
HAVING item_count = 0
ORDER BY p.purchase_id;

-- ============================================================================
-- STEP 5: Summary Report
-- ============================================================================

-- Get a summary of all purchases with their items
SELECT 
    p.purchase_id,
    p.invoice_no,
    c.customer_name,
    p.purchase_date,
    p.order_status,
    COUNT(oi.order_item_id) as items,
    GROUP_CONCAT(r.resource_title SEPARATOR ', ') as resources,
    SUM(oi.price) as total_amount
FROM purchases p
JOIN customer c ON p.customer_id = c.customer_id
LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
LEFT JOIN resources r ON oi.resource_id = r.resource_id
GROUP BY p.purchase_id, p.invoice_no, c.customer_name, p.purchase_date, p.order_status
ORDER BY p.purchase_id DESC;

-- ============================================================================
-- STEP 6: Check Quiz Access for Each Student
-- ============================================================================

-- See which categories each student can access quizzes for
SELECT 
    c.customer_id,
    c.customer_name,
    GROUP_CONCAT(DISTINCT cat.cat_name SEPARATOR ', ') as accessible_categories
FROM customer c
JOIN purchases p ON c.customer_id = p.customer_id
JOIN order_items oi ON p.purchase_id = oi.purchase_id
JOIN resources r ON oi.resource_id = r.resource_id
JOIN categories cat ON r.cat_id = cat.cat_id
WHERE c.user_type = 'student'
GROUP BY c.customer_id, c.customer_name
ORDER BY c.customer_id;

-- ============================================================================
-- NOTES:
-- ============================================================================

-- 1. Run this AFTER running db/fix_purchases_table.sql
-- 2. The invoice number update is safe and automatic
-- 3. Orphaned purchases need manual investigation
-- 4. Check Paystack dashboard for what was actually purchased
-- 5. Contact customers if you can't determine what they bought
-- 6. Future payments will work automatically with the fix applied

-- ============================================================================
