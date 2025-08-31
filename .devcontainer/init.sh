#!/bin/bash
set -e

echo "Starting WordPress initialization..."

# Wait for database to be ready
echo "Waiting for database connection..."
until wp db check --path=/var/www/html 2>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 3
done

echo "Database is ready!"

# Check if WordPress is already installed
if ! wp core is-installed --path=/var/www/html 2>/dev/null; then
    echo "Installing WordPress..."
    
    # Create wp-config.php if it doesn't exist
    if [ ! -f "/var/www/html/wp-config.php" ]; then
        echo "Creating wp-config.php..."
        wp config create \
            --dbname=wordpress \
            --dbuser=root \
            --dbpass= \
            --dbhost=database \
            --path=/var/www/html
    fi
    
    # Install WordPress
    wp core install \
        --path=/var/www/html \
        --url=http://localhost:8080 \
        --title="Hnutí Brontosaurus" \
        --admin_user=admin \
        --admin_password=test \
        --admin_email=admin@localhost \
        --skip-email \
        --locale=cs_CZ
    
    echo "WordPress installation completed!"
else
    echo "WordPress is already installed, skipping installation."
fi

echo "WordPress initialization finished!"