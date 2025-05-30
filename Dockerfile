FROM php:8.2-fpm

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip pdo_mysql gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ejecutar migraciones y configuración inicial
RUN php artisan config:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan shield:super-admin && \
    php artisan storage:link

# Exponer puerto que Render espera
EXPOSE 8000

# Arrancar Laravel en modo HTTP
CMD php artisan serve --host 0.0.0.0 --port=8000




