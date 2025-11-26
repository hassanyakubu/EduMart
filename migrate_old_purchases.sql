-- Migrate Old Purchases to order_items Table
-- This will add all existing purchases to the order_items table for earnings tracking

-- IMPORTANT: Run this ONLY ONCE to avoid duplicates!
-- This script assumes:
-- 1. You have purchases in the 'purchases' table
-- 2. You have downloads in the 'downloads' table
-- 3. The 'order_items' table is empty or you want to add missing items

-- Step 1: Check what data you have
-- Run this first to see your existing purchases:
SELECT 
    p.purchase_id,
    p.invoice_no,
    p.purchase_date,
    d.resource_id,
    r.resource_title,
    r.resource_price,
    c.customer_name as buyer
FROM purchases p
JOIN downloads d ON p.purchase_id = d.purchase_id
JOIN resources r ON d.resource_id = r.resource_id
JOIN customer c ON p.customer_id = c.customer_id
ORDER BY p.purchase_date DESC;

-- Step 2: Check if order_items already has data
-- Run this to see if you already have order items:
SELECT COUNT(*) as existing_order_items FROM order_items;

-- Step 3: Migrate old purchases to order_items
-- ONLY RUN THIS IF order_items is empty or missing old purchases!
-- This will insert all downloads as order_items with their prices

INSERT INTO order_items (purchase_id, resource_id, qty, price, created_at)
SELECT 
    d.purchase_id,
    d.resource_id,
    1 as qty,
    r.resource_price as price,
    p.purchase_date as created_at
FROM downloads d
JOIN resources r ON d.resource_id = r.resource_id
JOIN purchases p ON d.purchase_id = p.purchase_id
WHERE NOT EXISTS (
    -- Avoid duplicates: only insert if this combination doesn't exist
    SELECT 1 FROM order_items oi 
    WHERE oi.purchase_id = d.purchase_id 
    AND oi.resource_id = d.resource_id
);

-- Step 4: Verify the migration
-- Run this to see how many items were migrated:
SELECT 
    COUNT(*) as total_order_items,
    SUM(price) as total_revenue,
    SUM(price * 0.8) as creator_earnings,
    SUM(price * 0.2) as platform_commission
FROM order_items;

-- Step 5: Check earnings by creator
-- Run this to see earnings per creator:
SELECT 
    cr.creator_name,
    COUNT(oi.order_item_id) as total_sales,
    SUM(oi.price) as gross_revenue,
    SUM(oi.price * 0.8) as creator_earnings_80_percent,
    SUM(oi.price * 0.2) as platform_commission_20_percent
FROM order_items oi
JOIN resources r ON oi.resource_id = r.resource_id
JOIN creators cr ON r.creator_id = cr.creator_id
GROUP BY cr.creator_id
ORDER BY gross_revenue DESC;

-- DONE! Your old purchases should now show in earnings and analytics.
