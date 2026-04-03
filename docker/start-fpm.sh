#!/bin/sh
set -e # Exit immediately if a command fails

cd /var/www/html 

MAX_RETRIES=10
SLEEP_SECONDS=2
DB_READY=0

for i in $(seq 1 "$MAX_RETRIES"); do # Loop to check if the database is up and running, with a maximum of 30 attempts.
  if mysqladmin ping \
    -h"${DB_HOST}" \
    -P"${DB_PORT:-3306}" \
    -u"${DB_USERNAME}" \
    -p"${DB_PASSWORD}" \
    --silent; then
    DB_READY=1
    break
  fi

  echo "[$i/$MAX_RETRIES] Waiting for database..."
  sleep "$SLEEP_SECONDS"
done

if [ "$DB_READY" -ne 1 ]; then
  echo "Database is not reachable after $((MAX_RETRIES * SLEEP_SECONDS)) seconds. Exiting."
  exit 1
fi

php artisan config:clear || true # Clear the configuration cache, ignoring any errors that may occur 
php artisan package:discover --ansi # Rebuild the package discovery cache to ensure that all service providers are properly registered.
php artisan migrate --force

if [ "${APP_SEED}" = "true" ]; then
  php artisan db:seed --force
fi

exec php-fpm # Start the PHP FastCGI Process Manager, replacing the current shell process with it.
