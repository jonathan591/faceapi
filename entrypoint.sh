#!/bin/sh

php artisan config:clear

# Espera a que la base de datos esté disponible (opcional)
# Puedes hacer un pequeño loop para esperar, o asumir que ya está lista
# Asegura que el archivo SQLite existe
touch /var/www/html/database/database.sqlite

php artisan config:clear
php artisan migrate --force
php artisan db:seed --force

php artisan storage:link --force
php artisan shield:super-admin 
# Finalmente arranca el servidor
php artisan serve --host=0.0.0.0 --port=8000
