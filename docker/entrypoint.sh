#!/bin/sh
set -e

# Apache doit écouter sur le port fourni par l'hébergeur (PORT), 10000 par défaut.
PORT="${PORT:-10000}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

cd /var/www/html

php artisan config:clear
php artisan migrate --force
# Données de base (idempotentes) : services, types de chambres et chambres.
php artisan db:seed --class=ServiceSeeder --force
php artisan db:seed --class=RoomTypeSeeder --force
php artisan db:seed --class=RoomSeeder --force

# Comptes de démonstration (mots de passe publics, voir COMPTES-TEST.md) :
# créés uniquement si SEED_DEMO_USERS=true. À désactiver une fois la démo terminée.
if [ "${SEED_DEMO_USERS:-false}" = "true" ]; then
  php artisan db:seed --class=AdminUserSeeder --force
fi
php artisan storage:link || true
php artisan config:cache
php artisan route:cache

exec apache2-foreground
