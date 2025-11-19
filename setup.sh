#!/bin/bash

# EduMart Setup Script
# This script helps set up the EduMart application

echo "=========================================="
echo "   EduMart Setup Script"
echo "=========================================="
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8+ first."
    exit 1
fi

echo "✅ PHP found: $(php -v | head -n 1)"
echo ""

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    echo "⚠️  MySQL command not found. Make sure MySQL is installed."
else
    echo "✅ MySQL found"
fi
echo ""

# Create upload directories
echo "Creating upload directories..."
mkdir -p public/uploads/images
mkdir -p public/uploads/files
chmod -R 777 public/uploads
echo "✅ Upload directories created"
echo ""

# Check if database file exists
if [ -f "db/ecommerce_2025A_hassan_yakubu.sql" ]; then
    echo "✅ Database file found"
    echo ""
    echo "To import the database, run:"
    echo "mysql -u root -p < db/ecommerce_2025A_hassan_yakubu.sql"
else
    echo "⚠️  Database file not found"
fi
echo ""

# Check configuration
if [ -f "app/config/database.php" ]; then
    echo "✅ Database configuration file found"
    echo ""
    echo "Please update database credentials in:"
    echo "app/config/database.php"
else
    echo "❌ Database configuration file not found"
fi
echo ""

echo "=========================================="
echo "   Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Import the database (see command above)"
echo "2. Update database credentials in app/config/database.php"
echo "3. Start the server: php -S localhost:8000"
echo "4. Open browser: http://localhost:8000"
echo ""
echo "For detailed instructions, see SETUP_GUIDE.md"
echo ""
