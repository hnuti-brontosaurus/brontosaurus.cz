#!/bin/bash
set -e

echo "🚀 Setting up Brontosaurus WordPress development environment..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
while ! nc -z db 3306; do
  sleep 1
done
sleep 5

# Create the themes directory structure
echo "📁 Setting up WordPress directory structure..."
sudo mkdir -p /var/www/html/wp-content/themes/brontosaurus
sudo chown -R www-data:www-data /var/www/html/wp-content

# Symlink the current workspace to the theme directory
echo "🔗 Linking theme to WordPress..."
sudo ln -sf /workspaces/${CODESPACE_NAME:-$(basename $(pwd))} /var/www/html/wp-content/themes/brontosaurus

# Install WordPress if not already installed
if ! wp core is-installed --path=/var/www/html --allow-root; then
    echo "📦 Installing WordPress..."
    wp core install \
        --path=/var/www/html \
        --url=http://localhost:8080 \
        --title="Brontosaurus Development Site" \
        --admin_user=admin \
        --admin_password=admin \
        --admin_email=admin@brontosaurus.cz \
        --allow-root
fi

# Activate the Brontosaurus theme
echo "🎨 Activating Brontosaurus theme..."
wp theme activate brontosaurus --path=/var/www/html --allow-root

# Install backend dependencies (Composer)
WORKSPACE_DIR="/workspaces/${CODESPACE_NAME:-$(basename $(pwd))}"
if [ -f "${WORKSPACE_DIR}/composer.json" ]; then
    echo "📦 Installing PHP dependencies..."
    cd "${WORKSPACE_DIR}"
    composer install --no-dev --optimize-autoloader
fi

# Install frontend dependencies (Yarn)
if [ -f "${WORKSPACE_DIR}/package.json" ]; then
    echo "📦 Installing Node.js dependencies..."
    cd "${WORKSPACE_DIR}"
    yarn install
fi

# Set proper permissions
echo "🔐 Setting proper permissions..."
sudo chown -R www-data:www-data /var/www/html
sudo chown -R www-data:www-data "${WORKSPACE_DIR}"

# Create a sample post with the theme
echo "📝 Creating sample content..."
wp post create --post_title="Welcome to Brontosaurus Development" \
    --post_content="This is your Brontosaurus theme development environment. You can edit the theme files and see changes instantly!" \
    --post_status=publish \
    --path=/var/www/html \
    --allow-root

echo "✅ Setup complete!"
echo ""
echo "🌐 WordPress Site: http://localhost:8080"
echo "👤 Admin Login: http://localhost:8080/wp-admin (admin/admin)"
echo "🗄️  phpMyAdmin: http://localhost:8081"
echo ""
echo "🛠️  To start development:"
echo "   cd ${WORKSPACE_DIR} && yarn dev"
echo ""