# EduMart Payment Fix - Server Quick Guide

## 🌐 Your Server Configuration

**Server URL:** `http://169.239.251.102:442/~hassan.yakubu/EduMart`
**Database:** `ecommerce_2025A_hassan_yakubu`
**Database User:** `hassan.yakubu`

## 🔗 Access URLs

### Diagnostic Tools (Open in Browser):

**Test Payment System:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_payment_flow.php
```

**Debug Quiz Access (Must be logged in):**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/debug_quiz_access_issue.php
```

**Check Existing Data:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/check_existing_data.php
```

**Test Database Connection:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_db_connection.php
```

**Diagnose Quiz Access (Visual):**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/diagnose_quiz_access.php
```

### Main Application URLs:

**Home Page:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart
```

**Login:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/login/login.php
```

**Checkout:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/view/checkout.php
```

**Quizzes:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/view/quizzes.php
```

## 🔧 Quick Fix Steps

### Step 1: Fix Database Schema

**Option A: Using phpMyAdmin**
1. Go to: `http://169.239.251.102:442/phpmyadmin`
2. Select database: `ecommerce_2025A_hassan_yakubu`
3. Click "SQL" tab
4. Copy contents from: `db/fix_purchases_table.sql`
5. Paste and click "Go"

**Option B: Using SSH**
```bash
# SSH into your server
ssh hassan.yakubu@169.239.251.102 -p 442

# Navigate to EduMart directory
cd ~/public_html/EduMart

# Run the SQL fix
mysql -u hassan.yakubu -p ecommerce_2025A_hassan_yakubu < db/fix_purchases_table.sql
```

### Step 2: Test the System

Open in browser:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_payment_flow.php
```

Look for all ✓ checkmarks.

### Step 3: Debug Quiz Access

1. Log in as a student
2. Open:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/debug_quiz_access_issue.php
```
3. Check which of the 3 issues is causing "No quizzes found"

## 📊 Database Access

### Via phpMyAdmin:
```
URL: http://169.239.251.102:442/phpmyadmin
Username: hassan.yakubu
Password: [your password]
Database: ecommerce_2025A_hassan_yakubu
```

### Via SSH/Command Line:
```bash
mysql -u hassan.yakubu -p ecommerce_2025A_hassan_yakubu
```

## 🔍 Common Queries for Your Server

### Check Purchases:
```sql
SELECT purchase_id, customer_id, invoice_no, purchase_date, order_status
FROM purchases
ORDER BY purchase_id DESC
LIMIT 10;
```

### Check Order Items:
```sql
SELECT oi.order_item_id, oi.purchase_id, r.resource_title, oi.price
FROM order_items oi
JOIN resources r ON oi.resource_id = r.resource_id
ORDER BY oi.order_item_id DESC
LIMIT 10;
```

### Check Orphaned Purchases:
```sql
SELECT p.purchase_id, p.customer_id, c.customer_name, p.invoice_no
FROM purchases p
JOIN customer c ON p.customer_id = c.customer_id
LEFT JOIN order_items oi ON p.purchase_id = oi.purchase_id
WHERE oi.order_item_id IS NULL;
```

### Check Published Quizzes:
```sql
SELECT quiz_id, quiz_title, category_id, is_published
FROM quizzes
WHERE is_published = 1;
```

### Check Student Quiz Access:
```sql
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
);
```

## 📝 Server File Paths

All files are located in:
```
/home/hassan.yakubu/public_html/EduMart/
```

**Controller Files:**
- `/home/hassan.yakubu/public_html/EduMart/controllers/order_controller.php`
- `/home/hassan.yakubu/public_html/EduMart/controllers/cart_controller.php`

**Database Fix:**
- `/home/hassan.yakubu/public_html/EduMart/db/fix_purchases_table.sql`

**Test Files:**
- `/home/hassan.yakubu/public_html/EduMart/test_payment_flow.php`
- `/home/hassan.yakubu/public_html/EduMart/debug_quiz_access_issue.php`

## 🚀 Testing Workflow

### 1. Test Database Connection:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_db_connection.php
```
Should show: "✓ Connected successfully!"

### 2. Test Payment System:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_payment_flow.php
```
Should show all checks passing.

### 3. Make Test Payment:
1. Log in as student
2. Add items to cart
3. Go to checkout
4. Complete payment with Paystack test credentials

### 4. Verify in Database:
```sql
-- Check latest purchase
SELECT * FROM purchases ORDER BY purchase_id DESC LIMIT 1;

-- Check order items
SELECT * FROM order_items ORDER BY order_item_id DESC LIMIT 5;

-- Check payment record
SELECT * FROM payments ORDER BY payment_id DESC LIMIT 1;
```

### 5. Check Quiz Access:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/debug_quiz_access_issue.php
```
Should show quizzes user can access.

## 🐛 Troubleshooting

### Issue: "Access denied for user"
**Check:**
- Is MySQL running on server?
- Are credentials in `settings/db_cred.php` correct?
- Can you access phpMyAdmin?

### Issue: "No quizzes found"
**Debug:**
1. Open: `http://169.239.251.102:442/~hassan.yakubu/EduMart/debug_quiz_access_issue.php`
2. Check which of 3 issues:
   - No published quizzes?
   - No purchases?
   - No order_items?

### Issue: "Orders not being created"
**Check:**
1. Controller files exist in `/controllers/` directory
2. PHP error log: `/var/log/apache2/error.log` or check phpMyAdmin
3. Run: `http://169.239.251.102:442/~hassan.yakubu/EduMart/test_payment_flow.php`

## 📞 Quick Commands

### SSH into Server:
```bash
ssh hassan.yakubu@169.239.251.102 -p 442
```

### Navigate to EduMart:
```bash
cd ~/public_html/EduMart
```

### Check PHP Error Log:
```bash
tail -f /var/log/apache2/error.log
# or
tail -f ~/public_html/EduMart/error_log
```

### Run SQL Fix:
```bash
mysql -u hassan.yakubu -p ecommerce_2025A_hassan_yakubu < db/fix_purchases_table.sql
```

### Check File Permissions:
```bash
ls -la controllers/
ls -la db/
```

## ✅ Success Checklist

- [ ] Database fix applied (invoice_no is VARCHAR, payments table exists)
- [ ] Test payment flow shows all ✓ checks
- [ ] Test payment creates records in all 4 tables
- [ ] Students can access quizzes after purchase
- [ ] Analytics show sales and earnings
- [ ] No errors in error log

## 📚 Documentation Files

All documentation is in your EduMart directory:
- `START_HERE.md` - Main entry point
- `QUICK_START_GUIDE.md` - Quick implementation
- `SERVER_QUICK_GUIDE.md` - This file (server-specific)
- `QUIZ_ACCESS_TROUBLESHOOTING.md` - Quiz access debugging
- `PREVIOUS_PAYMENTS_GUIDE.md` - About old payments
- `CHECKLIST.md` - Complete verification checklist

## 🎯 Most Important URLs

**For Testing:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_payment_flow.php
```

**For Quiz Debug:**
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/debug_quiz_access_issue.php
```

**For Database:**
```
http://169.239.251.102:442/phpmyadmin
```

---

**Need help?** Run the diagnostic tools above to identify the exact issue!
