#!/usr/bin/env bash
#
# Zero-fuss deploy script for the VPS.
#
# Rebuilds the image and refreshes the running stack. The `app-code` volume is
# recreated so the containers pick up the freshly built code, while the MySQL,
# Redis and uploaded-file (app-storage) volumes are preserved.
#
# Usage (on the VPS, from the project root):
#     ./deploy.sh
#
set -euo pipefail

cd "$(dirname "$0")"

if [ ! -f .env ]; then
    echo "ERROR: .env not found. Copy .env.production.example to .env first."
    exit 1
fi

echo "==> Pulling latest code"
git pull --ff-only || echo "(skipping git pull — not a git checkout or no upstream)"

echo "==> Building image"
docker compose build

echo "==> Stopping app containers and clearing stale code volume"
# Remove the code-bearing containers + the app-code volume so new code is
# re-seeded from the rebuilt image. Data volumes (mysql/redis/storage) survive.
docker compose rm -sf app queue scheduler nginx
docker volume rm "$(basename "$PWD")_app-code" 2>/dev/null || true

echo "==> Starting stack"
docker compose up -d

echo "==> Cleaning up dangling images"
docker image prune -f >/dev/null 2>&1 || true

echo "==> Done. Recent app logs:"
docker compose logs --tail=30 app
