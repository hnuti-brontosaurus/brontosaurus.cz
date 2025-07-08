#!/bin/bash

# Get the current workspace name (your theme name)
THEME_NAME=$(basename $PWD)
WORKSPACE_DIR="/workspaces/$THEME_NAME"

echo "Setting up WordPress with your theme: $THEME_NAME"

# Install Apache and MariaDB (MySQL alternative)
sudo apt-get update
sudo apt-get install -y apache2 mariadb-server

# Add PHP repository and install PHP with Apache module
sudo apt-get install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get install -y php8.1 libapache2-mod-php8.1 php8.1-mysqli php8.1-mysql php8.1-curl php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip

# Configure Apache
sudo a2enmod rewrite
sudo service apache2 start

# Configure MariaDB (MySQL alternative)
sudo service mariadb start
sudo mysql -e "CREATE DATABASE wordpress;"
sudo mysql -e "CREATE USER 'wpuser'@'localhost' IDENTIFIED BY 'password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON wordpress.* TO 'wpuser'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Download and setup WordPress
cd /tmp
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz
sudo cp -r wordpress/* /var/www/html/
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/

# Create wp-config.php with proper debug settings
sudo cp /var/www/html/wp-config-sample.php /var/www/html/wp-config.php
sudo sed -i "s/database_name_here/wordpress/" /var/www/html/wp-config.php
sudo sed -i "s/username_here/wpuser/" /var/www/html/wp-config.php
sudo sed -i "s/password_here/password/" /var/www/html/wp-config.php

# Add debug settings only if they don't exist
if ! grep -q "WP_DEBUG" /var/www/html/wp-config.php; then
    sudo sed -i "/\/\*.*stop editing.*\*\//i\\
define('WP_DEBUG', true);\\
define('WP_DEBUG_LOG', true);\\
define('WP_DEBUG_DISPLAY', false);\\
" /var/www/html/wp-config.php
fi

# Remove default themes to avoid confusion
sudo rm -rf /var/www/html/wp-content/themes/twenty*

# Create symlink so your current repository becomes the active theme
sudo ln -s $WORKSPACE_DIR /var/www/html/wp-content/themes/$THEME_NAME

# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/wp-content/themes/$THEME_NAME
sudo chmod -R 755 $WORKSPACE_DIR

# Configure Apache to serve WordPress
sudo tee /etc/apache2/sites-available/000-default.conf > /dev/null <<EOF
<VirtualHost *:80>
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Enable PHP extensions and Apache PHP module
sudo phpenmod mysqli
sudo phpenmod mysql
sudo a2enmod php8.1

# Start services
sudo service apache2 restart
sudo service mariadb restart

# Install WP-CLI for easier WordPress management
cd /tmp
wget https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp

echo "WordPress setup complete!"
echo "Access your site at: http://localhost:8080"
echo "Your theme '$THEME_NAME' is now active in WordPress"
echo "All theme files in this repository are live - edit and refresh to see changes!"

# Run theme activation script
bash .devcontainer/activate-theme.sh
