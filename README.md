# EduMart - Educational Resources Marketplace

A web-based platform where students can purchase educational materials and take quizzes, while creators can upload and sell their content.


## About This Project

EduMart is an e-learning marketplace that connects students (JHS and SHS) with educational resources from previous years (past questions and sample questions). Students can buy study materials and access quizzes, while content creators earn money by sharing their knowledge.

## Main Features

### Students Can:
- Browse educational resources by category
- Purchase materials using mobile money or card
- Download purchased resources anytime
- Take quizzes for subjects they've purchased
- View their order history

### Creators Can:
- Upload educational resources (PDFs, documents)
- Set prices for their materials
- Create quizzes for their content
- Earn 80% commission on sales
- Track their earnings

### Admin Can:
- Manage users and resources
- View platform analytics
- Monitor sales and revenue
- Manage quizzes

## How It Works

1. **Students** browse resources and add items to cart
2. **Checkout** with secure Paystack payment (mobile money or card)
3. **Access** purchased materials immediately
4. **Take quizzes** for subjects they've purchased
5. **Creators** earn money when their resources are sold

## Technologies Used

- PHP (Backend)
- MySQL (Database)
- HTML/CSS/JavaScript (Frontend)
- Paystack (Payment Gateway)
- Apache Server

## Setup Instructions

### 1. Database Setup
- Import `db/ecommerce_2025A_hassan_yakubu.sql` into MySQL
- Update database credentials in `settings/db_cred.php`

### 2. Payment Setup
- Get Paystack API keys from paystack.com
- Update keys in `settings/paystack_config.php`
- Use test mode for development

### 3. File Permissions
Make sure the uploads folder is writable:
```bash
chmod -R 755 uploads/
```

### 4. Access the Site
- Local: `http://localhost/EduMart`
- Server: `http://169.239.251.102:442/~hassan.yakubu/EduMart`

## Testing the Site

### Test Payment (No Real Money!)
When testing payments, use these Paystack test credentials:
- **Card Number:** 4084084084084081
- **CVV:** 408
- **Expiry Date:** 12/25
- **PIN:** 0000
- **OTP:** 123456

Or use test mobile money numbers:
- MTN: 0241234567
- Vodafone: 0201234567

## How Quiz Access Works

Students can only take quizzes for subjects they've purchased. For example:
- Buy "JHS English Language" resource → Can take JHS English Language quizzes
- Buy "SHS Core Mathematics" resource → Can take SHS Core Mathematics quizzes

This ensures students have the study materials before taking quizzes.

## Revenue Sharing

- **Creators earn:** 80% of each sale
- **Platform earns:** 20% commission
- **Example:** Resource sells for GHS 25 → Creator gets GHS 20, Platform gets GHS 5

## Project Structure

```
EduMart/
├── app/                # Main application code
│   ├── controllers/    # Handles business logic
│   ├── models/         # Database operations
│   └── views/          # Page templates
├── settings/           # Configuration files
├── view/               # Public pages
├── uploads/            # Uploaded resources
└── db/                 # Database files
```

## Key Features Implemented

- User authentication (students, creators, admin)
- Shopping cart system
- Secure payment with Paystack
- Resource upload and management
- Quiz creation and taking
- Order tracking
- Sales analytics
- Commission tracking
- Download management

## Credits

**Developer:** Hassan Yakubu
**Institution:** Ashesi University
**Course:** E-Commerce 
**Git Repo:** https://github.com/hassanyakubu/EduMart
