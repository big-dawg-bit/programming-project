#!/bin/sh
set -e

# .env aanmaken als die nog niet bestaat (compose-env overschrijft DB_* sowieso)
if [ ! -f .env ]; then
    cp .env.example .env
fi

# APP_KEY genereren als die leeg is
if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate --force
fi

# Wachten tot MySQL klaar is
echo "Wachten op database..."
until php -r "new PDO('mysql:host=db;port=3306', 'root', 'secret');" 2>/dev/null; do
    sleep 2
done
echo "Database bereikbaar."

# Stale caches wissen (kunnen van de host zijn meegekomen) - geen route:cache
# wegens de closure-route in web.php
php artisan optimize:clear

# Migraties + seeders draaien
php artisan migrate --force --seed

exec "$@"
