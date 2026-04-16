FROM php:8.3-fpm

WORKDIR /var/www/html

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Create storage directories with proper permissions
RUN mkdir -p /var/www/html/storage/filestore/marketplace && \
    mkdir -p /var/www/html/storage/filestore/clubs && \
    mkdir -p /var/www/html/storage/filestore/events && \
    mkdir -p /var/www/html/storage/filestore/orders && \
    mkdir -p /var/www/html/storage/filestore/chats && \
    mkdir -p /var/www/html/storage/logs && \
    chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 777 /var/www/html/storage

# Optionally add more extensions here:
# RUN docker-php-ext-install mbstring gd
