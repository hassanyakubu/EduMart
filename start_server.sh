#!/bin/bash

# EduMart Development Server Starter

echo "=========================================="
echo "   Starting EduMart Development Server"
echo "=========================================="
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8+ first."
    exit 1
fi

# Check if port 8000 is available
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️  Port 8000 is already in use."
    echo "Please stop the other process or use a different port."
    echo ""
    echo "To use a different port, run:"
    echo "php -S localhost:PORT"
    exit 1
fi

echo "✅ Starting PHP development server on port 8000..."
echo ""
echo "Access the application at:"
echo "🌐 http://localhost:8000"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""
echo "=========================================="
echo ""

# Start the server
php -S localhost:8000
