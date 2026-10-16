#!/usr/bin/env bash
#
# Runs on the server from inside a freshly uploaded release:
#
#   <base>/releases/<sha>/scripts/activate-release.sh
#
# Links the shared storage and .env into the release, runs migrations and
# Laravel's caches, then atomically points <base>/current at the release.
# Rolling back is re-pointing that symlink at an older release.
#
# Expects <base>/shared/.env to exist (created once by hand).
# PHP_BIN overrides the PHP binary, e.g. /opt/plesk/php/8.3/bin/php.

set -euo pipefail

RELEASE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BASE_DIR="$(cd "$RELEASE_DIR/../.." && pwd)"
SHARED_DIR="$BASE_DIR/shared"
PHP="${PHP_BIN:-php}"
KEEP_RELEASES=3

if [[ ! -f "$SHARED_DIR/.env" ]]; then
    echo "Missing $SHARED_DIR/.env. Create it once before the first deploy." >&2
    exit 1
fi

echo "Activating $(basename "$RELEASE_DIR")"

# Shared, persistent state: storage (logs, sessions, uploads) and the SQLite database.
mkdir -p "$SHARED_DIR/storage/app/public" \
         "$SHARED_DIR/storage/framework/cache/data" \
         "$SHARED_DIR/storage/framework/sessions" \
         "$SHARED_DIR/storage/framework/views" \
         "$SHARED_DIR/storage/logs" \
         "$SHARED_DIR/database"
touch "$SHARED_DIR/database/database.sqlite"

rm -rf "$RELEASE_DIR/storage"
ln -sfn "$SHARED_DIR/storage" "$RELEASE_DIR/storage"
ln -sfn "$SHARED_DIR/.env" "$RELEASE_DIR/.env"

cd "$RELEASE_DIR"
"$PHP" artisan migrate --force
"$PHP" artisan optimize

# Atomic switch: create the new link next to the old one, then rename over it.
ln -sfn "$RELEASE_DIR" "$BASE_DIR/current.next"
mv -Tf "$BASE_DIR/current.next" "$BASE_DIR/current"

# Keep the newest few releases for rollback.
cd "$BASE_DIR/releases"
ls -1t | tail -n +$((KEEP_RELEASES + 1)) | xargs -r rm -rf --

echo "Live: $RELEASE_DIR"
