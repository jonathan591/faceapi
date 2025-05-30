FROM php:8.2-fpm

# Instala dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libsqlite3-dev \
    sqlite3

# Instala extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo pdo_sqlite mbstring

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia el contenido del proyecto
COPY . .

# Instala dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Crea el archivo de base de datos SQLite
RUN touch database/database.sqlite \
    && chmod 664 database/database.sqlite

# Da permisos adecuados
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Expone puerto 9000 para php-fpm
EXPOSE 9000

CMD ["php-fpm"]
