# EduMart Login Flow

## Public Access (No Login Required)
Users can browse without logging in:
- ✅ Home page (index.php) - View hero section with contact info
- ✅ Featured resources display
- ✅ Browse by category
- ✅ Resources list page - View all resources with filters

## Login Required
The following actions require authentication:

### Resource Actions
- ❌ View resource details
- ❌ Add to cart
- ❌ Upload resources
- ❌ Add reviews

### Shopping Actions
- ❌ View cart
- ❌ Checkout
- ❌ View orders
- ❌ Download purchased resources

### User Actions
- ❌ Profile dashboard
- ❌ Order history
- ❌ Admin dashboard (admin only)

## Login Flow
1. User clicks on any protected action (e.g., "View Details", "Add to Cart")
2. System stores the requested URL in session: `$_SESSION['redirect_after_login']`
3. User is redirected to login page with error message
4. After successful login, user is redirected back to the originally requested page
5. If no redirect URL exists, users go to:
   - Admin dashboard (for admin users)
   - Home page (for regular users)

## Error Messages
- "Please log in to view resource details."
- "Please log in to add items to your cart."
- "Please log in to view your cart."
- "Please log in to upload resources."

## Logout Behavior
- After logout, users are redirected to the home page (not login page)
- They can continue browsing resources without logging back in

## Implementation Details

### Files Updated
1. `app/views/resources/details.php` - Login required
2. `app/views/cart/add.php` - Login required
3. `app/views/cart/view.php` - Login required
4. `app/views/resources/upload.php` - Login required
5. `app/controllers/auth_controller.php` - Redirect logic added

### Session Variables Used
- `$_SESSION['user_id']` - User authentication check
- `$_SESSION['redirect_after_login']` - Store URL to redirect after login
- `$_SESSION['error']` - Display error messages
- `$_SESSION['success']` - Display success messages
