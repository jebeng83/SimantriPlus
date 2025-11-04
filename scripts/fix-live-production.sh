#!/usr/bin/env bash

# One-click production fix script for Laravel + Vite on live server
#
# What this script does:
# 1) Disable Vite dev-mode injection by removing public/hot
# 2) Ensure .env is in production mode (APP_ENV=production, APP_DEBUG=false)
# 3) Clear Laravel caches and rebuild route/config caches
# 4) Verify public/build/manifest.json exists; if missing, optionally attempt npm build
# 5) Restart PHP-FPM and reload Nginx when available
# 6) Print a concise summary of results and next steps
#
# Usage:
#   bash scripts/fix-live-production.sh
#
# Notes:
# - Run this on the LIVE SERVER in the project root (the folder containing artisan).
# - If Node/NPM is not installed on the server, make sure you deploy public/build from your local machine.

set -Eeuo pipefail

SCRIPT_NAME="fix-live-production"

info() { printf "[INFO] %s\n" "$*"; }
warn() { printf "[WARN] %s\n" "$*"; }
error() { printf "[ERROR] %s\n" "$*"; }

on_error() {
  error "Script encountered an error. Please review the output above."
}
trap on_error ERR

# Ensure we are in project root
if [ ! -f "artisan" ]; then
  error "artisan not found. Please run this script from your Laravel project root."
  exit 1
fi

PROJECT_ROOT=$(pwd)
PUBLIC_DIR="$PROJECT_ROOT/public"
ENV_FILE="$PROJECT_ROOT/.env"
ENV_PROD_FILE="$PROJECT_ROOT/.env.production"
MANIFEST_FILE="$PUBLIC_DIR/build/manifest.json"
ALT_MANIFEST_FILE="$PUBLIC_DIR/build/.vite/manifest.json"

is_darwin=false
if [ "$(uname)" = "Darwin" ]; then
  is_darwin=true
fi

sed_in_place() {
  # Portable sed in-place replacement for Linux/macOS
  local pattern="$1"; shift
  local file="$1"; shift
  if $is_darwin; then
    sed -i '' -E "$pattern" "$file"
  else
    sed -i -E "$pattern" "$file"
  fi
}

ensure_env_kv() {
  local key="$1"; shift
  local value="$1"; shift
  if [ ! -f "$ENV_FILE" ]; then
    if [ -f "$ENV_PROD_FILE" ]; then
      info ".env not found; copying from .env.production"
      cp "$ENV_PROD_FILE" "$ENV_FILE"
    else
      warn ".env not found and .env.production missing; creating new .env"
      touch "$ENV_FILE"
    fi
  fi

  if grep -q "^${key}=" "$ENV_FILE"; then
    sed_in_place "s|^${key}=.*|${key}=${value}|" "$ENV_FILE"
  else
    printf "\n%s=%s\n" "$key" "$value" >> "$ENV_FILE"
  fi
}

restart_service_if_exists() {
  local svc="$1"
  if command -v systemctl >/dev/null 2>&1; then
    if systemctl list-unit-files | grep -q "^${svc}\.service"; then
      info "Restarting service: ${svc}"
      sudo systemctl restart "$svc" || warn "Failed to restart ${svc}"
    else
      warn "Service ${svc} not found (systemd). Skipping."
    fi
  elif command -v service >/dev/null 2>&1; then
    info "Restarting service via 'service': ${svc}"
    sudo service "$svc" restart || warn "Failed to restart ${svc} via service"
  else
    warn "No service manager detected for ${svc}. Skipping."
  fi
}

reload_service_if_exists() {
  local svc="$1"
  if command -v systemctl >/dev/null 2>&1; then
    if systemctl list-unit-files | grep -q "^${svc}\.service"; then
      info "Reloading service: ${svc}"
      sudo systemctl reload "$svc" || warn "Failed to reload ${svc}"
    else
      warn "Service ${svc} not found (systemd). Skipping."
    fi
  else
    warn "Reload for ${svc} skipped (no systemd)."
  fi
}

php_artisan() {
  info "php artisan $*"
  php artisan "$@" || warn "php artisan $* failed"
}

step_remove_hot() {
  if [ -f "$PUBLIC_DIR/hot" ]; then
    info "Removing dev hot file: $PUBLIC_DIR/hot"
    rm -f "$PUBLIC_DIR/hot"
  else
    info "No public/hot file found (dev mode already disabled)."
  fi
}

