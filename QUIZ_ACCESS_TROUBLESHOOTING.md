# Quiz Access Troubleshooting Guide

## Debug Message: "No quizzes found"

This message appears when the quiz access query returns 0 results. There are 3 possible causes:

## 🔍 How to Diagnose

### Step 1: Run the Debug Script

Open in your browser:
```
http://localhost/EduMart/debug_quiz_access_issue.php
```

This will check:
1. ✓ Are there published quizzes?
2. ✓ Does the user have purchases?
3. ✓ Does the user have order_items? (CRITICAL!)
4. ✓ Do the categories match?
5. ✓ What does the actual query return?

### Step 2: Check Manually in Database

Open phpMyAdmin and run these queries:

#### Check 1: Published Quizzes
```sql
SELECT quiz_id, quiz_title, category_id, is_published
FROM quizzes
WHERE is_published = 1;
```

**Expected:** Should return at least 1 quiz
**If empty:** No published quizzes exist - creators need to publish them

#### Check 2: User's Purchases
```sql
-- Replace 21 with actual customer_id
SELECT purchase_id, customer_id, invoice_no, purchase_date
FROM purchases
WHERE customer_id = 21;
```

**Expected:** Should return at least 1 purchase
**If empty:** User hasn't made any purchases

#### Check 3: User's Order Items (MOST IMPORTANT!)
```sql
-- Replace 21 with actual customer_id
SELECT oi.order_item_id, oi.purchase_id, r.resource_title, r.cat_id, c.cat_name
FROM order_items oi
JOIN purchases p ON oi.purchase_id = p.purchase_id
JOIN resources r ON oi.resource_id = r.resource_id
JOIN categories c ON r.cat_id = c.cat_id
WHERE p.customer_id = 21;
```

**Expected:** Should return at least 1 order_item
**If empty:** THIS IS THE PROBLEM! Purchases have no order_items

#### Check 4: Category Match
```sql
-- Replace 21 with actual customer_id
-- This shows which categories user purchased
SELECT DISTINCT c.cat_id, c.cat_name
FROM order_items oi
JOIN purchases p ON oi.purchase_id = p.purchase_id
JOIN resources r ON oi.resource_id = r.resource_id
JOIN categories c ON r.cat_id = c.cat_id
WHERE p.customer_id = 21;

-- This shows which categories have published quizzes
SELECT DISTINCT category_id, cat_name
FROM quizzes q
JOIN categories c ON q.category_id = c.cat_id
WHERE is_published = 1;
```

**Expected:** At least one category should appear in both results
**If no match:** User needs to purchase resources in categories that have quizzes

## 🎯 Common Issues & Solutions

### Issue 1: No Published Quizzes

**Symptoms:**
- Query returns 0 quizzes
- `is_published = 0` for all quizzes

**Solution:**
1. Log in as the quiz creator
2. Go to quiz management page
3. Click "Publish" on quizzes
4. Verify `is_published = 1` in database

**SQL to publish a quiz:**
```sql
UPDATE quizzes SET is_published = 1 WHERE quiz_id = 6;
```

### Issue 2: User Has No Purchases

**Symptoms:**
- User is logged in but hasn't bought anything
- `purchases` table has no records for this customer_id

**Solution:**
- User needs to make a purchase first
- Add items to cart and complete checkout

### Issue 3: Purchases Have No Order Items (MOST COMMON!)

**Symptoms:**
- User has purchases in database
- But `order_items` table has no records for these purchases
- This is the old payment system bug!

**Root Cause:**
The old payment system didn't create order_items when processing payments.

**Solution A: Apply the Fix and Make New Purchase**
1. Apply database fix: `db/fix_purchases_table.sql`
2. User makes a new purchase
3. New purchase will have order_items
4. User can access quizzes for new purchase

**Solution B: Manually Add Order Items for Old Purchases**

If you know what the user purchased:

```sql
-- Example: User purchased resource 3 in purchase 1
INSERT INTO order_items (purchase_id, resource_id, qty, price)
VALUES (1, 3, 1, 15.00);

-- Also add download access
INSERT INTO downloads (customer_id, resource_id, purchase_id)
SELECT customer_id, 3, 1 FROM purchases WHERE purchase_id = 1;
```

**Solution C: Refund and Repurchase**
1. Refund the old purchase
2. User makes a new purchase with the fixed system
3. New purchase will work correctly

