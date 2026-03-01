FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
