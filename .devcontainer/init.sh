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

# Create .htaccess with environment-based URL handling
if [ ! -z "$CODESPACE_NAME" ] && [ ! -z "$GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN" ]; then
    cat > /var/www/html/.htaccess << 'EOF'
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
EOF
    chown www-data:www-data /var/www/html/.htaccess
fi

# Start Apache
echo "Starting Apache..."
sudo apache2ctl -D FOREGROUND
echo "Apache started!"

echo "WordPress initialization finished!"