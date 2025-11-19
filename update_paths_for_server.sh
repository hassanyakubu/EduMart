#!/bin/bash

# Script to update all paths for server deployment
# Server: http://169.239.251.102:442/~hassan.yakubu

echo "🔧 Updating paths for server deployment..."
echo "Server: http://169.239.251.102:442/~hassan.yakubu"
echo ""

BASE_PATH="/~hassan.yakubu"

# Update header.php
echo "📝 Updating header.php..."
sed -i '' "s|href=\"/public/|href=\"${BASE_PATH}/public/|g" app/views/layouts/header.php
sed -i '' "s|href=\"/app/views/|href=\"${BASE_PATH}/app/views/|g" app/views/layouts/header.php

# Update footer.php
echo "📝 Updating footer.php..."
sed -i '' "s|src=\"/public/|src=\"${BASE_PATH}/public/|g" app/views/layouts/footer.php

# Update all view files
echo "📝 Updating view files..."
find app/views -type f -name "*.php" -exec sed -i '' "s|href=\"/app/views/|href=\"${BASE_PATH}/app/views/|g" {} \;
find app/views -type f -name "*.php" -exec sed -i '' "s|src=\"/public/|src=\"${BASE_PATH}/public/|g" {} \;
find app/views -type f -name "*.php" -exec sed -i '' "s|action=\"/app/views/|action=\"${BASE_PATH}/app/views/|g" {} \;

# Update index.php
echo "📝 Updating index.php..."
cat > index.php << 'EOF'
<?php
/**
 * EduMart - Digital Learning Resources Marketplace
 * Main Entry Point
 */

// Define base URL
define('BASE_URL', 'http://169.239.251.102:442/~hassan.yakubu');

// Redirect to home page
header('Location: ' . BASE_URL . '/app/views/home/index.php');
exit;
?>
EOF

# Create config file
echo "📝 Creating config file..."
cat > app/config/config.php << 'EOF'
<?php
/**
 * Base URL Configuration
 */

define('BASE_URL', 'http://169.239.251.102:442/~hassan.yakubu');
define('BASE_PATH', '/~hassan.yakubu');

/**
 * Helper function to generate URLs
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Helper function for assets
 */
function asset($path = '') {
    return BASE_URL . '/public/assets/' . ltrim($path, '/');
}
?>
EOF

# Update .htaccess
echo "📝 Updating .htaccess..."
cat > .htaccess << 'EOF'
# EduMart .htaccess Configuration for Subdirectory

# Enable Rewrite Engine
RewriteEngine On
RewriteBase /~hassan.yakubu/

# Remove trailing slashes
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)/$ /$1 [L,R=301]

# Protect sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Prevent directory browsing
Options -Indexes

# Set default charset
AddDefaultCharset UTF-8

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# PHP Settings
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
php_value max_input_time 300
EOF

echo ""
echo "✅ All paths updated!"
echo ""
echo "Next steps:"
echo "1. Upload files to server: scp -P 442 -r * hassan.yakubu@169.239.251.102:~/public_html/"
echo "2. SSH into server: ssh hassan.yakubu@169.239.251.102 -p 442"
echo "3. Set permissions: chmod -R 777 public/uploads/"
echo "4. Import database"
echo "5. Test: http://169.239.251.102:442/~hassan.yakubu"
echo ""
