#!/bin/bash

# Script to activate your theme and set up WordPress
THEME_NAME=$(basename $PWD)

echo "Activating theme: $THEME_NAME"

# Wait for WordPress to be ready
sleep 5

# Install WordPress (if not already installed)
if ! wp core is-installed --path=/var/www/html --allow-root 2>/dev/null; then
    echo "Installing WordPress..."
    wp core install \
        --path=/var/www/html \
        --url="http://localhost:8080" \
        --title="Hnutí Brontosaurus" \
        --admin_user="admin" \
        --admin_password="test" \
        --admin_email="admin@example.com" \
        --allow-root
fi

# Activate your theme
wp theme activate $THEME_NAME --path=/var/www/html --allow-root

# Install useful plugins for theme development
wp plugin install query-monitor --activate --path=/var/www/html --allow-root
wp plugin install debug-bar --activate --path=/var/www/html --allow-root

echo "Theme '$THEME_NAME' activated successfully!"
echo "WordPress Admin: http://localhost:8080/wp-admin"
echo "Username: admin"
echo "Password: test"
