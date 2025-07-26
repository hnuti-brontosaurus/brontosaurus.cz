#!/bin/bash
set -e

echo "🔄 Starting development services..."

# Ensure Apache is running
if ! pgrep apache2 > /dev/null; then
    echo "🚀 Starting Apache..."
    sudo service apache2 start
fi

# Check if we're in the workspace directory and have the necessary files
if [ -d "/workspace" ] && [ -f "/workspace/package.json" ]; then
    cd /workspace
    
    # Install/update dependencies if needed
    if [ ! -d "node_modules" ] || [ "package.json" -nt "node_modules" ]; then
        echo "📦 Installing/updating Node.js dependencies..."
        yarn install
    fi
    
    if [ -f "composer.json" ] && { [ ! -d "vendor" ] || [ "composer.json" -nt "vendor" ]; }; then
        echo "📦 Installing/updating PHP dependencies..."
        composer install --no-dev --optimize-autoloader
    fi
    
    echo "✅ Services ready!"
    echo ""
    echo "🌐 WordPress Site: http://localhost:8080"
    echo "👤 Admin: http://localhost:8080/wp-admin (admin/admin)"
    echo "🗄️  phpMyAdmin: http://localhost:8081"
    echo ""
    echo "💡 Run 'yarn dev' to start the development build process with auto-reload"
else
    echo "⚠️  Workspace not properly mounted or package.json not found"
fi