#!/bin/sh

php artisan config:clear

# Espera a que la base de datos esté disponible (opcional)
# Puedes hacer un pequeño loop para esperar, o asumir que ya está lista

php artisan migrate --force
php artisan db:seed --force

php artisan storage:link --force
php artisan shield:super-admin --user=admin@gmail.com
# Finalmente arranca el servidor
php artisan serve --host=0.0.0.0 --port=8000
