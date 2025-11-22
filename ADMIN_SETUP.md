# Admin Setup Guide - EduMart

## Single Admin Configuration

**Admin Email:** yhassan677@gmail.com  
**Admin Role:** user_role = 1  
**Admin Type:** user_type = 'creator' (can upload and manage)

---

## Step 1: Run Database Migrations

Execute these SQL commands in order:

### 1.1 Add user_type column (if not done already)
```sql
ALTER TABLE `customer` 
ADD COLUMN `user_type` ENUM('student', 'creator') DEFAULT 'student' AFTER `user_role`;

UPDATE `customer` SET `user_type` = 'student' WHERE `user_type` IS NULL;
```

### 1.2 Set Hassan Yakubu as the only admin
```sql
-- Reset all users to regular users first
UPDATE `customer` SET `user_role` = 2;

-- Set yhassan677@gmail.com as admin
UPDATE `customer` 
SET `user_role` = 1, `user_type` = 'creator' 
WHERE `customer_email` = 'yhassan677@gmail.com';
```

### 1.3 Verify admin setup
```sql
SELECT customer_id, customer_name, customer_email, user_role, user_type 
FROM customer 
WHERE customer_email = 'yhassan677@gmail.com';
```

Expected result:
```
user_role: 1
user_type: creator
```

---

## Step 2: Create Admin Account (if doesn't exist)

If you haven't registered yet:

1. Go to registration page
2. Select "✍️ Creator" as user type
3. Fill in your details:
   - Name: Hassan Yakubu
   - Email: yhassan677@gmail.com
   - Password: [Your secure password]
   - Country: Ghana
   - City: [Your city]
   - Contact: [Your number]
4. Complete registration
5. Then run the SQL in Step 1.2 to upgrade to admin

---

## Security Features

### 1. Registration Protection
- All new registrations are hardcoded to `user_role = 2` (regular user)
- No one can register as admin through the form
- Admin role can only be set via direct database update

### 2. Profile Update Protection
- Users cannot change their `user_role` or `user_type` through profile updates
- Only name, email, country, city, and contact can be updated
- Prevents privilege escalation attacks

### 3. Admin-Only Access
The following features require `user_role = 1`:
- Admin Dashboard
- User Management
- Category Management
- Order Management
- System Settings

---

## User Role Hierarchy

### Level 1: Admin (user_role = 1)
**Email:** yhassan677@gmail.com  
**Permissions:**
- ✅ Full system access
- ✅ Manage all users
- ✅ Manage categories and creators
- ✅ Upload resources
- ✅ View all orders and statistics
- ✅ Access admin dashboard
- ✅ Everything creators and students can do

### Level 2: Creator (user_role = 2, user_type = 'creator')
**Permissions:**
- ✅ Upload resources
- ✅ Download purchased resources
- ✅ Browse and purchase resources
- ✅ Manage their profile
- ❌ No admin access

### Level 3: Student (user_role = 2, user_type = 'student')
**Permissions:**
- ✅ Browse resources
- ✅ Purchase resources
- ✅ Download purchased resources
- ✅ Leave reviews
- ✅ Manage their profile
- ❌ Cannot upload resources
- ❌ No admin access

---

## Admin Dashboard Access

After logging in as admin, you'll see:

**Navigation:**
- Home
- Browse Resources
- **Admin** ← Admin dashboard
- **Upload** ← Upload resources
- Cart
- Profile
- Logout

**Admin Dashboard Features:**
- User management
- Resource management
- Category management
- Order management
- System statistics
- Settings

---

## Important Notes

### 1. Single Admin Policy
- Only yhassan677@gmail.com should have `user_role = 1`
- If you need to add another admin, manually update the database
- Never set multiple admins unless absolutely necessary

### 2. Database Backups
- Always backup database before making role changes
- Keep a record of admin credentials securely

### 3. Password Security
- Use a strong password for admin account
- Change password regularly
- Never share admin credentials

### 4. Testing Other Roles
To test creator or student features:
- Create separate test accounts
- Do not change your admin account role
- Use different email addresses for testing

---

## Troubleshooting

### Problem: Can't access admin dashboard
**Solution:**
```sql
-- Verify your role
SELECT user_role, user_type FROM customer WHERE customer_email = 'yhassan677@gmail.com';

-- If not admin, fix it
UPDATE customer SET user_role = 1 WHERE customer_email = 'yhassan677@gmail.com';
```

### Problem: Other users showing as admin
**Solution:**
```sql
-- Reset all users except admin
UPDATE customer 
SET user_role = 2 
WHERE customer_email != 'yhassan677@gmail.com';
```

### Problem: Admin can't upload
**Solution:**
```sql
-- Ensure admin has creator type
UPDATE customer 
SET user_type = 'creator' 
WHERE customer_email = 'yhassan677@gmail.com';
```

---

## Quick Reference SQL

### Check all admins:
```sql
SELECT customer_id, customer_name, customer_email, user_role 
FROM customer 
WHERE user_role = 1;
```

### Check all creators:
```sql
SELECT customer_id, customer_name, customer_email, user_type 
FROM customer 
WHERE user_type = 'creator';
```

### Reset a user to student:
```sql
UPDATE customer 
SET user_role = 2, user_type = 'student' 
WHERE customer_email = 'user@example.com';
```

### Make a user a creator (not admin):
```sql
UPDATE customer 
SET user_type = 'creator' 
WHERE customer_email = 'creator@example.com';
```

---

## Files with Admin Checks

1. **app/models/user_model.php**
   - Line 13: `$user_role = 2;` - Hardcoded for registration

2. **app/views/layouts/header.php**
   - Admin menu check: `$_SESSION['user_role'] == 1`

3. **app/controllers/auth_controller.php**
   - Admin redirect: `if ($user['user_role'] == 1)`

4. **app/views/admin/dashboard.php**
   - Admin access check at top of file

---

## Maintenance

### Monthly Tasks:
- [ ] Review user list for suspicious accounts
- [ ] Verify only one admin exists
- [ ] Check for unauthorized role changes
- [ ] Backup database

### After System Updates:
- [ ] Verify admin access still works
- [ ] Test admin dashboard features
- [ ] Confirm role restrictions are enforced

---

## Contact

**Admin:** Hassan Yakubu  
**Email:** yhassan677@gmail.com  
**Phone:** 0204200934