### Issue 4: Category Mismatch

**Symptoms:**
- User has purchases with order_items
- But purchased categories don't have published quizzes

**Example:**
- User purchased "BECE English" (category 3)
- But only "WASSCE Science" (category 2) has published quizzes

**Solution A: Publish Quizzes in User's Categories**
1. Create/publish quizzes for categories user purchased
2. User will then see those quizzes

**Solution B: User Purchases More Categories**
1. User purchases resources in categories that have quizzes
2. User will then see those quizzes

## 🔧 Quick Fixes

### Fix 1: Publish All Quizzes
```sql
UPDATE quizzes SET is_published = 1;
```

### Fix 2: Check Which Purchases Need Order Items
```sql
SELECT p.purchase_id, p.customer_id, c.customer_name, p.invoice_no
FROM purchases p
JOIN customer c ON p.customer_id = c.customer_id
LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
WHERE oi.order_item_id IS NULL;
```

### Fix 3: Add Order Items for a Specific Purchase

If you know purchase 1 bought resource 3:
```sql
-- Add order item
INSERT INTO order_items (purchase_id, resource_id, qty, price)
SELECT 1, 3, 1, resource_price FROM resources WHERE resource_id = 3;

-- Add download
INSERT INTO downloads (customer_id, resource_id, purchase_id)
SELECT customer_id, 3, 1 FROM purchases WHERE purchase_id = 1;
```

## 📊 Verification Queries

### Verify Quiz Access Works
```sql
-- This is the exact query the system uses
-- Replace 21 with actual customer_id
SELECT DISTINCT q.quiz_id, q.quiz_title, cat.cat_name
FROM quizzes q
JOIN categories cat ON q.category_id = cat.cat_id
WHERE q.is_published = 1
AND q.category_id IN (
    SELECT DISTINCT r.cat_id
    FROM order_items oi
    JOIN resources r ON oi.resource_id = r.resource_id
    JOIN purchases p ON oi.purchase_id = p.purchase_id
    WHERE p.customer_id = 21
)
ORDER BY q.created_at DESC;
```

**Expected:** Should return quizzes user can access
**If empty:** One of the 3 issues exists

### Check All Students' Quiz Access
```sql
SELECT 
    c.customer_id,
    c.customer_name,
    COUNT(DISTINCT oi.order_item_id) as items_purchased,
    GROUP_CONCAT(DISTINCT cat.cat_name) as accessible_categories
FROM customer c
LEFT JOIN purchases p ON c.customer_id = p.customer_id
LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
LEFT JOIN resources r ON oi.resource_id = r.resource_id
LEFT JOIN categories cat ON r.cat_id = cat.cat_id
WHERE c.user_type = 'student'
GROUP BY c.customer_id, c.customer_name
ORDER BY c.customer_id;
```

## 🎯 Step-by-Step Troubleshooting

1. **Run debug script:**
   ```
   http://localhost/EduMart/debug_quiz_access_issue.php
   ```

2. **Identify the issue:**
   - No published quizzes? → Publish them
   - No purchases? → User needs to buy
   - No order_items? → Apply fix and repurchase
   - Category mismatch? → Publish quizzes or buy more

3. **Apply the fix:**
   - Run `db/fix_purchases_table.sql`
   - Test with new purchase
   - Manually fix old purchases if needed

4. **Verify it works:**
   - User should see quizzes
   - Run verification queries
   - Check debug script shows success

## 📞 Still Not Working?

If quizzes still don't show after fixing:

1. **Clear browser cache**
2. **Log out and log back in**
3. **Check PHP error log:**
   ```bash
   tail -f /Applications/XAMPP/xamppfiles/logs/php_error_log
   ```
4. **Check browser console** for JavaScript errors
5. **Verify database changes** were applied
6. **Run debug script** again to see current state

## ✅ Success Indicators

Quizzes are working when:
- ✓ Debug script shows quizzes in all checks
- ✓ Verification query returns results
- ✓ User can see and take quizzes
- ✓ No errors in PHP log
- ✓ No errors in browser console

## 📝 Summary

**Most Common Issue:** Purchases have no order_items (old payment system bug)

**Quick Solution:**
1. Apply database fix
2. Make new purchase
3. Verify order_items are created
4. User can access quizzes

**For Old Purchases:**
- Manually add order_items if you know what was bought
- Or ask users to repurchase with discount/refund
