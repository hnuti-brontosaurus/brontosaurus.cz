FROM mcr.microsoft.com/devcontainers/php:1-8.2-bullseye

# node js
#RUN nvm install 20

# wp cli
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp

RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/memory-limit.ini

RUN echo "vscode ALL=(www-data) NOPASSWD: /usr/local/bin/wp" >> /etc/sudoers.d/wp-cli

RUN wp core download --path=/var/www/html --allow-root
RUN chown -R www-data:www-data /var/www/html
