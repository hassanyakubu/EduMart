# 🚀 EduMart Deployment Guide

## Server Details
- **URL**: http://169.239.251.102:442/~hassan.yakubu
- **Type**: Shared hosting with subdirectory
- **Base Path**: `/~hassan.yakubu`

---

## Step 1: Upload Files to Server

### Option A: Using FTP/SFTP (FileZilla, Cyberduck)
1. Connect to your server:
   - Host: `169.239.251.102`
   - Port: `442` or `22` (for SFTP)
   - Username: `hassan.yakubu`
   - Password: [your password]

2. Navigate to your public_html or www directory (usually `~/public_html/`)

3. Upload all EduMart files to the server

### Option B: Using Command Line (SSH)
```bash
# Connect to server
ssh hassan.yakubu@169.239.251.102 -p 442

# Navigate to web directory
cd ~/public_html/

# Upload files (from your local machine)
scp -P 442 -r /path/to/EduMart/* hassan.yakubu@169.239.251.102:~/public_html/
```

---

## Step 2: Configure Database

### 2.1 Create Database
```bash
# SSH into server
ssh hassan.yakubu@169.239.251.102 -p 442

# Login to MySQL
mysql -u hassan.yakubu -p

# Create database and import
CREATE DATABASE IF NOT EXISTS ecommerce_2025A_hassan_yakubu;
USE ecommerce_2025A_hassan_yakubu;
SOURCE ~/public_html/db/ecommerce_2025A_hassan_yakubu.sql;
EXIT;
```

**Note**: There is only ONE database file to import.

### 2.2 Update Database Credentials
Edit `settings/db_cred.php`:
```php
<?php
if (!defined("SERVER")) {
    define("SERVER", "localhost");
}

if (!defined("USERNAME")) {
    define("USERNAME", "hassan.yakubu");  // Your MySQL username
}

if (!defined("PASSWD")) {
    define("PASSWD", "your_mysql_password");  // Your MySQL password
}

if (!defined("DATABASE")) {
    define("DATABASE", "ecommerce_2025A_hassan_yakubu");
}
?>
```

---

## Step 3: Configure Base URL

### 3.1 Update .htaccess
Create/edit `.htaccess` in root directory:
```apache
# Set base directory
RewriteEngine On
RewriteBase /~hassan.yakubu/

# Redirect to index.php if file doesn't exist
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L]

# Prevent directory browsing
Options -Indexes

# Set default charset
AddDefaultCharset UTF-8

# PHP Settings
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
```

### 3.2 Update index.php
Edit `index.php`:
```php
<?php
/**
 * EduMart - Digital Learning Resources Marketplace
 * Main Entry Point
 */

// Define base URL
define('BASE_URL', 'http://169.239.251.102:442/~hassan.yakubu');

// Redirect to home page
header('Location: ' . BASE_URL . '/app/views/home/index.php');
exit;
?>
```

---

## Step 4: Set File Permissions

```bash
# SSH into server
ssh hassan.yakubu@169.239.251.102 -p 442

# Navigate to project
cd ~/public_html/

# Set permissions for upload directories
chmod -R 777 public/uploads/
mkdir -p public/uploads/images
mkdir -p public/uploads/files
chmod -R 777 public/uploads/images
chmod -R 777 public/uploads/files

# Set permissions for other directories
chmod -R 755 app/
chmod -R 755 public/
chmod -R 755 settings/
chmod 644 .htaccess
```

---

## Step 5: Update File Paths in Code

### 5.1 Update Header Links
Edit `app/views/layouts/header.php`:

Find:
```php
<link rel="stylesheet" href="/public/assets/css/styles.css">
<link rel="stylesheet" href="/public/assets/css/animations.css">
```

Replace with:
```php
<link rel="stylesheet" href="/~hassan.yakubu/public/assets/css/styles.css">
<link rel="stylesheet" href="/~hassan.yakubu/public/assets/css/animations.css">
```

Find:
```php
<a href="/app/views/home/index.php" class="logo">EduMart</a>
```

Replace with:
```php
<a href="/~hassan.yakubu/app/views/home/index.php" class="logo">EduMart</a>
```

Update all navigation links:
```php
<li><a href="/~hassan.yakubu/app/views/home/index.php">Home</a></li>
<li><a href="/~hassan.yakubu/app/views/resources/list.php">Browse Resources</a></li>
<li><a href="/~hassan.yakubu/app/views/cart/view.php">Cart</a></li>
<li><a href="/~hassan.yakubu/app/views/profile/dashboard.php">Profile</a></li>
<li><a href="/~hassan.yakubu/app/views/auth/login.php">Login</a></li>
<li><a href="/~hassan.yakubu/app/views/auth/register.php">Sign Up</a></li>
```

### 5.2 Update Footer Links
Edit `app/views/layouts/footer.php`:
```php
<script src="/~hassan.yakubu/public/assets/js/main.js"></script>
```

### 5.3 Create Config File for Base URL
Create `app/config/config.php`:
```php
<?php
// Base URL Configuration
define('BASE_URL', 'http://169.239.251.102:442/~hassan.yakubu');
define('BASE_PATH', '/~hassan.yakubu');

// Helper function to generate URLs
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Helper function for assets
function asset($path = '') {
    return BASE_URL . '/public/assets/' . ltrim($path, '/');
}
?>
```

---

## Step 6: Test the Application

### 6.1 Access Homepage
Open browser and go to:
```
http://169.239.251.102:442/~hassan.yakubu
```

You should be redirected to:
```
http://169.239.251.102:442/~hassan.yakubu/app/views/home/index.php
```

