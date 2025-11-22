# User Roles & Permissions Guide

## User Types in EduMart

### 1. Admin (user_role = 1)
**Full system access**
- Manage all users
- Manage categories and creators
- Upload resources
- View all orders
- Access admin dashboard
- Can do everything creators and students can do

### 2. Creator (user_role = 2, user_type = 'creator')
**Content creators who sell resources**
- ✅ Upload resources
- ✅ Download purchased resources
- ✅ Browse and purchase resources
- ✅ View their sales (future feature)
- ✅ Manage their profile
- ❌ Cannot access admin functions

### 3. Student (user_role = 2, user_type = 'student')
**Regular users who purchase resources**
- ✅ Browse resources
- ✅ Purchase resources
- ✅ Download purchased resources
- ✅ Leave reviews
- ✅ Manage their profile
- ❌ Cannot upload resources
- ❌ Cannot access admin functions

## Registration Process

### Step 1: User selects role
During registration, users choose:
- **🎓 Student** - Browse and purchase resources
- **✍️ Creator** - Upload and sell resources

### Step 2: Complete registration form
- Full Name
- Email
- Password (minimum 6 characters)
- Country (default: Ghana)
- City
- Contact Number

### Step 3: Account created
- User receives confirmation
- Redirected to login page
- Can log in with credentials

## Navigation Menu by User Type

### For Students:
- Home
- Browse Resources
- Cart
- Profile
- Logout

### For Creators:
- Home
- Browse Resources
- **Upload** ← Extra feature
- Cart
- Profile
- Logout

### For Admins:
- Home
- Browse Resources
- **Admin** ← Extra feature
- **Upload** ← Extra feature
- Cart
- Profile
- Logout

## Dashboard Display

### Student Dashboard:
```
Hello, [Name]! 👋
🎓 Student Account

- Total Orders: X
- Downloads: Y
- Profile Information
- Recent Orders
```

### Creator Dashboard:
```
Hello, [Name]! 👋
✍️ Creator Account

- Total Orders: X
- Downloads: Y
- Profile Information
- Recent Orders
```

## Database Schema Update

### Required SQL Migration:
Run this SQL to add user_type support:

```sql
ALTER TABLE `customer` 
ADD COLUMN `user_type` ENUM('student', 'creator') DEFAULT 'student' AFTER `user_role`;

UPDATE `customer` SET `user_type` = 'student' WHERE `user_type` IS NULL;
```

### Customer Table Structure:
- customer_id (int)
- customer_name (varchar)
- customer_email (varchar)
- customer_pass (varchar)
- customer_country (varchar)
- customer_city (varchar)
- customer_contact (varchar)
- customer_image (varchar)
- user_role (int) - 1=Admin, 2=Regular User
- **user_type (enum)** - 'student' or 'creator' ← NEW

## Session Variables

After login, these are stored:
- `$_SESSION['user_id']` - User ID
- `$_SESSION['user_name']` - Full name
- `$_SESSION['user_email']` - Email
- `$_SESSION['user_role']` - 1 or 2
- `$_SESSION['user_type']` - 'student' or 'creator'

## Permission Checks

### To show Upload link:
```php
<?php if ($_SESSION['user_role'] == 1 || $_SESSION['user_type'] == 'creator'): ?>
    <li><a href="upload.php">Upload</a></li>
<?php endif; ?>
```

### To restrict upload page:
```php
if (!isset($_SESSION['user_id']) || 
    ($_SESSION['user_role'] != 1 && $_SESSION['user_type'] != 'creator')) {
    $_SESSION['error'] = 'Only creators and admins can upload resources.';
    header('Location: home.php');
    exit;
}
```

## Files Modified

1. **app/views/auth/register.php**
   - Added user type selection dropdown
   - Added helpful descriptions

2. **app/controllers/auth_controller.php**
   - Updated register() to handle user_type
   - Updated login() to store user_type in session

3. **app/models/user_model.php**
   - Updated register() method to save user_type

4. **app/views/layouts/header.php**
   - Upload link now shows for creators and admins only

5. **app/views/profile/dashboard.php**
   - Shows user type badge (🎓 Student or ✍️ Creator)

6. **db/add_user_type.sql**
   - Migration script to add user_type column

## Testing Checklist

### Student Account:
- [ ] Can register as student
- [ ] Cannot see "Upload" in navigation
- [ ] Can browse resources
- [ ] Can add to cart and purchase
- [ ] Can download purchased items
- [ ] Dashboard shows "🎓 Student Account"

### Creator Account:
- [ ] Can register as creator
- [ ] Can see "Upload" in navigation
- [ ] Can upload resources
- [ ] Can browse and purchase resources
- [ ] Can download purchased items
- [ ] Dashboard shows "✍️ Creator Account"

### Admin Account:
- [ ] Can see both "Admin" and "Upload"
- [ ] Has full access to all features
- [ ] Can manage users and content

## Future Enhancements

### For Creators:
- View their uploaded resources
- See sales statistics
- Earnings dashboard
- Withdraw earnings
- Edit/delete their resources

### For Students:
- Wishlist feature
- Resource recommendations
- Learning progress tracking
- Certificates for completed courses

## Important Notes

1. **Existing Users**: After running the migration, all existing users will be set as "student" by default. You may need to manually update creators in the database.

2. **Admin Users**: Admins (user_role = 1) can upload regardless of user_type.

3. **Default Value**: If user_type is not set during registration, it defaults to 'student'.

4. **Security**: Always check both user_role and user_type when restricting access to upload functionality.
