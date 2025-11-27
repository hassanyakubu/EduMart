# EduMart Testing Guide

## Welcome Testers! 👋

Thank you for testing EduMart! This guide will help you test all the key features.

## 🌐 Access the Site

**URL:** http://169.239.251.102:442/~hassan.yakubu/EduMart

## 🧪 Test Mode

The site is in **TEST MODE** - no real money will be charged!

## 📝 What to Test

### 1. User Registration & Login

**Test Registration:**
1. Click "Sign Up" or "Register"
2. Create a new account (use a test email like `tester1@test.com`)
3. Fill in all required fields
4. Submit and verify you can log in

**Test Login:**
1. Log in with your new account
2. Verify you're redirected to the home page
3. Check that your name appears in the navigation

### 2. Browse Resources

**Test Browsing:**
1. Go to "Resources" or "Browse"
2. Check if resources are displayed correctly
3. Click on a resource to view details
4. Check if images, descriptions, and prices show correctly

**Test Categories:**
1. Filter by different categories
2. Verify filtering works correctly

### 3. Shopping Cart

**Test Adding to Cart:**
1. Click "Add to Cart" on a resource
2. Verify item appears in cart
3. Check cart count badge updates
4. Add multiple items

**Test Cart Management:**
1. Go to cart page
2. Verify all items are listed
3. Try removing an item
4. Check total price calculation

### 4. Checkout & Payment (IMPORTANT!)

**Test Checkout:**
1. Click "Checkout" from cart
2. Review order summary
3. Click "Proceed to Payment"

**Test Payment with Paystack:**
1. You'll be redirected to Paystack
2. Use these **TEST CREDENTIALS** (no real money!):
   - **Card Number:** 4084084084084081
   - **CVV:** 408
   - **Expiry:** 12/25 (any future date)
   - **PIN:** 0000
   - **OTP:** 123456

3. Complete the payment
4. You should be redirected back to EduMart
5. Verify you see a success message

**Alternative: Test Mobile Money**
- Use test numbers: 0241234567 (MTN) or 0201234567 (Vodafone)
- Follow the prompts

### 5. After Purchase

**Test Order Confirmation:**
1. Check if you see order confirmation page
2. Verify invoice number is displayed
3. Check order details are correct

**Test My Orders:**
1. Go to "My Orders" or "Order History"
2. Verify your purchase appears
3. Check order status is "Paid" or "Completed"

**Test Downloads:**
1. Go to "My Downloads" or "My Resources"
2. Verify purchased resources appear
3. Try downloading a resource
4. Check if download works

### 6. Quiz Access (CRITICAL!)

**Test Quiz Access:**
1. After purchasing a resource, go to "Quizzes"
2. You should see quizzes for the category you purchased
3. Example: If you bought "BECE English", you should see "BECE English Quiz"
4. Click on a quiz to take it

**Test Taking Quiz:**
1. Click "Start Quiz" or "Take Quiz"
2. Answer the questions
3. Submit the quiz
4. Check if you see your results
5. Verify score is calculated correctly

### 7. User Profile

**Test Profile:**
1. Go to "My Profile" or "Account"
2. Check if your information is displayed
3. Try updating your profile (if available)

### 8. Creator Features (If Applicable)

**If you're testing as a creator:**
1. Upload a resource
2. Create a quiz
3. Publish the quiz
4. Check analytics/earnings

## 🐛 What to Report

Please report any issues you find:

### Critical Issues:
- ❌ Cannot register/login
- ❌ Payment fails or doesn't complete
- ❌ Cannot access purchased resources
- ❌ Cannot see quizzes after purchase
- ❌ Quiz doesn't work or submit

### Important Issues:
- ⚠️ Images not loading
- ⚠️ Cart not updating
- ⚠️ Prices incorrect
- ⚠️ Download doesn't work

### Minor Issues:
- 🔧 Typos or grammar errors
- 🔧 Layout issues
- 🔧 Slow loading
- 🔧 Confusing navigation

## 📊 Test Scenarios

### Scenario 1: Student Buying Resources
1. Register as student
2. Browse resources
3. Add 2-3 items to cart
4. Complete checkout
5. Verify you can download resources
6. Check if you can access quizzes

### Scenario 2: Taking Quizzes
1. Purchase a resource (e.g., BECE English)
2. Go to Quizzes page
3. Find quiz for that category
4. Take the quiz
5. Submit and view results

### Scenario 3: Multiple Purchases
1. Make a purchase
2. Add more items to cart
3. Make another purchase
4. Check order history shows both orders
5. Verify all resources are accessible

## ✅ Success Criteria

The system is working correctly if:
- ✅ You can register and login
- ✅ You can browse and add items to cart
- ✅ Payment completes successfully (with test credentials)
- ✅ You receive order confirmation
- ✅ Purchased resources appear in "My Downloads"
- ✅ You can access quizzes for purchased categories
- ✅ Quizzes work and show results

## 💡 Tips

- Use **test credentials** - no real money will be charged
- Test on different devices (phone, tablet, computer)
- Try different browsers (Chrome, Firefox, Safari)
- Take screenshots of any errors
- Note the steps that led to any issues

## 📞 Report Issues

When reporting issues, please include:
1. What you were trying to do
2. What happened (error message, unexpected behavior)
3. Screenshot (if possible)
4. Browser and device you're using

## 🎉 Thank You!

Your testing helps make EduMart better for everyone!

---

**Test Accounts (if needed):**
- Email: tester1@test.com
- Password: Test123!

(Create your own or use these if provided)
