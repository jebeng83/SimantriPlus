#!/usr/bin/env bash
# Deploy script for Laravel + Vite (React) on Nginx (aaPanel)
# Usage (example):
#   export DOMAIN="kerjo.faskesku.com"
#   export WEBROOT="/www/wwwroot/${DOMAIN}"
#   export BUILD_ASSETS=false   # set true if Node is installed on server and you want to build on server
#   export RUN_MIGRATIONS=true
#   bash scripts/deploy-aapanel-nginx.sh

set -euo pipefail

log() { printf "\033[1;32m[DEPLOY]\033[0m %s\n" "$*"; }
warn() { printf "\033[1;33m[WARN]\033[0m %s\n" "$*"; }
err() { printf "\033[1;31m[ERROR]\033[0m %s\n" "$*"; }

# -------- Configurable Variables --------
DOMAIN=${DOMAIN:-"example.com"}
WEBROOT=${WEBROOT:-"/www/wwwroot/${DOMAIN}"}
REPO_DIR=${REPO_DIR:-"${WEBROOT}"}
WEB_USER=${WEB_USER:-"www"}      # aaPanel default web user
WEB_GROUP=${WEB_GROUP:-"www"}
BUILD_ASSETS=${BUILD_ASSETS:-false}
RUN_MIGRATIONS=${RUN_MIGRATIONS:-true}

# Detect PHP binary (aaPanel typical paths), fallback to system php
detect_php() {
  local candidates=(
    "/www/server/php/83/bin/php"
    "/www/server/php/82/bin/php"
    "/www/server/php/81/bin/php"
    "/www/server/php/80/bin/php"
    "/usr/bin/php"
    "/usr/local/bin/php"
  )
  for p in "${candidates[@]}"; do
    if [ -x "$p" ]; then echo "$p"; return 0; fi
  done
  echo "php" # hope it's in PATH
}

PHP_BIN=${PHP_BIN:-"$(detect_php)"}

# Detect composer
detect_composer() {
  if command -v composer >/dev/null 2>&1; then
    command -v composer
  elif [ -x "/usr/local/bin/composer" ]; then
    echo "/usr/local/bin/composer"
  elif [ -x "/usr/bin/composer" ]; then
    echo "/usr/bin/composer"
  else
    err "Composer tidak ditemukan. Install composer terlebih dahulu."; exit 1
  fi
}
COMPOSER_BIN=${COMPOSER_BIN:-"$(detect_composer)"}

log "Domain         : ${DOMAIN}"
log "Webroot        : ${WEBROOT}"
log "Repository dir : ${REPO_DIR}"
log "PHP binary     : ${PHP_BIN}"
log "Composer       : ${COMPOSER_BIN}"
log "Web user/group : ${WEB_USER}:${WEB_GROUP}"
log "Build assets   : ${BUILD_ASSETS}"
log "Run migrations : ${RUN_MIGRATIONS}"

if [ ! -d "${REPO_DIR}" ]; then
  err "Direktori repository ${REPO_DIR} tidak ditemukan."; exit 1
fi

cd "${REPO_DIR}"

# -------- Validasi .env --------
if [ -f ".env" ]; then
  log ".env ditemukan, menggunakan konfigurasi yang ada."
else
  warn ".env tidak ditemukan di ${REPO_DIR}. Harap upload file .env produksi lewat aaPanel atau SCP sebelum deploy."
  if [ -f ".env.production" ]; then
    warn "Terdapat .env.production, namun tidak akan di-copy otomatis demi menjaga konfigurasi produksi yang sudah ada."
  fi
fi

# -------- Dependencies --------
log "Composer install (no-dev, optimize autoloader)"
"${COMPOSER_BIN}" install --no-dev --prefer-dist --optimize-autoloader

# Optional: Build assets on server (recommended build di lokal)
if [ "${BUILD_ASSETS}" = "true" ]; then
  if command -v npm >/dev/null 2>&1; then
    log "Node ditemukan, menjalankan npm ci && npm run build"
    npm ci
    npm run build
  else
    err "npm tidak ditemukan. Set BUILD_ASSETS=false atau install Node terlebih dahulu."; exit 1
  fi
else
log "BUILD_ASSETS=false, pastikan public/build sudah ter-upload dari lokal."
fi

# -------- Vite dev hot file cleanup --------
if [ -f "public/hot" ]; then
  log "Menghapus public/hot (file Vite dev) untuk mencegah koneksi ke dev server di produksi"
  rm -f public/hot || true
fi

# -------- Permissions --------
log "Menyiapkan direktori dan permission"
mkdir -p storage/logs
mkdir -p bootstrap/cache
chown -R "${WEB_USER}:${WEB_GROUP}" storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

# -------- Laravel cache & migrate --------
log "Membersihkan dan membangun cache Laravel"
"${PHP_BIN}" artisan optimize:clear || true
"${PHP_BIN}" artisan config:clear || true
"${PHP_BIN}" artisan route:clear || true
"${PHP_BIN}" artisan view:clear || true
"${PHP_BIN}" artisan event:clear || true

"${PHP_BIN}" artisan config:cache
"${PHP_BIN}" artisan route:cache
"${PHP_BIN}" artisan view:cache
"${PHP_BIN}" artisan event:cache

if [ "${RUN_MIGRATIONS}" = "true" ]; then
  log "Menjalankan migrasi database (--force)"
  "${PHP_BIN}" artisan migrate --force
else
  log "RUN_MIGRATIONS=false, melewati migrasi database"
fi

# -------- Web server restart --------
restart_nginx() {
  if command -v systemctl >/dev/null 2>&1; then
    sudo systemctl restart nginx || sudo systemctl reload nginx || true
  else
    sudo service nginx restart || sudo service nginx reload || true
  fi
}

restart_php_fpm() {
  local services=(php-fpm php-fpm-83 php-fpm-82 php-fpm-81 php-fpm-80 php-fpm-74)
  for s in "${services[@]}"; do
    if command -v systemctl >/dev/null 2>&1; then
      if systemctl list-units --type=service | grep -q "${s}.service"; then
        sudo systemctl restart "${s}" && return 0
      fi
    else
      if service --status-all 2>/dev/null | grep -q "${s}"; then
        sudo service "${s}" restart && return 0
      fi
    fi
  done
  warn "Tidak menemukan layanan php-fpm yang cocok untuk restart. Pastikan versi PHP-FPM Anda."
}

log "Restarting services (nginx & php-fpm)"
restart_nginx
restart_php_fpm

# -------- Final checks --------
log "Menjalankan smoke test internal"
"${PHP_BIN}" -r "echo 'PHP OK';" >/dev/null || warn "PHP CLI masalah, tetapi ini tidak selalu mempengaruhi FPM."

log "Selesai. Pastikan vhost Nginx diarahkan ke ${WEBROOT}/public dan APP_URL sudah benar di .env"
log "Jika menggunakan queue/scheduler, konfigurasi Supervisor dan cron diperlukan."

# Tips tambahan aaPanel:
# - Buat site di aaPanel dengan docroot: /www/wwwroot/${DOMAIN}/public
# - Pastikan permission milik user 'www'
# - Jika SELinux aktif, set context: chcon -R -t httpd_sys_rw_content_t storage bootstrap/cache