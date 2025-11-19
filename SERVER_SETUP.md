# 🚀 EduMart Server Setup - Correct Paths

## Your Server Structure

```
~/public_html/
├── project1/
├── project2/
└── EduMart/          ← Your EduMart project
    ├── app/
    ├── public/
    ├── settings/
    ├── db/
    ├── index.php
    └── test_server.php
```

## Correct URLs

### Main URL
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/
```

### Test File
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_server.php
```

### Homepage
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/home/index.php
```

### Login
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/login.php
```

### Register
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/auth/register.php
```

### Browse Resources
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/resources/list.php
```

### Admin Dashboard
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/admin/dashboard.php
```

---

## Quick Setup Steps

### 1. Upload Files
Upload all EduMart files to:
```
~/public_html/EduMart/
```

### 2. Import Database
```bash
ssh hassan.yakubu@169.239.251.102 -p 442

mysql -u hassan.yakubu -p
CREATE DATABASE IF NOT EXISTS ecommerce_2025A_hassan_yakubu;
USE ecommerce_2025A_hassan_yakubu;
SOURCE ~/public_html/EduMart/db/ecommerce_2025A_hassan_yakubu.sql;
EXIT;
```

### 3. Set Permissions
```bash
cd ~/public_html/EduMart/
chmod -R 777 public/uploads/
mkdir -p public/uploads/images
mkdir -p public/uploads/files
```

### 4. Update Database Credentials
Edit `settings/db_cred.php`:
```php
define("USERNAME", "hassan.yakubu");
define("PASSWD", "your_mysql_password");
define("DATABASE", "ecommerce_2025A_hassan_yakubu");
```

### 5. Test Your Site
```
http://169.239.251.102:442/~hassan.yakubu/EduMart/test_server.php
```

---

## Files Already Updated

✅ **index.php** - Points to `/~hassan.yakubu/EduMart/`
✅ **app/config/config.php** - Base URL includes `EduMart`
✅ **.htaccess** - RewriteBase set to `/~hassan.yakubu/EduMart/`
✅ **test_server.php** - Auto-detects correct paths

---

## Testing Checklist

- [ ] Upload all files to `~/public_html/EduMart/`
- [ ] Import database
- [ ] Set permissions on uploads folder
- [ ] Update database credentials
- [ ] Access test file: `http://169.239.251.102:442/~hassan.yakubu/EduMart/test_server.php`
- [ ] Test homepage: `http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/home/index.php`
- [ ] Register account
- [ ] Login
- [ ] Test cart and checkout
- [ ] Delete test_server.php

---

## Quick Commands

### SSH Connection
```bash
ssh hassan.yakubu@169.239.251.102 -p 442
```

### Navigate to Project
```bash
cd ~/public_html/EduMart/
```

### Check Files
```bash
ls -la ~/public_html/EduMart/
```

### Set Permissions
```bash
cd ~/public_html/EduMart/
chmod -R 777 public/uploads/
```

### View Error Logs
```bash
tail -50 ~/public_html/EduMart/error_log
```

---

## Important Notes

1. **All URLs must include `/EduMart/`** in the path
2. **Base URL**: `http://169.239.251.102:442/~hassan.yakubu/EduMart`
3. **Files location**: `~/public_html/EduMart/`
4. **Test first**: Use `test_server.php` to verify everything

---

## Your Project Structure on Server

```
~/public_html/EduMart/
├── .htaccess                    (RewriteBase: /~hassan.yakubu/EduMart/)
├── index.php                    (Redirects to homepage)
├── test_server.php              (Test file - DELETE after testing)
├── app/
│   ├── config/
│   │   ├── database.php
│   │   └── config.php           (BASE_URL includes /EduMart)
│   ├── controllers/
│   ├── models/
│   └── views/
├── public/
│   ├── assets/
│   └── uploads/                 (chmod 777)
├── settings/
│   └── db_cred.php              (Update credentials)
└── db/
    └── ecommerce_2025A_hassan_yakubu.sql
```

---

**Ready to test!** 🚀

Access: `http://169.239.251.102:442/~hassan.yakubu/EduMart/test_server.php`
