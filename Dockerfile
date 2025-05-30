# Usar imagen base oficial PHP con FPM
FROM php:8.2-fpm

# Instalar dependencias necesarias para intl y zip, además de otros útiles
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl zip pdo_mysql

# Instalar Composer (opcional, si no tienes Composer ya instalado)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos del proyecto al contenedor (ajusta según tu estructura)
COPY . /var/www/html

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias PHP vía Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ajustar permisos si es necesario
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto (opcional)
EXPOSE 9000

# Comando para correr PHP-FPM
CMD ["php-fpm"]


