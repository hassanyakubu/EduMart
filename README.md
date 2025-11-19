# EduMart - Digital Learning Resources Marketplace

A complete e-commerce platform for selling and downloading educational resources (ebooks, PDFs, past questions, notes, videos).

## Features

- User registration and authentication
- Browse and search educational resources
- Shopping cart system
- Mobile money payment (MTN MoMo, Vodafone Cash, AirtelTigo) - Simulated
- Secure downloads after purchase
- Rating and review system
- Order history and invoices
- Admin dashboard for management

## Technology Stack

- **Backend**: PHP 8+
- **Database**: MySQL 8.0
- **Architecture**: MVC (Model-View-Controller)
- **Frontend**: HTML5, CSS3, JavaScript
- **Design**: Light Grey (#F4F4F4) + Yellow (#FFD947)

## Quick Setup

### 1. Import Database (Single File)
```bash
mysql -u root -p < db/ecommerce_2025A_hassan_yakubu.sql
```

**Note**: There is only ONE database file.

### 2. Configure Database
Edit `settings/db_cred.php` if needed (default settings work for most setups)

### 3. Create Upload Directories
```bash
mkdir -p public/uploads/images public/uploads/files
chmod -R 777 public/uploads
```

### 4. Start Server
```bash
php -S localhost:8000
```

### 5. Access Application
Open `http://localhost:8000` in your browser

## Project Structure

```
EduMart/
├── app/
│   ├── controllers/    # Business logic (snake_case)
│   ├── models/         # Database operations (snake_case)
│   ├── views/          # HTML templates
│   ├── config/         # Configuration files
│   └── helpers/        # Utility functions
├── public/
│   ├── assets/         # CSS, JS, images
│   └── uploads/        # User uploaded files
├── settings/           # Database and payment config
├── db/                 # Database SQL files
└── view/               # Template files
```

## Default Admin Account

To create an admin account:
```sql
UPDATE customer SET user_role = 1 WHERE customer_email = 'your@email.com';
```

## Testing Payment

The platform uses simulated mobile money payment:
1. Add items to cart
2. Proceed to checkout
3. Select payment method (MTN MoMo, Vodafone Cash, or AirtelTigo)
4. Enter any 10-digit phone number (e.g., 0244123456)
5. Complete payment (90% success rate for realistic simulation)
6. Download your resources

## Key Features

### For Students
- Browse resources by category
- Search and filter
- Add to cart
- Secure payment
- Instant downloads
- Leave reviews

### For Creators
- Upload resources
- Set pricing
- Add descriptions
- Manage content

### For Admins
- Dashboard with statistics
- User management
- Resource management
- Category management
- Order tracking

## Design

- **Colors**: Light Grey background (#F4F4F4) with Yellow accents (#FFD947)
- **Typography**: Inter font family
- **Style**: Modern, clean, rounded edges
- **Responsive**: Mobile, tablet, and desktop optimized

## Security

- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- XSS protection
- Session-based authentication
- Download access control

## Documentation

- **PAYSTACK_ARCHITECTURE.md** - Payment system architecture
- **PAYSTACK_SETUP.md** - Payment setup guide

## Support

For issues or questions, check the code comments or review the database structure.

## License

Educational project for Ashesi University

---

**Status**: Ready for testing and demonstration
**Platform**: EduMart
**Architecture**: MVC with snake_case naming
**Payment**: Simulated mobile money (safe for testing)
