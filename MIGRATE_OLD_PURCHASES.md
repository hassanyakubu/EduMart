# Migrate Old Purchases to Earnings System

## Problem
Purchases made **before** adding the earnings system won't show in analytics because they weren't saved to the `order_items` table.

## Solution
Run the migration script to copy old purchases from `downloads` table to `order_items` table.

## Steps

### 1. Open phpMyAdmin
- Select your database
- Go to SQL tab

### 2. Check Your Old Purchases
Run this to see what you have:
```sql
SELECT 
    p.purchase_id,
    p.invoice_no,
    d.resource_id,
    r.resource_title,
    r.resource_price
FROM purchases p
JOIN downloads d ON p.purchase_id = d.purchase_id
JOIN resources r ON d.resource_id = r.resource_id
ORDER BY p.purchase_date DESC;
```

### 3. Check if order_items is Empty
```sql
SELECT COUNT(*) as existing_order_items FROM order_items;
```

If it shows 0, you need to migrate.

### 4. Migrate Old Purchases
**IMPORTANT: Run this ONLY ONCE!**

```sql
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
    SELECT 1 FROM order_items oi 
    WHERE oi.purchase_id = d.purchase_id 
    AND oi.resource_id = d.resource_id
);
```

### 5. Verify Migration
```sql
SELECT 
    COUNT(*) as total_order_items,
    SUM(price) as total_revenue
FROM order_items;
```

### 6. Check Earnings
Now check:
- Creator earnings page - should show sales
- Admin analytics - should show revenue

## What This Does

- Copies all purchases from `downloads` to `order_items`
- Preserves original purchase dates
- Avoids duplicates (won't add same item twice)
- Calculates earnings correctly (80/20 split)

## After Migration

✅ Old purchases will show in earnings
✅ Admin analytics will show historical revenue
✅ Creator earnings will show all sales
✅ New purchases will continue to work automatically

## If You Have No Old Purchases

If your `downloads` table is empty, you don't need to migrate anything. Just start making new purchases and they'll be tracked automatically!
