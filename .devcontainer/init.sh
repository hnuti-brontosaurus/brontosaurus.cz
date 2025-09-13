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

# Add WordPress URL constants to wp-config.php if not already present
if ! grep -q "WP_HOME" /var/www/html/wp-config.php; then
    echo "Adding WordPress URL constants..."
    sudo -u www-data wp config set WP_HOME "https://\${CODESPACE_NAME}-8080.\${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}" --type=constant --path=/var/www/html
    sudo -u www-data wp config set WP_SITEURL "https://\${CODESPACE_NAME}-8080.\${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}" --type=constant --path=/var/www/html
    echo "WordPress URL constants added!"
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
    
    # Install WordPress with localhost URL (will be overridden by constants)
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

# Update URLs in database if codespace environment variables are available
if [ ! -z "$CODESPACE_NAME" ] && [ ! -z "$GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN" ]; then
    echo "Updating WordPress URLs for Codespaces..."
    CODESPACE_URL="https://${CODESPACE_NAME}-8080.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
    
    sudo -u www-data wp search-replace "http://localhost:8080" "$CODESPACE_URL" --path=/var/www/html --skip-columns=guid || true
    sudo -u www-data wp option update home "$CODESPACE_URL" --path=/var/www/html || true
    sudo -u www-data wp option update siteurl "$CODESPACE_URL" --path=/var/www/html || true
    
    echo "WordPress URLs updated to: $CODESPACE_URL"
fi

# Start Apache
echo "Starting Apache..."
sudo apache2ctl -D FOREGROUND
echo "Apache started!"

echo "WordPress initialization finished!"