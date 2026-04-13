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

# Helper: create a page only if it doesn't exist (matched by slug)
# Usage: PAGE_ID=$(create_or_get_page "Title" "slug")
create_or_get_page() {
    local title="$1"
    local slug="$2"
    local existing_id
    existing_id=$(sudo -u www-data wp post list \
        --post_type=page \
        --name="$slug" \
        --field=ID \
        --path=/var/www/html 2>/dev/null)

    if [ -n "$existing_id" ]; then
        echo "$existing_id"
    else
        sudo -u www-data wp post create \
            --post_type=page \
            --post_title="$title" \
            --post_name="$slug" \
            --post_content="" \
            --post_status=publish \
            --porcelain \
            --path=/var/www/html
    fi
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
    id=$(create_or_get_page "$title" "$slug")
    PAGE_IDS["$slug"]="$id"
    echo "Page '$title' (slug: $slug) → ID: $id"
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

# Create WordPress menus
echo "Creating WordPress menus..."

# Temporarily disable set -e for menu operations to continue on errors
set +e

# Helper function to create menu and assign to location
create_and_assign_menu() {
    local menu_name="$1"
    local location="$2"
    local menu_id=""
    
    # Try to get existing menu ID by name
    while IFS= read -r line; do
        # Skip header row if present
        if [[ "$line" == term_id* ]]; then
            continue
        fi
        if [[ "$line" == *"$menu_name"* ]]; then
            # Extract the term_id (first field in CSV)
            menu_id=$(echo "$line" | cut -d',' -f1 | tr -d ' "')
            break
        fi
    done < <(sudo -u www-data wp menu list --fields=term_id,name --format=csv --path=/var/www/html 2>/dev/null)
    
    if [ -z "$menu_id" ]; then
        # Menu doesn't exist, create it
        echo "Creating menu: $menu_name"
        menu_id=$(sudo -u www-data wp menu create "$menu_name" --porcelain --path=/var/www/html 2>/dev/null || echo "")
        if [ -n "$menu_id" ]; then
            echo "Menu '$menu_name' created with ID: $menu_id"
        else
            echo "ERROR: Failed to create menu '$menu_name'"
            return 1
        fi
    else
        echo "Menu '$menu_name' already exists with ID: $menu_id"
    fi
    
    # Assign menu to location
    if [ -n "$menu_id" ]; then
        echo "Assigning menu '$menu_name' to location '$location'"
        sudo -u www-data wp menu location assign "$menu_id" "$location" --path=/var/www/html 2>/dev/null || true
    fi
    
    echo "$menu_id"
}

# Helper function to add pages to menu
add_pages_to_menu() {
    local menu_id="$1"
    local menu_name="$2"
    shift 2
    local page_slugs=("$@")
    
    if [ -z "$menu_id" ]; then
        echo "⚠ Skipping menu '$menu_name' - no valid menu ID"
        return
    fi
    
    echo "Adding pages to menu '$menu_name' (ID: $menu_id)..."

    # Cache existing menu item object IDs for idempotency
    local existing_item_ids
    existing_item_ids=$(sudo -u www-data wp menu item list --menu_id="$menu_id" --field=object_id --format=csv --path=/var/www/html 2>/dev/null | tr -d '"')

    for slug in "${page_slugs[@]}"; do
        # Look up page ID by slug directly from database
        local page_id
        page_id=$(sudo -u www-data wp post list --post_type=page --name="$slug" --field=ID --path=/var/www/html 2>/dev/null | grep -v '^$' | head -1)

        if [ -z "$page_id" ]; then
            echo "  ⚠ Page slug '$slug' not found"
            continue
        fi

        if [[ ",${existing_item_ids}," == *",$page_id,"* ]]; then
            echo "  · Page '$slug' (ID: $page_id) is already in menu"
            continue
        fi

        sudo -u www-data wp menu item add-post "$menu_id" "$page_id" --path=/var/www/html >/dev/null 2>&1
        if [ $? -eq 0 ]; then
            echo "  ✓ Added page '$slug' (ID: $page_id)"
        else
            echo "  ✗ Failed to add page '$slug' (ID: $page_id)"
        fi
    done
}

# Define page slugs for each menu
declare -a HEADER_MENU_PAGES=(
    "dobrovolnicke-akce"
    "kurzy-a-prednasky"
    "setkavani-a-kluby"
    "pro-deti"
    "zapoj-se"
    "podpor-nas"
    "o-brontosaurovi"
    "kontakty"
)

declare -a FOOTER_LEFT_MENU_PAGES=(
    "dobrovolnicke-akce"
    "kurzy-a-prednasky"
    "setkavani-a-kluby"
    "pro-deti"
    "zapoj-se"
    "o-brontosaurovi"
    "kontakty"
)

declare -a FOOTER_CENTER_MENU_PAGES=(
    "co-je-noveho"
    "co-se-chysta"
    "pro-organizatory"
    "programy-pro-stredni-skoly"
    "pronajmy"
)

declare -a FOOTER_RIGHT_MENU_PAGES=(
    "podpor-nas"
    "newsletter"
    "english"
)

# Create all four menus and get their IDs
HEADER_MENU_ID=$(create_and_assign_menu "Hlavní navigace" "header" || echo "")
FOOTER_LEFT_MENU_ID=$(create_and_assign_menu "Patička – vlevo" "footer-left" || echo "")
FOOTER_CENTER_MENU_ID=$(create_and_assign_menu "Patička – uprostřed" "footer-center" || echo "")
FOOTER_RIGHT_MENU_ID=$(create_and_assign_menu "Patička – vpravo" "footer-right" || echo "")

# Debug: Show what menu IDs we got
echo "Menu IDs: Header=$HEADER_MENU_ID, Footer-Left=$FOOTER_LEFT_MENU_ID, Footer-Center=$FOOTER_CENTER_MENU_ID, Footer-Right=$FOOTER_RIGHT_MENU_ID"

# Verify we have valid menu IDs before proceeding
if [ -z "$HEADER_MENU_ID" ] || [ -z "$FOOTER_LEFT_MENU_ID" ] || [ -z "$FOOTER_CENTER_MENU_ID" ] || [ -z "$FOOTER_RIGHT_MENU_ID" ]; then
    echo "WARNING: Some menus failed to create. Attempting recovery..."
    # List all menus to see current state
    sudo -u www-data wp menu list --format=table --path=/var/www/html 2>/dev/null || true
fi

# Assign pages to menus
add_pages_to_menu "$HEADER_MENU_ID" "Hlavní navigace" "${HEADER_MENU_PAGES[@]}"
add_pages_to_menu "$FOOTER_LEFT_MENU_ID" "Patička – vlevo" "${FOOTER_LEFT_MENU_PAGES[@]}"
add_pages_to_menu "$FOOTER_CENTER_MENU_ID" "Patička – uprostřed" "${FOOTER_CENTER_MENU_PAGES[@]}"
add_pages_to_menu "$FOOTER_RIGHT_MENU_ID" "Patička – vpravo" "${FOOTER_RIGHT_MENU_PAGES[@]}"

echo "WordPress menus created, assigned to locations, and populated with pages!"

# Re-enable set -e for remaining operations
set -e

# Start Apache
echo "Starting Apache..."
sudo apache2ctl -D FOREGROUND