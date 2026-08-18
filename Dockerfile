FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN cp .env.example .env \
    && composer install --no-interaction --optimize-autoloader \
    && php artisan key:generate

EXPOSE 8000
# The backfill is wrapped in `|| true` deliberately: it makes outbound API
# calls, and a provider outage or an exhausted quota must never stop the web
# server from booting.
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && (php artisan memories:backfill-media --limit=200 --sleep=13 || true) && php -S 0.0.0.0:${PORT:-8000} -t public"]
