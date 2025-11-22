# EduMart Deployment Notes

## Server Configuration
- **URL**: http://169.239.251.102:442/~hassan.yakubu/EduMart/
- **Entry Point**: app/views/home/index.php
- **Database**: ecommerce_2025A_hassan_yakubu
- **DB User**: hassan.yakubu
- **DB Password**: jonnytest

## Path Configuration
All paths are now configured through `app/config/config.php`:

```php
define('BASE_URL', '/~hassan.yakubu/EduMart');
```

### Helper Functions
- `url($path)` - For application URLs
- `asset($path)` - For public assets (CSS, JS, images)

## Files Fixed for Subdirectory Deployment

### ✅ Completed
1. app/config/config.php (created)
2. app/views/layouts/header.php
3. app/views/layouts/footer.php
4. app/views/home/index.php
5. app/views/cart/view.php
6. app/views/resources/list.php
7. app/views/resources/details.php
8. app/views/checkout/payment.php
9. app/views/checkout/payment_simulated.php

### ⚠️ May Need Fixing (if you encounter 404s)
- app/views/admin/dashboard.php
- app/views/profile/dashboard.php
- app/views/orders/list.php
- app/views/orders/invoice.php
- app/views/checkout/success.php
- app/views/resources/upload.php

## Testing Checklist
1. ✅ CSS loads correctly
2. ✅ JS loads correctly
3. ✅ Navigation links work
4. ✅ Images display
5. Test cart functionality
6. Test checkout process
7. Test resource browsing
8. Test user authentication

## Common Issues & Solutions

### CSS Not Loading
**Problem**: GET http://169.239.251.102:442/public/assets/css/styles.css 404
**Solution**: Already fixed - now uses `asset('assets/css/styles.css')`

### Links Not Working
**Problem**: Clicking links gives 404
**Solution**: Already fixed - now uses `url('app/views/page.php')`

### Images Not Displaying
**Problem**: Resource images show broken
**Solution**: Already fixed - now uses `asset($image_path)`

## Quick Fix for Remaining Files
If you encounter 404s in other pages, apply these replacements:

1. **For links**: 
   - Find: `href="/app/views/`
   - Replace: `href="<?php echo url('app/views/`
   - Add: `'); ?>` before closing quote

2. **For forms**:
   - Find: `action="/app/views/`
   - Replace: `action="<?php echo url('app/views/`
   - Add: `'); ?>` before closing quote

3. **For assets**:
   - Find: `src="/public/`
   - Replace: `src="<?php echo asset('`
   - Add: `'); ?>` before closing quote
