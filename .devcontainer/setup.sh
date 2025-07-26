#!/bin/bash
set -e

echo "🚀 Setting up Brontosaurus WordPress development environment..."

# Ensure we're running as the correct user context
echo "👤 Current user: $(whoami)"

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

# Get the workspace directory
WORKSPACE_DIR="/workspaces/${CODESPACE_NAME:-$(basename $(pwd))}"
echo "📂 Workspace directory: ${WORKSPACE_DIR}"

# Symlink the current workspace to the theme directory
echo "🔗 Linking theme to WordPress..."
sudo ln -sf "${WORKSPACE_DIR}" /var/www/html/wp-content/themes/brontosaurus

# Install WordPress if not already installed
if ! sudo -u www-data wp core is-installed --path=/var/www/html; then
    echo "📦 Installing WordPress..."
    sudo -u www-data wp core install \
        --path=/var/www/html \
        --url=http://localhost:8080 \
        --title="Brontosaurus Development Site" \
        --admin_user=admin \
        --admin_password=admin \
        --admin_email=admin@brontosaurus.cz
fi

# Activate the Brontosaurus theme
echo "🎨 Activating Brontosaurus theme..."
sudo -u www-data wp theme activate brontosaurus --path=/var/www/html

# Install backend dependencies (Composer)
if [ -f "${WORKSPACE_DIR}/composer.json" ]; then
    echo "📦 Installing PHP dependencies..."
    cd "${WORKSPACE_DIR}"
    sudo -u www-data composer install --no-dev --optimize-autoloader
fi

# Install frontend dependencies (Yarn)
if [ -f "${WORKSPACE_DIR}/package.json" ]; then
    echo "📦 Installing Node.js dependencies..."
    cd "${WORKSPACE_DIR}"
    sudo -u www-data yarn install
fi

# Set proper permissions
echo "🔐 Setting proper permissions..."
sudo chown -R www-data:www-data /var/www/html
sudo chown -R www-data:www-data "${WORKSPACE_DIR}"

# Create a sample post with the theme
echo "📝 Creating sample content..."
sudo -u www-data wp post create --post_title="Welcome to Brontosaurus Development" \
    --post_content="This is your Brontosaurus theme development environment. You can edit the theme files and see changes instantly!" \
    --post_status=publish \
    --path=/var/www/html

echo "✅ Setup complete!"
echo ""
echo "🌐 WordPress Site: http://localhost:8080"
echo "👤 Admin Login: http://localhost:8080/wp-admin (admin/admin)"
echo "🗄️  phpMyAdmin: http://localhost:8081"
echo ""
echo "🛠️  To start development:"
echo "   cd ${WORKSPACE_DIR} && yarn dev"
echo ""