step_env_production() {
  info "Ensuring .env is production-ready"
  ensure_env_kv "APP_ENV" "production"
  ensure_env_kv "APP_DEBUG" "false"
  if ! grep -q "^APP_URL=" "$ENV_FILE"; then
    warn "APP_URL not set in .env. Consider setting it to your domain, e.g.: APP_URL=https://faskesku.my.id"
  fi
}

step_clear_caches() {
  info "Clearing and rebuilding Laravel caches"
  php_artisan route:clear
  php_artisan config:clear
  php_artisan view:clear
  php_artisan cache:clear
  php_artisan optimize:clear

  # Attempt to cache routes/config for performance (ignore failures)
  php_artisan route:cache
  php_artisan config:cache
}

step_verify_manifest() {
  # If manifest missing but an alternative manifest exists (Vite v7 default under .vite), reconcile it.
  if [ ! -f "$MANIFEST_FILE" ] && [ -f "$ALT_MANIFEST_FILE" ]; then
    info "Found Vite manifest at alternate path: $ALT_MANIFEST_FILE"
    info "Copying to expected path for Laravel compatibility: $MANIFEST_FILE"
    cp "$ALT_MANIFEST_FILE" "$MANIFEST_FILE" || warn "Failed to copy alternate manifest to expected path"
  fi

  if [ -f "$MANIFEST_FILE" ]; then
    info "Found Vite manifest: $MANIFEST_FILE"
  else
    warn "Vite manifest NOT found at $MANIFEST_FILE"
    if command -v npm >/dev/null 2>&1; then
      if [ -f "$PROJECT_ROOT/package.json" ]; then
        info "Attempting to build assets with npm run build"
        # Install dependencies including devDeps for building
        if NPM_CONFIG_PRODUCTION=false npm ci >/dev/null 2>&1; then
          info "npm ci completed (dev dependencies included)"
        else
          warn "npm ci failed; falling back to npm install with dev dependencies"
          npm install --include=dev || warn "npm install failed"
        fi

        # Try build; if fails due to missing plugins, install dev deps and retry
        if ! npm run build; then
          warn "npm run build failed; attempting to install missing dev deps (vite, laravel-vite-plugin)"
          npm install -D vite laravel-vite-plugin || warn "Failed to install vite/laravel-vite-plugin"
          if ! npm run build; then
            error "npm run build failed after installing dev deps. Please build assets locally and deploy public/build."
          fi
        fi
      else
        error "package.json not found. Cannot build assets on server. Please deploy public/build from local machine."
      fi
    else
      error "npm not found. Please build assets locally (npm run build) and deploy public/build to the server."
    fi

    # Reconcile alternate manifest after attempted build
    if [ ! -f "$MANIFEST_FILE" ] && [ -f "$ALT_MANIFEST_FILE" ]; then
      info "Found alternate manifest after build: $ALT_MANIFEST_FILE"
      info "Copying to expected path: $MANIFEST_FILE"
      cp "$ALT_MANIFEST_FILE" "$MANIFEST_FILE" || warn "Failed to copy alternate manifest after build"
    fi

    # Re-check after attempted build or reconciliation
    if [ -f "$MANIFEST_FILE" ]; then
      info "Vite manifest now present: $MANIFEST_FILE"
    else
      error "Vite manifest still missing after attempted build. Page will 500 until assets exist."
    fi
  fi
}

step_restart_services() {
  # Try common PHP-FPM service names
  for svc in php-fpm php8.2-fpm php8.1-fpm php8.0-fpm php7.4-fpm; do
    restart_service_if_exists "$svc"
  done
  # Reload nginx if available
  reload_service_if_exists nginx
}

main() {
  info "Starting $SCRIPT_NAME in: $PROJECT_ROOT"
  step_remove_hot
  step_env_production
  step_clear_caches
  step_verify_manifest
  step_restart_services

  info "All steps completed. Next steps:"
  info "1) Hard reload your browser (Ctrl+F5 / Cmd+Shift+R)."
  info "2) Open DevTools > Network and confirm assets load from /build and no requests go to 0.0.0.0:5174/5175 or /@vite/client."
  info "3) If you still see 500 errors, run: tail -n 200 storage/logs/laravel.log and share the exact error line."
}

main "$@"