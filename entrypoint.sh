#!/bin/bash
set -e

# Copy .env if not present
if [ ! -f /app/.env ]; then
    cp /app/.env.example /app/.env
fi

# Install dependencies if vendor is missing
if [ ! -d /app/vendor ]; then
    composer install --no-interaction --optimize-autoloader
fi

# Generate app key if not set
php artisan key:generate --no-interaction 2>/dev/null || true

# Wait for DB to be ready
echo "Waiting for database..."
until php artisan migrate --force 2>/dev/null; do
    echo "DB not ready, retrying in 2s..."
    sleep 2
done

# Seed tags if empty
php artisan db:seed --force --no-interaction 2>/dev/null || true

echo "🏀 Hardwood is ready at http://localhost:8000"
php artisan serve --host=0.0.0.0 --port=8000
