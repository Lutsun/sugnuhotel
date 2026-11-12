#!/bin/sh
set -e

# Apache doit écouter sur le port fourni par l'hébergeur (PORT), 10000 par défaut.
PORT="${PORT:-10000}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

cd /var/www/html

php artisan config:clear
php artisan migrate --force
# Les seeders sont idempotents : comptes de démonstration, chambres et services.
php artisan db:seed --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache

exec apache2-foreground
