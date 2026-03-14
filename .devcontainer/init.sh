#!/bin/bash
set -e

echo "Starting WordPress initialization..."

WORKSPACE_DIR=$(find /workspaces -maxdepth 1 -mindepth 1 -type d | head -n 1)

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

# Always install the mu-plugin (even if WP was already installed)
echo "Setting up mu-plugins..."
sudo mkdir -p /var/www/html/wp-content/mu-plugins
sudo cp "$WORKSPACE_DIR/.devcontainer/mu-codespaces-urls.php" /var/www/html/wp-content/mu-plugins/codespaces-urls.php
sudo chown www-data:www-data /var/www/html/wp-content/mu-plugins/codespaces-urls.php
echo "Codespaces URL handler plugin installed!"

# Update WordPress URLs to match the Codespaces environment
if [ -n "$CODESPACE_NAME" ]; then
    CODESPACE_URL="https://${CODESPACE_NAME}-8080.app.github.dev"
    echo "Updating WordPress URLs to $CODESPACE_URL..."
    sudo -u www-data wp option update siteurl "$CODESPACE_URL" --path=/var/www/html
    sudo -u www-data wp option update home "$CODESPACE_URL" --path=/var/www/html
    echo "URLs updated!"
fi

# Start Apache
echo "Starting Apache..."
sudo apache2ctl -D FOREGROUND