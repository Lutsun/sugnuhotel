#!/bin/sh
set -e

# Apache doit écouter sur le port fourni par l'hébergeur (PORT), 10000 par défaut.
PORT="${PORT:-10000}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

cd /var/www/html

# Les erreurs Laravel doivent apparaître dans les logs Render.
export LOG_CHANNEL="${LOG_CHANNEL:-stderr}"

php artisan config:clear
php artisan migrate --force
# Données de base (idempotentes) : services, types de chambres et chambres.
php artisan db:seed --class=ServiceSeeder --force
php artisan db:seed --class=RoomTypeSeeder --force
php artisan db:seed --class=RoomSeeder --force

# Comptes de démonstration (mots de passe publics, voir COMPTES-TEST.md) :
# créés uniquement si SEED_DEMO_USERS=true. À désactiver une fois la démo terminée.
SEED_DEMO="$(printf '%s' "${SEED_DEMO_USERS:-false}" | tr -d ' "\r' | tr 'A-Z' 'a-z')"
echo "[entrypoint] SEED_DEMO_USERS=${SEED_DEMO}"
if [ "$SEED_DEMO" = "true" ]; then
  php artisan db:seed --class=AdminUserSeeder --force
  echo "[entrypoint] Comptes de démonstration : $(php artisan tinker --execute='echo App\Models\User::count();') utilisateur(s) en base."
else
  echo "[entrypoint] Comptes de démonstration ignorés."
fi
php artisan storage:link || true
php artisan config:cache
php artisan route:cache

# Les commandes artisan tournent en root : on rend le stockage à Apache (www-data).
chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