### 6.2 Test Database Connection
Create a test file `test_db.php` in root:
```php
<?php
require_once 'settings/db_cred.php';

$conn = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "✅ Database connected successfully!<br>";
echo "Server: " . SERVER . "<br>";
echo "Database: " . DATABASE . "<br>";

$conn->close();
?>
```

Access: `http://169.239.251.102:442/~hassan.yakubu/test_db.php`

**Delete this file after testing!**

### 6.3 Test Pages
1. **Homepage**: `http://169.239.251.102:442/~hassan.yakubu/app/views/home/index.php`
2. **Login**: `http://169.239.251.102:442/~hassan.yakubu/app/views/auth/login.php`
3. **Register**: `http://169.239.251.102:442/~hassan.yakubu/app/views/auth/register.php`
4. **Browse**: `http://169.239.251.102:442/~hassan.yakubu/app/views/resources/list.php`

---

## Step 7: Create Admin Account

### Option A: Via Database
```bash
# SSH into server
ssh hassan.yakubu@169.239.251.102 -p 442

# Login to MySQL
mysql -u hassan.yakubu -p ecommerce_2025A_hassan_yakubu

# Update user role to admin
UPDATE customer SET user_role = 1 WHERE customer_email = 'your@email.com';

# Exit
EXIT;
```

### Option B: Register then Update
1. Register a new account via the website
2. Then run the SQL command above to make it admin

---

## Step 8: Test Full User Journey

### 8.1 Register Account
1. Go to: `http://169.239.251.102:442/~hassan.yakubu/app/views/auth/register.php`
2. Fill in details
3. Submit

### 8.2 Login
1. Go to: `http://169.239.251.102:442/~hassan.yakubu/app/views/auth/login.php`
2. Enter credentials
3. Login

### 8.3 Browse Resources
1. Go to: `http://169.239.251.102:442/~hassan.yakubu/app/views/resources/list.php`
2. View available resources

### 8.4 Add to Cart
1. Click "Add to Cart" on any resource
2. Go to cart: `http://169.239.251.102:442/~hassan.yakubu/app/views/cart/view.php`

### 8.5 Checkout
1. Click "Proceed to Checkout"
2. Select payment method (MTN MoMo, Vodafone, or AirtelTigo)
3. Enter phone number (any 10 digits, e.g., 0244123456)
4. Click "Pay Securely"
5. Wait for processing (2 seconds)
6. Should redirect to success page

### 8.6 Download
1. On success page, click "Download" for each resource
2. Files should download

### 8.7 Leave Review
1. Go to resource details page
2. Select rating (1-5 stars)
3. Write comment
4. Submit review

---

## Step 9: Test Admin Dashboard

1. Login with admin account
2. Go to: `http://169.239.251.102:442/~hassan.yakubu/app/views/admin/dashboard.php`
3. Test:
   - View statistics
   - Manage users
   - Manage resources
   - Manage categories
   - View orders

---

## Troubleshooting

### Issue: CSS Not Loading
**Solution**: Check paths in header.php, ensure they include `/~hassan.yakubu/`

### Issue: Images Not Showing
**Solution**: 
```bash
chmod -R 777 public/uploads/
```

### Issue: Database Connection Error
**Solution**: 
1. Check `settings/db_cred.php` credentials
2. Verify database exists: `mysql -u hassan.yakubu -p -e "SHOW DATABASES;"`

### Issue: 404 Errors
**Solution**: 
1. Check `.htaccess` has correct RewriteBase
2. Ensure all links include `/~hassan.yakubu/` prefix

### Issue: Permission Denied
**Solution**:
```bash
chmod -R 755 app/
chmod -R 777 public/uploads/
```

### Issue: PHP Errors Showing
**Solution**: Edit `php.ini` or add to `.htaccess`:
```apache
php_flag display_errors off
php_flag log_errors on
```

---

## Quick Commands Reference

### SSH Connection
```bash
ssh hassan.yakubu@169.239.251.102 -p 442
```

### MySQL Connection
```bash
mysql -u hassan.yakubu -p ecommerce_2025A_hassan_yakubu
```

### Check Permissions
```bash
ls -la ~/public_html/
```

### View Error Logs
```bash
tail -f ~/public_html/error_log
# or
tail -f /var/log/apache2/error.log
```

### Restart Apache (if you have access)
```bash
sudo service apache2 restart
```

---

## Testing Checklist

- [ ] Homepage loads
- [ ] CSS and images display correctly
- [ ] Can register new account
- [ ] Can login
- [ ] Can browse resources
- [ ] Can search resources
- [ ] Can add to cart
- [ ] Cart displays correctly
- [ ] Can proceed to checkout
- [ ] Payment form loads
- [ ] Can complete payment (simulated)
- [ ] Success page shows
- [ ] Can download resources
- [ ] Can leave reviews
- [ ] Admin dashboard accessible
- [ ] Admin can manage users
- [ ] Admin can manage resources
- [ ] Admin can view orders

---

## Important URLs

- **Homepage**: http://169.239.251.102:442/~hassan.yakubu
- **Login**: http://169.239.251.102:442/~hassan.yakubu/app/views/auth/login.php
- **Register**: http://169.239.251.102:442/~hassan.yakubu/app/views/auth/register.php
- **Browse**: http://169.239.251.102:442/~hassan.yakubu/app/views/resources/list.php
- **Cart**: http://169.239.251.102:442/~hassan.yakubu/app/views/cart/view.php
- **Admin**: http://169.239.251.102:442/~hassan.yakubu/app/views/admin/dashboard.php

---

## Need Help?

1. Check error logs
2. Verify file permissions
3. Test database connection
4. Check `.htaccess` configuration
5. Verify all paths include `/~hassan.yakubu/`

---

**Status**: Ready for deployment
**Server**: http://169.239.251.102:442/~hassan.yakubu
**Next**: Follow steps 1-9 above
