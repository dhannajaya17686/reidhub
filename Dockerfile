FROM php:8.3-fpm

WORKDIR /var/www/html

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Optionally add more extensions here:
# RUN docker-php-ext-install mbstring gd
