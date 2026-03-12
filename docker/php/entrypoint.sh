#!/bin/sh
set -e
chown -R www-data:www-data /var/www/backend/storage /var/www/backend/bootstrap/cache /var/www/backend/database
chmod -R 775 /var/www/backend/storage /var/www/backend/bootstrap/cache /var/www/backend/database
exec php-fpm
