# Path Fix Guide for EduMart

## Problem
The application is deployed in a subdirectory: `/~hassan.yakubu/EduMart/`
All absolute paths starting with `/app/` or `/public/` fail because they don't include the subdirectory.

## Solution Implemented
Created `app/config/config.php` with helper functions:
- `url($path)` - for application URLs
- `asset($path)` - for public assets (CSS, JS, images)

## Files Already Fixed
✅ app/views/layouts/header.php
✅ app/views/layouts/footer.php  
✅ app/views/home/index.php
✅ app/views/cart/view.php

## Files That Still Need Fixing

### High Priority (Navigation & Core)
- app/views/resources/list.php
- app/views/resources/details.php
- app/views/checkout/payment.php
- app/views/checkout/payment_simulated.php

### Medium Priority
- app/views/admin/dashboard.php
- app/views/profile/dashboard.php
- app/views/orders/list.php
- app/views/orders/invoice.php

### Low Priority
- app/views/checkout/success.php
- app/views/resources/upload.php

## Pattern Replacements Needed

### For Links (href)
BEFORE: `href="/app/views/page.php"`
AFTER: `href="<?php echo url('app/views/page.php'); ?>"`

### For Forms (action)
BEFORE: `action="/app/views/process.php"`
AFTER: `action="<?php echo url('app/views/process.php'); ?>"`

### For Images (src)
BEFORE: `src="/public/assets/images/pic.jpg"`
AFTER: `src="<?php echo asset('assets/images/pic.jpg'); ?>"`

### For CSS/JS
BEFORE: `href="/public/assets/css/style.css"`
AFTER: `href="<?php echo asset('assets/css/style.css'); ?>"`

## Quick Fix Command
Run this search and replace in each file:
1. Replace `/app/views/` with `<?php echo url('app/views/` 
2. Add `'); ?>` before the closing quote
3. Replace `/public/` with `<?php echo asset('`
4. Add `'); ?>` before the closing quote
