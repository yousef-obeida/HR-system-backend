#!/bin/sh
set -e

# ---------------------------------------------------------------------------
# Container entrypoint for the HR System (Laravel) image.
#
# Shared by the web (php-fpm), queue and scheduler containers. Heavy one-time
# bootstrap work (migrations, caching) is guarded by env flags so it only runs
# where it should and never races across multiple replicas.
# ---------------------------------------------------------------------------

cd /var/www/html

# Wait for the database to accept connections before doing anything that needs it.
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database at ${DB_HOST}:${DB_PORT:-3306} ..."
    until php -r "exit(@fsockopen(getenv('DB_HOST'), (int)(getenv('DB_PORT') ?: 3306)) ? 0 : 1);" 2>/dev/null; do
        echo "  database not ready yet, retrying in 2s..."
        sleep 2
    done
    echo "Database is up."
fi

# APP_KEY must be provided via the environment (the .env file is intentionally
# not baked into the image). A per-container generated key would differ between
# the web/queue/scheduler replicas and break sessions & encrypted data, so we
# fail fast instead of guessing one.
if [ -z "$APP_KEY" ]; then
    echo "FATAL: APP_KEY is not set. Generate one and add it to your .env:" >&2
    echo "       docker compose run --rm --no-deps --entrypoint php app artisan key:generate --show" >&2
    exit 1
fi

# Symlink storage -> public/storage so uploaded CVs are publicly accessible.
php artisan storage:link || true

# Run migrations only on the container explicitly designated for it (the web
# service). RUN_MIGRATIONS=true is set on that service in docker-compose.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

# Cache framework config/routes/views/events for performance. Rebuilt on every
# container start so a fresh deploy always reflects the current code & env.
echo "Optimizing application caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache || true
php artisan event:cache || true

exec "$@"
