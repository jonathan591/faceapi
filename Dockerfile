FROM php:8.2-fpm-alpine

# Instala extensiones PHP necesarias, incluidas intl, gd, zip, etc
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    autoconf \
    gcc \
    g++ \
    make \
    bash \
    && docker-php-ext-install intl zip pdo_mysql gd \
    && apk del icu-dev libzip-dev autoconf gcc g++ make

# Instala composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copiar permisos, configurar .env, etc si necesitas

CMD php artisan migrate --force && php artisan db:seed --force && php artisan shield:super-admin && php artisan serve --host=0.0.0.0 --port=${PORT}



