FROM mcr.microsoft.com/devcontainers/php:1-8.2-bullseye

# node js
#RUN nvm install 20

# wp cli
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp
RUN wp core download --path=/var/www/html

RUN wp core install \
    --path=/var/www/html \
    --url=http://localhost:8080 \
    --title="Hnutí Brontosaurus" \
    --admin_user=admin \
    --admin_password=test \
    --admin_email=admin@localhost \
    --skip-email \
    --locale=cs_CZ