FROM php:8.2-fpm

# Instalar librerías necesarias y extensiones PHP
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

# Instalar Composer (si no está instalado)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos del proyecto
COPY . /var/www/html

WORKDIR /var/www/html

# Ejecutar composer install
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Exponer el puerto 9000 y arrancar PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]



