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
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
echo "Copying codespaces-urls.php from $SCRIPT_DIR..."
sudo cp "$SCRIPT_DIR/codespaces-urls.php" /var/www/html/codespaces-urls.php
sudo chown www-data:www-data /var/www/html/codespaces-urls.php
echo "Codespaces URL handler copied!"

if ! grep -q "codespaces-urls.php" /var/www/html/wp-config.php; then
    echo "Injecting require_once into wp-config.php..."
    sudo sed -i '1 a\require_once __DIR__ . "/codespaces-urls.php";' /var/www/html/wp-config.php
    echo "Injected!"
else
    echo "require_once already present in wp-config.php"
fi

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

# Helper: create a page and return its ID
# Usage: PAGE_ID=$(create_page "Title" "slug")
create_page() {
    local title="$1"
    local slug="$2"
    sudo -u www-data wp post create \
        --post_type=page \
        --post_title="$title" \
        --post_name="$slug" \
        --post_content="<p></p>" \
        --post_status=publish \
        --porcelain \
        --path=/var/www/html
}

# Create pages and store their IDs in an associative array
declare -A PAGE_IDS

declare -a POSTS=(
    "Hlavní stránka:hlavni-stranka"
    "Dobrovolnické akce:dobrovolnicke-akce"
    "Kurzy a přednášky:kurzy-a-prednasky"
    "Setkávání a kluby:setkavani-a-kluby"
    "Pro děti:pro-deti"
    "Zapoj se:zapoj-se"
    "Podpoř nás:podpor-nas"
    "O Brontosaurovi:o-brontosaurovi"
    "Kontakty:kontakty"
    "Co je nového:co-je-noveho"
    "Co se chystá:co-se-chysta"
    "Pro organizátory:pro-organizatory"
    "Programy pro SŠ:programy-pro-stredni-skoly"
    "Pronájmy:pronajmy"
    "Newsletter:newsletter"
    "English:english"
    "Jedu poprvé:jedu-poprve"
)

for post in "${POSTS[@]}"; do
    title="${post%:*}"
    slug="${post#*:}"
    id=$(create_page "$title" "$slug")
    PAGE_IDS["$slug"]="$id"
    echo "Created page '$title' (slug: $slug) with ID: $id"
done

# Now you can reference any page by slug, e.g.:
# PAGE_IDS["hlavni-stranka"], PAGE_IDS["kontakty"], etc.

# Set homepage as the front page
sudo -u www-data wp option update show_on_front page --path=/var/www/html
sudo -u www-data wp option update page_on_front "${PAGE_IDS["hlavni-stranka"]}" --path=/var/www/html
echo "Front page set to: ${PAGE_IDS["hlavni-stranka"]}"

# Set permalinks
sudo -u www-data wp rewrite structure '/%postname%/' --path=/var/www/html
sudo -u www-data wp rewrite flush --path=/var/www/html
# After the rewrite flush lines, add:
sudo tee /var/www/html/.htaccess > /dev/null <<'EOF'
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
EOF
sudo chown www-data:www-data /var/www/html/.htaccess

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