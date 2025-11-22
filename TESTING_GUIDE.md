# 🧪 EduMart Testing Guide

## Step-by-Step Testing Instructions

---

## STEP 1: Upload Files to Server

### Option A: Using FTP/SFTP (FileZilla, Cyberduck)
1. Open your FTP client
2. Connect to:
   - **Host:** 169.239.251.102
   - **Port:** 442 (or 22 for SFTP)
   - **Username:** hassan.yakubu
   - **Password:** [your server password]
3. Navigate to `~/public_html/EduMart/`
4. Upload ALL files from your local EduMart folder

### Option B: Using Command Line
```bash
# From your local EduMart folder
scp -P 442 -r * hassan.yakubu@169.239.251.102:~/public_html/EduMart/
```

---

## STEP 2: Import Database

```bash
# SSH into server
ssh hassan.yakubu@169.239.251.102 -p 442

# Login to MySQL
mysql -u hassan.yakubu -p
# Enter password: jonnytest

# Create and import database
CREATE DATABASE IF NOT EXISTS ecommerce_2025A_hassan_yakubu;
USE ecommerce_2025A_hassan_yakubu;
SOURCE ~/public_html/EduMart/db/ecommerce_2025A_hassan_yakubu.sql;

# Verify import
SHOW TABLES;
# You should see: carts, cart_items, categories, creators, customer, downloads, purchases, resources, reviews, settings

# Check sample data
SELECT COUNT(*) FROM customer;
# Should show: 14 users

EXIT;
```

---

## STEP 3: Set File Permissions

```bash
# Still in SSH
cd ~/public_html/EduMart/

# Create upload directories
mkdir -p public/uploads/images
mkdir -p public/uploads/files

# Set permissions
chmod -R 777 public/uploads/
```

---

## STEP 4: Test Database Connection

Open in browser:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_simple.php
```

**Expected Result:**
```
✅ PHP Version: 8.3.6
✅ This page loaded successfully!
✅ index.php
✅ app/config/database.php
✅ settings/db_cred.php
✅ app/views/home/index.php
✅ Database config file exists
✅ Database config loaded
✅ Database connected!
Database: ecommerce_2025A_hassan_yakubu
```

**If you see errors:** Check the error message and fix accordingly.

---

## STEP 5: Test Homepage

Open in browser:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/home/index.php
```

**Expected Result:**
- Yellow and grey design
- "Welcome to EduMart" hero section
- Featured resources displayed
- Categories shown
- Navigation menu working

**Check:**
- [ ] Page loads
- [ ] CSS styling appears (yellow/grey theme)
- [ ] Images load (or placeholders show)
- [ ] Navigation links work

---

## STEP 6: Test User Registration

1. **Go to Register page:**
   ```
   http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/register.php
   ```

2. **Fill in the form:**
   - Full Name: Test User
   - Email: test@example.com
   - Password: test123
   - Country: Ghana
   - City: Accra
   - Contact: 0244123456

3. **Click "Register"**

**Expected Result:**
- Success message: "Registration successful! Please login."
- Redirected to login page

---

## STEP 7: Test Login

1. **Go to Login page:**
   ```
   http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/login.php
   ```

2. **Enter credentials:**
   - Email: test@example.com
   - Password: test123

3. **Click "Login"**

**Expected Result:**
- Successfully logged in
- Redirected to homepage
- Navigation shows: Upload, Cart, Profile, Logout

---

## STEP 8: Test Browse Resources

1. **Go to Browse page:**
   ```
   http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/resources/list.php
   ```

2. **Check features:**
   - [ ] Resources displayed in cards
   - [ ] Search bar works
   - [ ] Category filter works
   - [ ] Price filter works

3. **Click on a resource**

**Expected Result:**
- Resource details page opens
- Shows title, price, description
- Shows rating and reviews
- "Add to Cart" button visible

---

## STEP 9: Test Add to Cart

1. **On resource details page, click "Add to Cart"**

2. **Go to Cart:**
   ```
   http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/cart/view.php
   ```

**Expected Result:**
- Item appears in cart
- Price shown correctly
- Total calculated
- "Proceed to Checkout" button visible

---

## STEP 10: Test Checkout & Payment

