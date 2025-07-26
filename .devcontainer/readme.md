# Brontosaurus WordPress Theme - Development Container

This repository includes a complete development environment using GitHub Codespaces and VS Code Dev Containers.

## 🚀 Quick Start

### Using GitHub Codespaces (Recommended)
1. Go to your repository on GitHub
2. Click the green "Code" button
3. Select "Codespaces" tab
4. Click "Create codespace on main"
5. Wait for the environment to set up (takes 3-5 minutes)

### Using VS Code Dev Containers Locally
1. Install [Docker Desktop](https://www.docker.com/products/docker-desktop)
2. Install [VS Code](https://code.visualstudio.com/) with the [Dev Containers extension](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)
3. Clone this repository
4. Open in VS Code
5. When prompted, click "Reopen in Container"

## 🌐 What You Get

Once the container is running, you'll have access to:

- **WordPress Site**: http://localhost:8080
  - Full WordPress installation with your theme activated
  - Admin access: `admin` / `admin`
  
- **phpMyAdmin**: http://localhost:8081
  - Database management interface
  - Login: `wordpress` / `wordpress`

- **Development Tools**:
  - PHP 8.2 with Composer
  - Node.js 18 with Yarn
  - All your theme dependencies pre-installed

## 🛠️ Development Workflow

### Starting Development
```bash
# Start the Gulp development server (with auto-reload)
yarn dev
```

This will:
- Watch for changes in SCSS files and compile them automatically
- Reload the browser when changes are detected
- Keep the development server running on port 3000

### Building for Production
```bash
# Build optimized assets for production
NODE_ENV=production yarn build
```

### WordPress Management
The container includes WP-CLI for WordPress management:
```bash
# Install a plugin
wp plugin install contact-form-7 --activate

# Create a new post
wp post create --post_title="My New Post" --post_status=publish

# Export/import database
wp db export backup.sql
wp db import backup.sql
```

## 📁 File Structure

Your theme files are automatically symlinked to `/var/www/html/wp-content/themes/brontosaurus/` inside the container, so any changes you make in VS Code are immediately reflected in the WordPress installation.

## 🔧 Customization

### Environment Variables
You can customize the setup by creating a `.devcontainer/docker-compose.override.yml` file:

```yaml
version: '3.8'
services:
  wordpress-dev:
    environment:
      # Add your custom environment variables
      CUSTOM_VAR: "value"
```

### Additional Dependencies
- **PHP packages**: Add to `composer.json` and run `composer install`
- **Node packages**: Add to `package.json` and run `yarn install`

## 🐛 Troubleshooting

### Container Won't Start
- Make sure Docker Desktop is running
- Try rebuilding the container: "Dev Containers: Rebuild Container"

### WordPress Installation Issues
- Check that the database container is running: `docker ps`
- Restart the setup: Run `.devcontainer/setup.sh` in the terminal

### Theme Not Appearing
- Check that files are properly symlinked: `ls -la /var/www/html/wp-content/themes/`
- Manually activate the theme: `wp theme activate brontosaurus`

### Port Conflicts
If ports 8080, 8081, or 3000 are already in use on your system:
1. Modify the ports in `.devcontainer/docker-compose.yml`
2. Update the `forwardPorts` array in `.devcontainer/devcontainer.json`
3. Rebuild the container

## 📝 Notes

- The WordPress admin credentials are `admin` / `admin` (change these for production!)
- The database is persisted in a Docker volume, so your data survives container restarts
- The development environment includes debugging enabled and error logging
- All standard WordPress and theme development tools are pre-configured

## 🎯 Next Steps

1. Start coding! Edit your theme files and see changes instantly
2. Run `yarn dev` to enable auto-compilation and browser refresh
3. Access your WordPress site at http://localhost:8080
4. Use the admin panel at http://localhost:8080/wp-admin to manage content

Happy coding! 🦕