#!/bin/sh
set -e

cd /var/www/html

MAX_RETRIES=10
SLEEP_SECONDS=2
DB_READY=0

for i in $(seq 1 "$MAX_RETRIES"); do
  if mysqladmin ping \
    -h"${DB_HOST}" \
    -P"${DB_PORT:-3306}" \
    -u"${DB_USERNAME}" \
    -p"${DB_PASSWORD}" \
    --silent; then
    DB_READY=1
    break
  fi
  echo "Waiting for database..."
  sleep "$SLEEP_SECONDS"
done

if [ "$DB_READY" -ne 1 ]; then
  echo "Database is not reachable after $((MAX_RETRIES * SLEEP_SECONDS)) seconds. Exiting."
  exit 1
fi

php artisan package:discover --ansi

DB_READY=0

for i in $(seq 1 "$MAX_RETRIES"); do
  if mysql \
    -h"${DB_HOST}" \
    -P"${DB_PORT:-3306}" \
    -u"${DB_USERNAME}" \
    -p"${DB_PASSWORD}" \
    -N -e "USE ${DB_DATABASE}; SHOW TABLES LIKE 'migrations';" | grep -q migrations; then
    DB_READY=1
    break
  fi
  echo "Waiting for migrations..."
  sleep "$SLEEP_SECONDS"
done

if [ "$DB_READY" -ne 1 ]; then
  echo "Migrations are not ready after $((MAX_RETRIES * SLEEP_SECONDS)) seconds. Exiting."
  exit 1
fi

# listen for new jobs on the queue and process them as they come in. The --tries=1 option means that if a job fails, it will not be retried, and the --timeout=0 option means that there is no time limit for how long a job can run before it is considered failed.
exec php artisan queue:listen --tries=1 --timeout=0
