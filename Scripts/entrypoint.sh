#!/bin/bash
set -euo pipefail

mkdir -p /app/storage/framework/cache \
         /app/storage/framework/sessions \
         /app/storage/framework/views \
         /app/storage/logs

chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache

php artisan storage:link --force >/dev/null 2>&1 || true

if [ "${RUN_OPTIMIZE:-false}" = "true" ]; then
    php artisan optimize:clear >/dev/null 2>&1 || true
    php artisan optimize
fi

exec "$@"
