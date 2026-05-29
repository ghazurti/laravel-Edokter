#!/bin/sh
set -e

cd /var/www/html

# Generate APP_KEY kalau belum ada
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then
    php artisan key:generate --force || true
fi

# Permission
chown -R www-data:www-data storage bootstrap/cache public/webapps 2>/dev/null || true
chmod -R 775 storage bootstrap/cache public/webapps 2>/dev/null || true

# Cache config & route untuk production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
