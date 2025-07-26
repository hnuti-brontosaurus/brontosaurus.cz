FROM wordpress:6.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    wget \
    netcat-traditional \
    vim \
    less \
    procps \
    sudo \
    gnupg \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 18 and Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/wp-cli/v2.8.1/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Enable Apache modules
RUN a2enmod rewrite headers

# Set proper permissions for www-data user and add to sudo
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && usermod -aG sudo www-data \
    && echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

# Create workspace directory
RUN mkdir -p /workspaces && chown -R www-data:www-data /workspaces

# Custom WordPress configuration
COPY wp-config-extra.php /usr/local/etc/php/conf.d/

# Custom Apache configuration
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /workspaces

# Switch to www-data user for development
USER www-data