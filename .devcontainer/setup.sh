#!/bin/bash

# Get the current workspace name (your theme name)
THEME_NAME=$(basename $PWD)

echo "Setting up WordPress with Docker for theme: $THEME_NAME"

# Create docker-compose.yml
cat > docker-compose.yml << EOF
version: '3.8'

services:
  wordpress:
    image: wordpress:latest
    ports:
      - "8080:80"
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
      WORDPRESS_DB_NAME: wordpress
      WORDPRESS_DEBUG: 1
    volumes:
      - .:/var/www/html/wp-content/themes/$THEME_NAME
      - wordpress_data:/var/www/html
    depends_on:
      - mysql

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: wordpress
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
      MYSQL_ROOT_PASSWORD: rootpassword
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  wordpress_data:
  mysql_data:
EOF

# Start WordPress and MySQL containers
docker-compose up -d

# Wait for containers to be ready
echo "Waiting for WordPress to be ready..."
sleep 30

# Install WP-CLI in WordPress container
docker-compose exec wordpress bash -c "
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
mv wp-cli.phar /usr/local/bin/wp
"

# Install and configure WordPress
docker-compose exec wordpress wp core install \
  --url="http://localhost:8080" \
  --title="Theme Development Site" \
  --admin_user="admin" \
  --admin_password="password" \
  --admin_email="admin@example.com" \
  --allow-root

# Activate your theme
docker-compose exec wordpress wp theme activate $THEME_NAME --allow-root

# Install development plugins
docker-compose exec wordpress wp plugin install query-monitor --activate --allow-root

# Create sample content
docker-compose exec wordpress wp post create \
  --post_title="Welcome to Your Theme" \
  --post_content="This is a sample post to test your theme." \
  --post_status=publish \
  --allow-root

echo "WordPress setup complete!"
echo "Site: http://localhost:8080"
echo "Admin: http://localhost:8080/wp-admin"
echo "Username: admin"
echo "Password: password"
echo ""
echo "Your theme '$THEME_NAME' is now active!"
echo "Edit files in this directory and refresh to see changes."
echo ""
echo "Useful commands:"
echo "  docker-compose logs -f wordpress  # View WordPress logs"
echo "  docker-compose exec wordpress wp ... # Run WP-CLI commands"
echo "  docker-compose restart            # Restart services"
echo "  docker-compose down              # Stop services"
