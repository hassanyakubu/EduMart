# Admin Dashboard Guide

## Overview
The admin dashboard has been redesigned for simplicity and functionality. It now focuses on core management tasks without unnecessary buttons.

---

## Admin Header (Simplified)

When in admin dashboard, the header shows only:
- **⚙️ EduMart Admin** (logo/home)
- **🏠 Exit Admin** - Return to main site
- **🚪 Logout** - Log out

**Removed from admin view:**
- Browse Resources
- Admin (redundant when already in admin)
- Cart
- Profile

---

## Dashboard Layout

### Statistics Cards (Top Section)
Three key metrics displayed prominently:

1. **👥 Total Users** - Yellow gradient card
2. **📚 Total Resources** - Purple gradient card  
3. **🛒 Total Orders** - Pink gradient card

These are **display-only** - no buttons, just numbers.

---

### Management Sections

#### 1. 📤 Upload Resources
- **Button**: "➕ Upload New Resource"
- **Action**: Takes you to upload form
- **Purpose**: Add new educational content to the platform

#### 2. 🎓 Manage Students
- **Button**: "View All Students"
- **Action**: Opens student management page
- **Features**:
  - View all students in a table
  - See: ID, Name, Email, Country, City, Contact
  - Delete individual students
  - Cannot delete yourself

#### 3. ✍️ Manage Creators
- **Button**: "View All Creators"
- **Action**: Opens creator management page
- **Features**:
  - View all creators in a table
  - See: ID, Name, Email, Country, City, Contact
  - Delete individual creators
  - Cannot delete yourself

#### 4. 📚 Manage Content
- **Button**: "View All Resources"
- **Action**: Opens resource management page
- **Features**:
  - View all resources in a table
  - See: ID, Title, Category, Price, Creator
  - Delete individual resources
  - Upload new resources

---

## Pages Created

### 1. Admin Dashboard
**File**: `app/views/admin/dashboard.php`
- Uses special admin header
- Shows statistics
- Links to management pages

### 2. Manage Students
**File**: `app/views/admin/students.php`
- Lists all users with `user_type = 'student'`
- Delete functionality with confirmation
- Back button to dashboard

### 3. Manage Creators
**File**: `app/views/admin/creators.php`
- Lists all users with `user_type = 'creator'`
- Delete functionality with confirmation
- Back button to dashboard

### 4. Manage Resources
**File**: `app/views/admin/resources.php`
- Lists all resources on platform
- Shows resource details
- Delete functionality with confirmation
- Upload new button
- Back button to dashboard

### 5. Delete Handlers
**Files**: 
- `app/views/admin/delete_user.php`
- `app/views/admin/delete_resource.php`

**Security Features**:
- Admin cannot delete themselves
- Confirmation required before deletion
- Redirects back with success/error message

---

## Admin Controller Methods

### New Methods Added:

```php
getStudents()        // Returns array of student users
getCreators()        // Returns array of creator users
getAllResources()    // Returns all resources
deleteUser($id)      // Deletes a user
deleteResource($id)  // Deletes a resource
```

---

## Security Features

### 1. Admin-Only Access
All admin pages check:
```php
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header('Location: login.php');
    exit;
}
```

### 2. Self-Deletion Prevention
Admin cannot delete their own account:
```php
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = 'You cannot delete your own account!';
    exit;
}
```

### 3. Confirmation Dialogs
JavaScript confirmation before deletion:
```javascript
onclick="return confirm('Are you sure you want to delete this student?');"
```

---

## User Flow

### Admin Login Flow:
1. Log in with admin credentials (yhassan677@gmail.com)
2. Redirected to Admin Dashboard
3. See simplified header (Exit Admin, Logout only)
4. View statistics at top
5. Access management sections below

### Managing Students:
1. Click "View All Students"
2. See table of all students
3. Click "🗑️ Delete" to remove a student
4. Confirm deletion
5. Student removed, redirected back with success message

### Managing Creators:
1. Click "View All Creators"
2. See table of all creators
3. Click "🗑️ Delete" to remove a creator
4. Confirm deletion
5. Creator removed, redirected back with success message

### Managing Resources:
1. Click "View All Resources"
2. See table of all resources
3. Click "➕ Upload New" to add resource
4. Click "🗑️ Delete" to remove resource
5. Confirm deletion
6. Resource removed, redirected back with success message

---

## Navigation Structure

```
Admin Dashboard
├── Upload Resources → Upload Form
├── Manage Students → Students List → Delete Student
├── Manage Creators → Creators List → Delete Creator
└── Manage Content → Resources List → Delete Resource
                                    └── Upload New
```

---

## Files Modified/Created

### Created:
1. `app/views/layouts/admin_header.php` - Simplified admin header
2. `app/views/admin/students.php` - Student management
3. `app/views/admin/creators.php` - Creator management
4. `app/views/admin/resources.php` - Resource management
5. `app/views/admin/delete_user.php` - User deletion handler
6. `app/views/admin/delete_resource.php` - Resource deletion handler

### Modified:
1. `app/views/admin/dashboard.php` - Redesigned layout
2. `app/controllers/admin_controller.php` - Added new methods

---

## Design Features

### Color Scheme:
- **Admin Header**: Purple gradient (#667eea to #764ba2)
- **Statistics Cards**: 
  - Users: Yellow (#FFD947)
  - Resources: Purple (#667eea)
  - Orders: Pink (#f093fb)
- **Management Cards**: White with shadow
- **Buttons**: 
  - Primary: Yellow (#FFD947)
  - Secondary: Gray
  - Danger: Red

### Typography:
- **Headers**: Inter font, bold
- **Body**: Inter font, regular
- **Icons**: Emoji for visual clarity

---

## Testing Checklist

- [ ] Admin can access dashboard
- [ ] Statistics display correctly
- [ ] Can view all students
- [ ] Can delete students (not self)
- [ ] Can view all creators
- [ ] Can delete creators (not self)
- [ ] Can view all resources
- [ ] Can delete resources
- [ ] Can upload new resources
- [ ] Exit Admin returns to main site
- [ ] Logout works correctly
- [ ] Non-admins cannot access admin pages
- [ ] Confirmation dialogs appear before deletion
- [ ] Success/error messages display correctly

---

## Future Enhancements

### Potential Features:
- Edit user information
- Edit resource details
- Bulk delete operations
- Search and filter functionality
- Export data to CSV
- View detailed analytics
- Manage categories inline
- Approve/reject uploaded content
- Ban/suspend users
- View user activity logs

---

## Troubleshooting

### Problem: Can't access admin dashboard
**Solution**: Verify you're logged in as admin (user_role = 1)

### Problem: Delete button doesn't work
**Solution**: Check JavaScript is enabled for confirmation dialog

### Problem: Statistics show 0
**Solution**: Ensure database has data and connection is working

### Problem: Header shows regular menu
**Solution**: Ensure page uses `admin_header.php` not `header.php`

---

## Quick Reference

### Admin Email:
yhassan677@gmail.com

### Admin Dashboard URL:
`http://169.239.251.102:442/~hassan.yakubu/EduMart/app/views/admin/dashboard.php`

### Key Shortcuts:
- **Exit Admin**: Return to main site
- **Logout**: End session
- **Back buttons**: Return to dashboard from management pages
