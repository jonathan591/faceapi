FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones PHP necesarias
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

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos de la aplicación al contenedor
COPY . /var/www/html

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias PHP con Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ejecutar migraciones, seeders y crear super admin


# Ajustar permisos si es necesario (opcional, dependiendo de tu setup)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache



# Comando para iniciar PHP-FPM
CMD php artisan migrate --force && php artisan db:seed --force && php artisan shield:super-admin && php artisan serve --host=0.0.0.0 --port=${PORT}



