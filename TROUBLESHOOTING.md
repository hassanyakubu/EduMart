# 🔧 EduMart Troubleshooting Guide

## Quick Fix for 404 Error

### Problem: `index.php:1 Failed to load resource: 404 Not Found`

This means the server can't find the file. Here's how to fix it:

---

## Solution 1: Use Test File

1. **Upload `test_server.php` to your server**

2. **Access it in browser:**
   ```
   http://169.239.251.102:442/~hassan.yakubu/test_server.php
   ```

3. **Check all tests** - it will show you:
   - PHP version
   - Database connection
   - File permissions
   - Missing files
   - Direct links to test

4. **DELETE the test file after testing!**

---

## Solution 2: Access Homepage Directly

Instead of going to the root, go directly to the homepage:

```
http://169.239.251.102:442/~hassan.yakubu/app/views/home/index.php
```

---

## Solution 3: Check File Upload

Make sure ALL files are uploaded to the correct location:

```bash
# SSH into server
ssh hassan.yakubu@169.239.251.102 -p 442

# Check if files exist
cd ~/public_html/
ls -la

# You should see:
# - app/
# - public/
# - settings/
# - db/
# - index.php
# - test_server.php
```

---

## Solution 4: Fix .htaccess

Make sure `.htaccess` exists in root with correct content:

```apache
RewriteEngine On
RewriteBase /~hassan.yakubu/

# Prevent directory browsing
Options -Indexes

# Set default charset
AddDefaultCharset UTF-8
```

---

## Solution 5: Check Permissions

```bash
# SSH into server
ssh hassan.yakubu@169.239.251.102 -p 442

cd ~/public_html/

# Set correct permissions
chmod 644 index.php
chmod 644 .htaccess
chmod -R 755 app/
chmod -R 755 public/
chmod -R 777 public/uploads/
```

---

## Common Issues & Fixes

### Issue 1: CSS Not Loading
**Symptom**: Page loads but no styling

**Fix**: Check paths in `app/views/layouts/header.php`
```php
<link rel="stylesheet" href="/~hassan.yakubu/public/assets/css/styles.css">
```

### Issue 2: Database Connection Error
**Symptom**: "Database connection failed"

**Fix**: Update `settings/db_cred.php`
```php
define("USERNAME", "hassan.yakubu");
define("PASSWD", "your_mysql_password");
define("DATABASE", "ecommerce_2025A_hassan_yakubu");
```

### Issue 3: Upload Directory Error
**Symptom**: "Permission denied" when uploading

**Fix**:
```bash
chmod -R 777 public/uploads/
mkdir -p public/uploads/images
mkdir -p public/uploads/files
```

### Issue 4: 500 Internal Server Error
**Symptom**: White page or 500 error

**Fix**: Check error logs
```bash
tail -f ~/public_html/error_log
# or
tail -f /var/log/apache2/error.log
```

### Issue 5: Links Not Working
**Symptom**: Clicking links gives 404

**Fix**: All links should include `/~hassan.yakubu/`
```php
// Correct
<a href="/~hassan.yakubu/app/views/auth/login.php">Login</a>

// Wrong
<a href="/app/views/auth/login.php">Login</a>
```

---

## Testing Checklist

After fixing, test these URLs:

1. **Test File**:
   ```
   http://169.239.251.102:442/~hassan.yakubu/test_server.php
   ```

2. **Homepage**:
   ```
   http://169.239.251.102:442/~hassan.yakubu/app/views/home/index.php
   ```

3. **Login**:
   ```
   http://169.239.251.102:442/~hassan.yakubu/app/views/auth/login.php
   ```

4. **Register**:
   ```
   http://169.239.251.102:442/~hassan.yakubu/app/views/auth/register.php
   ```

5. **Browse**:
   ```
   http://169.239.251.102:442/~hassan.yakubu/app/views/resources/list.php
   ```

---

## Quick Commands

### Check Files Exist
```bash
ssh hassan.yakubu@169.239.251.102 -p 442
cd ~/public_html/
ls -la
```

### Check Database
```bash
mysql -u hassan.yakubu -p
SHOW DATABASES;
USE ecommerce_2025A_hassan_yakubu;
SHOW TABLES;
EXIT;
```

### View Error Logs
```bash
tail -50 ~/public_html/error_log
```

### Fix Permissions
```bash
cd ~/public_html/
chmod -R 755 app/
chmod -R 777 public/uploads/
```

---

## Still Not Working?

1. **Use test_server.php** - It will tell you exactly what's wrong
2. **Check error logs** - They show the actual error
3. **Verify file upload** - Make sure all files are on the server
4. **Test database** - Make sure it's imported correctly

---

## Contact Info

If you need help:
1. Run `test_server.php` and screenshot the results
2. Check error logs: `tail -50 ~/public_html/error_log`
3. Verify all files uploaded: `ls -la ~/public_html/`

---

**Remember**: DELETE `test_server.php` after testing for security!
