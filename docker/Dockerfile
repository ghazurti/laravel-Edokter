FROM php:8.2-fpm-alpine

# System dependencies
RUN apk add --no-cache \
    nginx supervisor \
    git curl zip unzip \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev oniguruma-dev icu-dev \
    mariadb-client tzdata bash \
    && cp /usr/share/zoneinfo/Asia/Makassar /etc/localtime \
    && echo "Asia/Makassar" > /etc/timezone

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mysqli mbstring zip exif pcntl bcmath gd intl opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Workdir
WORKDIR /var/www/html

# Copy source
COPY . /var/www/html

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && mkdir -p /var/www/html/public/webapps/berkasrawat/pages/upload \
    && chmod -R 775 /var/www/html/public/webapps

# Nginx & supervisor config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
