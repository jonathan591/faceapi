FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libonig-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite mbstring gd

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia el contenido del proyecto
COPY . .

# Instala dependencias PHP del proyecto
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Crea base de datos SQLite vacía
RUN touch database/database.sqlite && chmod 664 database/database.sqlite

# Da permisos adecuados
RUN chown -R www-data:www-data /var/www && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]

