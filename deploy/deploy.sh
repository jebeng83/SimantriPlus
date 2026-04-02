#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$(cd -- "${SCRIPT_DIR}/.." && pwd)"
LOCK_FILE="${APP_DIR}/storage/logs/deploy.lock"

read_dotenv_value() {
    local key="$1"
    local env_file="${APP_DIR}/.env"

    if [[ ! -f "$env_file" ]]; then
        echo ""
        return
    fi

    awk -v k="$key" '
        $0 ~ "^[[:space:]]*"k"=" {
            sub(/^[^=]*=/, "", $0);
            gsub(/\r/, "", $0);
            gsub(/^[[:space:]]+|[[:space:]]+$/, "", $0);
            if (($0 ~ /^".*"$/) || ($0 ~ /^'\''.*'\''$/)) {
                $0 = substr($0, 2, length($0) - 2);
            }
            print $0;
            exit;
        }
    ' "$env_file"
}

normalize_bool() {
    local value="${1:-}"
    value="$(printf '%s' "$value" | tr -d '\r' | tr '[:upper:]' '[:lower:]' | xargs)"
    case "$value" in
        1|true|yes|on) echo "true" ;;
        *) echo "false" ;;
    esac
}

BRANCH="${DEPLOY_WEBHOOK_BRANCH:-$(read_dotenv_value DEPLOY_WEBHOOK_BRANCH)}"
[[ -n "$BRANCH" ]] || BRANCH="master"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
SKIP_NPM_BUILD="${DEPLOY_SKIP_NPM_BUILD:-$(read_dotenv_value DEPLOY_SKIP_NPM_BUILD)}"
[[ -n "$SKIP_NPM_BUILD" ]] || SKIP_NPM_BUILD="false"
SKIP_MIGRATIONS="${DEPLOY_SKIP_MIGRATIONS:-$(read_dotenv_value DEPLOY_SKIP_MIGRATIONS)}"
[[ -n "$SKIP_MIGRATIONS" ]] || SKIP_MIGRATIONS="false"
DB_HEALTHCHECK="${DEPLOY_DB_HEALTHCHECK:-$(read_dotenv_value DEPLOY_DB_HEALTHCHECK)}"
[[ -n "$DB_HEALTHCHECK" ]] || DB_HEALTHCHECK="true"
RESTART_COMMAND="${DEPLOY_RESTART_COMMAND:-$(read_dotenv_value DEPLOY_RESTART_COMMAND)}"
SKIP_NPM_BUILD="$(normalize_bool "$SKIP_NPM_BUILD")"
SKIP_MIGRATIONS="$(normalize_bool "$SKIP_MIGRATIONS")"
DB_HEALTHCHECK="$(normalize_bool "$DB_HEALTHCHECK")"

log() {
    printf '%s [deploy-edokter] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$1"
}

run_db_healthcheck() {
    local output
    if output=$("$PHP_BIN" -r 'require "vendor/autoload.php"; $app=require "bootstrap/app.php"; $kernel=$app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap(); try { Illuminate\Support\Facades\DB::select("select 1"); echo "DB_OK\n"; } catch (\Throwable $e) { fwrite(STDERR, "DB_FAIL: ".$e->getMessage()."\n"); exit(1); }' 2>&1); then
        log "DB healthcheck berhasil (select 1)."
    else
        output="${output//$'\n'/ }"
        log "DB healthcheck gagal: ${output}"
        return 1
    fi
}

mkdir -p "$(dirname "$LOCK_FILE")"
exec 200>"$LOCK_FILE"

if ! flock -n 200; then
    log "Deploy lain masih berjalan, request ini diabaikan."
    exit 0
fi

cd "$APP_DIR"

log "Mulai deploy branch ${BRANCH}"
log "Flags: skip_npm_build=${SKIP_NPM_BUILD}, skip_migrations=${SKIP_MIGRATIONS}, db_healthcheck=${DB_HEALTHCHECK}"
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

if [[ "$SKIP_MIGRATIONS" == "true" ]]; then
    log "Migrations dilewati (DEPLOY_SKIP_MIGRATIONS=true)."
else
    "$PHP_BIN" artisan migrate --force
fi
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

if [[ "$DB_HEALTHCHECK" == "true" ]]; then
    run_db_healthcheck
else
    log "DB healthcheck dilewati (DEPLOY_DB_HEALTHCHECK=false)."
fi

log "Deploy selesai."