1. **Click "Proceed to Checkout"**

2. **On payment page:**
   - Select payment method (MTN MoMo, Vodafone, or AirtelTigo)
   - Enter phone number: 0244123456
   - Enter account name: Test User
   - Click "Pay Securely"

3. **Wait 2 seconds for processing**

**Expected Result:**
- Payment processes (90% success rate)
- Redirected to success page
- Shows payment receipt
- Download buttons available

---

## STEP 11: Test Download

1. **On success page, click "Download" for a resource**

**Expected Result:**
- File downloads to your computer
- Download is logged in database

---

## STEP 12: Test Review System

1. **Go to resource details page**

2. **Scroll to reviews section**

3. **Select rating (1-5 stars)**

4. **Write comment:** "Great resource!"

5. **Click "Submit Review"**

**Expected Result:**
- Review appears on page
- Average rating updates

---

## STEP 13: Test Admin Dashboard

1. **Make yourself admin:**
   ```bash
   ssh hassan.yakubu@169.239.251.102 -p 442
   mysql -u hassan.yakubu -p
   # Password: jonnytest
   
   USE ecommerce_2025A_hassan_yakubu;
   UPDATE customer SET user_role = 1 WHERE customer_email = 'test@example.com';
   EXIT;
   ```

2. **Logout and login again**

3. **Go to Admin Dashboard:**
   ```
   http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/admin/dashboard.php
   ```

**Expected Result:**
- Dashboard shows statistics
- Can view users
- Can view resources
- Can view orders
- Can manage categories

---

## STEP 14: Clean Up Test Files

```bash
ssh hassan.yakubu@169.239.251.102 -p 442
cd ~/public_html/EduMart/
rm test_simple.php test_server.php check_errors.php
```

---

## Complete Testing Checklist

### Basic Functionality
- [ ] Homepage loads with styling
- [ ] Can register new account
- [ ] Can login
- [ ] Can logout
- [ ] Navigation works

### Resources
- [ ] Can browse resources
- [ ] Can search resources
- [ ] Can filter by category
- [ ] Can filter by price
- [ ] Can view resource details

### Shopping
- [ ] Can add to cart
- [ ] Can view cart
- [ ] Can remove from cart
- [ ] Cart total calculates correctly

### Payment
- [ ] Checkout page loads
- [ ] Can select payment method
- [ ] Can enter phone number
- [ ] Payment processes
- [ ] Success page shows

### Downloads
- [ ] Download buttons appear after payment
- [ ] Files download successfully
- [ ] Download is logged

### Reviews
- [ ] Can rate resources
- [ ] Can write reviews
- [ ] Reviews display
- [ ] Average rating calculates

### Admin
- [ ] Admin dashboard accessible
- [ ] Can view users
- [ ] Can view resources
- [ ] Can view orders
- [ ] Can manage categories

### Mobile
- [ ] Site works on mobile
- [ ] Navigation responsive
- [ ] Forms work on mobile
- [ ] Payment works on mobile

---

## Common Issues & Solutions

### Issue: CSS Not Loading
**Solution:** Check browser console for 404 errors. Verify paths include `/~hassan.yakubu/EduMart/`

### Issue: Database Connection Failed
**Solution:** Verify credentials in `settings/db_cred.php`

### Issue: 404 on Pages
**Solution:** Check `.htaccess` has correct RewriteBase

### Issue: Upload Permission Denied
**Solution:** `chmod -R 777 public/uploads/`

### Issue: Payment Always Fails
**Solution:** Normal - 10% failure rate for realism. Try again.

---

## Test URLs Quick Reference

```
Homepage:     http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/home/index.php
Login:        http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/login.php
Register:     http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/register.php
Browse:       http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/resources/list.php
Cart:         http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/cart/view.php
Admin:        http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/admin/dashboard.php
Test:         http://169.239.251.102:442/~hassan.yakubu/EduMart/test_simple.php
```

---

## Success Criteria

Your EduMart is working if:
- ✅ All pages load without errors
- ✅ Can complete full user journey (register → browse → cart → checkout → download)
- ✅ Payment simulation works
- ✅ Admin dashboard accessible
- ✅ Mobile responsive

---

**Follow these steps in order and check off each item!** ✅
