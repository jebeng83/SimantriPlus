#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$(cd -- "${SCRIPT_DIR}/.." && pwd)"
LOCK_FILE="${APP_DIR}/storage/logs/deploy.lock"
BRANCH="${DEPLOY_WEBHOOK_BRANCH:-master}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
SKIP_NPM_BUILD="${DEPLOY_SKIP_NPM_BUILD:-false}"
RESTART_COMMAND="${DEPLOY_RESTART_COMMAND:-}"

log() {
    printf '%s [deploy-edokter] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$1"
}

mkdir -p "$(dirname "$LOCK_FILE")"
exec 200>"$LOCK_FILE"

if ! flock -n 200; then
    log "Deploy lain masih berjalan, request ini diabaikan."
    exit 0
fi

cd "$APP_DIR"

log "Mulai deploy branch ${BRANCH}"
git fetch origin "$BRANCH"
git pull --ff-only origin "$BRANCH"

if ! command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
    log "Gagal: composer tidak ditemukan."
    exit 1
fi

"$COMPOSER_BIN" install --no-interaction --no-dev --prefer-dist --optimize-autoloader

if [[ -f package.json && "$SKIP_NPM_BUILD" != "true" ]]; then
    if command -v "$NPM_BIN" >/dev/null 2>&1; then
        if [[ -f package-lock.json ]]; then
            "$NPM_BIN" ci
        else
            "$NPM_BIN" install
        fi
        "$NPM_BIN" run build
    else
        log "npm tidak ditemukan, build frontend dilewati."
    fi
fi

"$PHP_BIN" artisan migrate --force
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache || true
"$PHP_BIN" artisan view:cache || true
"$PHP_BIN" artisan event:cache || true

if "$PHP_BIN" artisan list --raw 2>/dev/null | grep -q '^octane:reload'; then
    "$PHP_BIN" artisan octane:reload || true
fi

if [[ -n "$RESTART_COMMAND" ]]; then
    bash -lc "$RESTART_COMMAND" || true
fi

log "Deploy selesai."
