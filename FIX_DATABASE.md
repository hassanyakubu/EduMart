# 🔧 Fix Database Connection

## Problem Found
```
❌ Error: Access denied for user 'root'@'localhost' (using password: NO)
```

The database credentials are incorrect!

---

## Solution: Update Database Credentials

### Step 1: Edit `settings/db_cred.php`

You need to update this file with YOUR MySQL credentials:

```php
<?php
if (!defined("SERVER")) {
    define("SERVER", "localhost");
}

if (!defined("USERNAME")) {
    define("USERNAME", "hassan.yakubu");  // Your MySQL username
}

if (!defined("PASSWD")) {
    define("PASSWD", "YOUR_MYSQL_PASSWORD_HERE");  // ⚠️ ADD YOUR PASSWORD
}

if (!defined("DATABASE")) {
    define("DATABASE", "ecommerce_2025A_hassan_yakubu");
}
?>
```

### Step 2: Find Your MySQL Password

**Option A: You know your password**
- Just add it to the `PASSWD` line

**Option B: You don't know your password**
```bash
# SSH into server
ssh hassan.yakubu@169.239.251.102 -p 442

# Try to login to MySQL
mysql -u hassan.yakubu -p
# Enter your password when prompted

# If you can't remember, you may need to reset it
# Contact your server administrator
```

### Step 3: Verify Database Exists

```bash
# SSH into server
ssh hassan.yakubu@169.239.251.102 -p 442

# Login to MySQL
mysql -u hassan.yakubu -p
# Enter your password

# Check if database exists
SHOW DATABASES;

# If you don't see 'ecommerce_2025A_hassan_yakubu', create it:
CREATE DATABASE IF NOT EXISTS ecommerce_2025A_hassan_yakubu;
USE ecommerce_2025A_hassan_yakubu;
SOURCE ~/public_html/EduMart/db/ecommerce_2025A_hassan_yakubu.sql;
EXIT;
```

---

## Quick Fix Steps

1. **Edit the file on server:**
   ```bash
   ssh hassan.yakubu@169.239.251.102 -p 442
   cd ~/public_html/EduMart/
   nano settings/db_cred.php
   ```

2. **Change these lines:**
   ```php
   define("USERNAME", "hassan.yakubu");  // Your username
   define("PASSWD", "your_actual_password");  // Your password
   ```

3. **Save and exit:**
   - Press `Ctrl + X`
   - Press `Y` to confirm
   - Press `Enter`

4. **Test again:**
   ```
   http://169.239.251.102:442/~hassan.yakubu/EduMart/test_simple.php
   ```

---

## Common MySQL Usernames

Your MySQL username is likely one of these:
- `hassan.yakubu` (most common for shared hosting)
- `hassanyakubu` (without dot)
- `root` (if you have root access)

---

## After Fixing

Once you update the password, refresh:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_simple.php
```

You should see:
```
✅ Database connected!
Database: ecommerce_2025A_hassan_yakubu
```

Then you can access:
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/home/index.php
```

---

## Need Help Finding Password?

1. Check your hosting control panel (cPanel, Plesk, etc.)
2. Check your email for hosting setup details
3. Contact your hosting provider
4. Reset MySQL password through control panel

---

**Once fixed, your site will work perfectly!** 🎯
