#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$(cd -- "${SCRIPT_DIR}/.." && pwd)"
QUEUE_FILE="${DEPLOY_QUEUE_PATH:-${APP_DIR}/storage/app/deploy-webhook.queue}"
LOG_FILE="${DEPLOY_LOG_PATH:-${APP_DIR}/storage/logs/deploy.log}"
LOCK_FILE="${APP_DIR}/storage/logs/deploy-consumer.lock"
DEPLOY_SCRIPT="${APP_DIR}/deploy/deploy.sh"

mkdir -p "${APP_DIR}/storage/app" "${APP_DIR}/storage/logs"
touch "$QUEUE_FILE" "$LOG_FILE"

exec 200>"$LOCK_FILE"
if ! flock -n 200; then
    exit 0
fi

if [[ ! -s "$QUEUE_FILE" ]]; then
    exit 0
fi

latest_request="$(tail -n 1 "$QUEUE_FILE" || true)"
: > "$QUEUE_FILE"

printf '%s [deploy-consumer] Menjalankan deploy dari antrean: %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$latest_request" >> "$LOG_FILE"

skip_npm_build="$(printf '%s' "$latest_request" | php -r '$j=json_decode(stream_get_contents(STDIN), true); echo (!empty($j["skip_npm_build"]) ? "true" : "false");' 2>/dev/null || echo "false")"

if [[ "$skip_npm_build" == "true" ]]; then
    printf '%s [deploy-consumer] Flag antrean: skip_npm_build=true\n' "$(date '+%Y-%m-%d %H:%M:%S')" >> "$LOG_FILE"
    DEPLOY_SKIP_NPM_BUILD=true bash "$DEPLOY_SCRIPT" >> "$LOG_FILE" 2>&1
else
    bash "$DEPLOY_SCRIPT" >> "$LOG_FILE" 2>&1
fi
