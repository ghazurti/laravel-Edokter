#!/bin/sh
set -e

cd /var/www/html

echo "[entrypoint] Memulai E-Dokter container..."

# 1. Pastikan .env ada — kalau tidak, copy dari .env.example
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        echo "[entrypoint] .env belum ada, copy dari .env.example..."
        cp .env.example .env
    else
        echo "[entrypoint] WARNING: .env dan .env.example tidak ada, skip key:generate."
    fi
fi

# 2. Generate APP_KEY kalau ada .env tapi kosong
if [ -f .env ]; then
    if ! grep -qE "^APP_KEY=base64:.+" .env; then
        echo "[entrypoint] APP_KEY kosong, generate baru..."
        php artisan key:generate --force 2>/dev/null || echo "[entrypoint] key:generate gagal (cek .env)"
    fi
fi

# 3. Set permission storage & cache
echo "[entrypoint] Set permission storage & bootstrap/cache..."
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 4. Permission folder upload berkas (kalau ada)
if [ -d public/webapps ]; then
    chown -R www-data:www-data public/webapps 2>/dev/null || true
    chmod -R 775 public/webapps 2>/dev/null || true
fi

# 5. Clear cache lama (in case ada perubahan .env)
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# 6. Cache untuk production (skip kalau gagal supaya container tetap jalan)
echo "[entrypoint] Cache config & route untuk production..."
php artisan config:cache 2>/dev/null || echo "[entrypoint] config:cache skipped"
php artisan route:cache 2>/dev/null || echo "[entrypoint] route:cache skipped"
php artisan view:cache 2>/dev/null || echo "[entrypoint] view:cache skipped"

echo "[entrypoint] Siap. Menjalankan supervisor..."
exec "$@"
