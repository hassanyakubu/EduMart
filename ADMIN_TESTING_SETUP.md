# Admin Testing Setup Checklist

## ✅ Pre-Testing Checklist

Before inviting testers, make sure:

### 1. Server & Database
- [ ] Server is running (Apache + MySQL)
- [ ] Database is accessible
- [ ] All tables exist and are properly configured
- [ ] Payment system is working

**Test:** Visit http://169.239.251.102:442/~hassan.yakubu/EduMart

### 2. Paystack Configuration
- [ ] Paystack test keys are configured in `settings/paystack_config.php`
- [ ] Test mode is enabled (`APP_ENVIRONMENT = 'test'`)
- [ ] Callback URL is correct

**Current Settings:**
```php
define('PAYSTACK_SECRET_KEY', 'sk_test_...');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_...');
define('APP_ENVIRONMENT', 'test');
```

### 3. Sample Data
- [ ] At least 5-10 resources are uploaded
- [ ] Resources have images and descriptions
- [ ] Resources are in different categories
- [ ] At least 2-3 quizzes are created and published
- [ ] Quizzes have questions

**Check:** Browse the site and verify content exists

### 4. Test Accounts
Create a few test accounts for testers:

**Student Accounts:**
- Email: student1@test.com / Password: Test123!
- Email: student2@test.com / Password: Test123!

**Creator Account:**
- Email: creator1@test.com / Password: Test123!

**Admin Account:**
- Your admin account for monitoring

### 5. Payment Testing
- [ ] Test a payment yourself first
- [ ] Verify order is created
- [ ] Verify order_items are created
- [ ] Verify downloads are created
- [ ] Verify quiz access works

**Test Payment:**
1. Add item to cart
2. Checkout
3. Use test card: 4084084084084081
4. Complete payment
5. Check database for order_items

### 6. Quiz Access Testing
- [ ] Create a quiz in category 3 (BECE English)
- [ ] Publish the quiz
- [ ] Purchase a resource in category 3
- [ ] Verify you can see and take the quiz

### 7. Error Handling
- [ ] Test with empty cart
- [ ] Test with invalid payment
- [ ] Test accessing quiz without purchase
- [ ] Check error messages are user-friendly

## 📋 Testing Scenarios to Prepare

### Scenario 1: New Student Journey
1. Register → Browse → Add to Cart → Checkout → Pay → Download → Take Quiz

### Scenario 2: Multiple Purchases
1. Buy resource A → Buy resource B → Check both are accessible

### Scenario 3: Quiz Access
1. Buy BECE English → See BECE quiz → Take quiz → Get results

## 🔧 Quick Fixes Before Testing

### Fix 1: Ensure All Quizzes Are Published
```sql
UPDATE quizzes SET is_published = 1;
```

### Fix 2: Verify Resources Have Images
```sql
SELECT resource_id, resource_title, resource_image 
FROM resources 
WHERE resource_image IS NULL OR resource_image = '';
```

### Fix 3: Check Categories
```sql
SELECT * FROM categories;
```

Make sure you have diverse categories.

## 👥 Inviting Testers

### Option 1: Send Direct Link
Share: http://169.239.251.102:442/~hassan.yakubu/EduMart

### Option 2: Provide Test Accounts
Give testers pre-created accounts so they can start immediately.

### Option 3: Let Them Register
Allow testers to create their own accounts.

## 📊 Monitoring During Testing

### Check These Regularly:

**1. Database Activity:**
```sql
-- Recent purchases
SELECT * FROM purchases ORDER BY purchase_id DESC LIMIT 10;

-- Recent order_items
SELECT * FROM order_items ORDER BY order_item_id DESC LIMIT 10;

-- Quiz attempts
SELECT * FROM quiz_attempts ORDER BY attempt_id DESC LIMIT 10;
```

**2. Error Logs:**
Check for PHP errors during testing.

**3. User Feedback:**
Keep a document to track issues reported by testers.

## 🐛 Common Issues & Quick Fixes

### Issue: "No quizzes found"
**Fix:** Publish quizzes
```sql
UPDATE quizzes SET is_published = 1;
```

### Issue: Payment not completing
**Check:**
1. Paystack keys are correct
2. Callback URL is accessible
3. Check `check_current_state.php` for order_items

### Issue: Can't download resources
**Check:**
1. Files exist in `uploads/files/` directory
2. Downloads table has records
3. File permissions are correct

### Issue: Quiz won't load
**Check:**
1. Quiz has questions
2. Quiz is published
3. Student purchased correct category

## 📝 Feedback Collection

Create a simple form or document for testers to report:
1. What they tested
2. What worked
3. What didn't work
4. Suggestions

## 🎯 Success Metrics

Testing is successful when:
- [ ] 5+ testers complete full purchase flow
- [ ] 3+ testers successfully take quizzes
- [ ] No critical bugs reported
- [ ] Payment system works 100% of the time
- [ ] Quiz access works correctly

## 🚀 Go Live Checklist

Before going live with real payments:
1. [ ] Switch to Paystack LIVE keys
2. [ ] Change `APP_ENVIRONMENT` to 'production'
3. [ ] Remove test accounts
4. [ ] Clear test data (optional)
5. [ ] Backup database
6. [ ] Test one real payment yourself

## 📞 Support During Testing

Be available to:
- Answer questions
- Fix critical bugs quickly
- Monitor database
- Respond to feedback

## 🎉 Ready to Test!

Once all checkboxes are complete, you're ready to invite testers!

**Share with testers:**
1. Site URL: http://169.239.251.102:442/~hassan.yakubu/EduMart
2. Testing guide: `TESTING_GUIDE.md`
3. Test payment credentials
4. Your contact for reporting issues

Good luck with testing! 🚀
