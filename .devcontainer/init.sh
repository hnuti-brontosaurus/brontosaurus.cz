#!/bin/bash
set -e

echo "Starting WordPress initialization..."

# Determine the site URL
if [ -n "$CODESPACES" ] && [ -n "$CODESPACE_NAME" ] && [ -n "$GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN" ]; then
    SITE_URL="https://${CODESPACE_NAME}-8080.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
    echo "Codespaces detected! Site URL: $SITE_URL"
else
    SITE_URL="http://localhost:8080"
    echo "Local environment. Site URL: $SITE_URL"
fi

# Create wp-config.php if it doesn't exist
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

# --- Codespaces URL handler setup ---
# This MUST happen after wp config create, because that command
# generates a fresh wp-config.php which would overwrite any earlier injection.

# 1. Copy the PHP snippet into the WordPress root
# Use the script's own location to find the .devcontainer directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "Copying codespaces-urls.php from $SCRIPT_DIR..."
sudo cp "$SCRIPT_DIR/codespaces-urls.php" /var/www/html/codespaces-urls.php
sudo chown www-data:www-data /var/www/html/codespaces-urls.php
echo "Codespaces URL handler copied!"

# 2. Inject require_once into wp-config.php if not already present
if ! grep -q "codespaces-urls.php" /var/www/html/wp-config.php; then
    echo "Injecting require_once into wp-config.php..."
    sudo sed -i '1 a\require_once __DIR__ . "/codespaces-urls.php";' /var/www/html/wp-config.php
    echo "Injected!"
else
    echo "require_once already present in wp-config.php"
fi

# Verify injection
echo "wp-config.php first 3 lines:"
head -3 /var/www/html/wp-config.php

# --- End Codespaces URL handler setup ---

# Wait for database
echo "Waiting for database connection..."
until sudo -u www-data wp db check --path=/var/www/html 2>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 3
done
echo "Database is ready!"

# Install or update WordPress
if ! sudo -u www-data wp core is-installed --path=/var/www/html 2>/dev/null; then
    echo "Installing WordPress..."
    sudo -u www-data wp core install \
        --path=/var/www/html \
        --url="$SITE_URL" \
        --title="Hnutí Brontosaurus" \
        --admin_user=admin \
        --admin_password=test \
        --admin_email=admin@example.com \
        --skip-email \
        --locale=cs_CZ
    echo "WordPress installation completed!"
else
    echo "WordPress is already installed, updating URLs..."
    sudo -u www-data wp option update home "$SITE_URL" --path=/var/www/html
    sudo -u www-data wp option update siteurl "$SITE_URL" --path=/var/www/html
fi

# Install theme dependencies and build
echo "Installing theme dependencies..."
cd /workspaces/brontosaurus.cz
cp config/config.local.example.neon config/config.local.neon
composer install
npm i
npm run build
echo "Theme dependencies installed and built!"

# Symlink the repo as a WordPress theme
THEME_SLUG="brontosaurus"
THEME_TARGET="/var/www/html/wp-content/themes/$THEME_SLUG"
if [ ! -e "$THEME_TARGET" ]; then
    echo "Symlinking theme as $THEME_SLUG..."
    sudo ln -s "/workspaces/brontosaurus.cz" "$THEME_TARGET"
    sudo chown -h www-data:www-data "$THEME_TARGET"
    echo "Theme symlinked!"
else
    echo "Theme symlink already exists"
fi

# Activate the theme
echo "Activating theme..."
sudo -u www-data wp theme activate "$THEME_SLUG" --path=/var/www/html
echo "Theme activated!"

# Start Apache
echo "Starting Apache..."
sudo apache2ctl -D FOREGROUND