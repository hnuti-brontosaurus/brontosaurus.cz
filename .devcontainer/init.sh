#!/bin/bash
set -e

echo "Starting WordPress initialization..."

# Create wp-config.php first if it doesn't exist
if [ ! -f "/var/www/html/wp-config.php" ]; then
    echo "Creating wp-config.php..."
    sudo -u www-data wp config create \
        --dbname=wordpress \
        --dbuser=root \
        --dbpass= \
        --dbhost=database \
        --path=/var/www/html \
        --skip-check
    echo "wp-config.php created!"
else
    echo "wp-config.php already exists"
fi

# Now wait for database to be ready
echo "Waiting for database connection..."
until sudo -u www-data wp db check --path=/var/www/html 2>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 3
done

echo "Database is ready!"

# Check if WordPress is already installed
if ! sudo -u www-data wp core is-installed --path=/var/www/html 2>/dev/null; then
    echo "Installing WordPress..."
    
    # Install WordPress
    sudo -u www-data wp core install \
        --path=/var/www/html \
        --url=http://localhost:8080/ \
        --title="Hnutí Brontosaurus" \
        --admin_user=admin \
        --admin_password=test \
        --admin_email=admin@example.com \
        --skip-email \
        --locale=cs_CZ
    
    echo "WordPress installation completed!"
else
    echo "WordPress is already installed, skipping installation."
fi

# Create mu-plugins directory if it doesn't exist
echo "Setting up mu-plugins..."
if [ ! -d "/var/www/html/wp-content/mu-plugins" ]; then
    sudo mkdir -p /var/www/html/wp-content/mu-plugins
    echo "mu-plugins directory created!"
fi

# Create the Codespaces URL handler plugin
sudo cat > /var/www/html/wp-content/mu-plugins/codespaces-urls.php << 'EOF'
<?php
/**
 * Plugin Name: Codespaces URL Handler
 * Description: Automatically handles URLs for GitHub Codespaces environment
 * Version: 1.0
 * Author: Auto-generated
 */

if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'app.github.dev') !== false) {
    $codespace_url = 'https://' . $_SERVER['HTTP_HOST'];
    define('WP_HOME', $codespace_url);
    define('WP_SITEURL', $codespace_url);
}
EOF

# Set proper permissions
sudo chown www-data:www-data /var/www/html/wp-content/mu-plugins/codespaces-urls.php
echo "Codespaces URL handler plugin created!"

# Start Apache
echo "Starting Apache..."
sudo apache2ctl -D FOREGROUND
echo "Apache started!"

echo "WordPress initialization finished!"