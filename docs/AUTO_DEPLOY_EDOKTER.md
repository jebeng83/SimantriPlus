# Auto Deploy EDokter (Git Push -> Server Update)

Panduan ini mengikuti pola "jari emas": GitHub Webhook memanggil endpoint Laravel, lalu menjalankan `deploy/deploy.sh`.

## 1) Komponen yang sudah ditambahkan di repo

- Route webhook: `POST /webhook-deploy`
- Controller: `app/Http/Controllers/DeployWebhookController.php`
- CSRF exception: `app/Http/Middleware/VerifyCsrfToken.php`
- Script deploy: `deploy/deploy.sh`
- Script consumer antrean deploy: `deploy/deploy-consumer.sh`
- Konfigurasi env/app:
  - `DEPLOY_WEBHOOK_SECRET`
  - `DEPLOY_WEBHOOK_BRANCH` (default `master`)
  - `DEPLOY_SCRIPT_PATH`
  - `DEPLOY_LOG_PATH`
  - `DEPLOY_QUEUE_PATH`
  - `DEPLOY_DB_HEALTHCHECK` (default `true`)

## 2) Setup di server

Jalankan di server:

```bash
cd /www/wwwroot/faskesku.my.id/edokter
chmod +x deploy/deploy.sh
```

Tambahkan ke `.env` production:

```env
DEPLOY_WEBHOOK_SECRET=isi_dengan_secret_panjang_acak
DEPLOY_WEBHOOK_BRANCH=master
DEPLOY_SCRIPT_PATH=/www/wwwroot/faskesku.my.id/edokter/deploy/deploy.sh
DEPLOY_LOG_PATH=/www/wwwroot/faskesku.my.id/edokter/storage/logs/deploy.log
DEPLOY_QUEUE_PATH=/www/wwwroot/faskesku.my.id/edokter/storage/app/deploy-webhook.queue
DEPLOY_SKIP_MIGRATIONS=true
DEPLOY_DB_HEALTHCHECK=true

# Opsional:
# DEPLOY_SKIP_NPM_BUILD=true
# DEPLOY_RESTART_COMMAND="sudo /usr/local/bin/restart-edokter-octane.sh"
```

Reload config Laravel:

```bash
cd /www/wwwroot/faskesku.my.id/edokter
php artisan optimize:clear
php artisan config:cache
```

Aktifkan mode fallback consumer (wajib jika `exec/proc_open/popen` dibatasi di PHP-FPM):

```bash
cd /www/wwwroot/faskesku.my.id/edokter
chmod +x deploy/deploy.sh deploy/deploy-consumer.sh

# Tambahkan cron (jalan tiap menit)
(crontab -l 2>/dev/null; echo '* * * * * /bin/bash /www/wwwroot/faskesku.my.id/edokter/deploy/deploy-consumer.sh >/dev/null 2>&1') | crontab -
```

## 3) Setup GitHub Webhook

Di repo GitHub `jebeng83/SimantriPlus`:

1. Buka `Settings -> Webhooks -> Add webhook`.
2. Isi:
   - `Payload URL`: `https://faskesku.my.id/webhook-deploy`
   - `Content type`: `application/json`
   - `Secret`: samakan dengan `DEPLOY_WEBHOOK_SECRET`
   - `Which events`: `Just the push event`
3. Simpan.

## 4) Verifikasi cepat dari server

Tes endpoint webhook dengan signature valid:

```bash
cd /www/wwwroot/faskesku.my.id/edokter
payload='{"ref":"refs/heads/master","repository":{"full_name":"jebeng83/SimantriPlus"},"pusher":{"name":"manual-test"}}'
secret='ISI_SECRET_SAMA_DENGAN_ENV'
sig=$(printf '%s' "$payload" | openssl dgst -sha256 -hmac "$secret" | awk '{print $2}')
curl -i -X POST "https://faskesku.my.id/webhook-deploy" \
  -H "Content-Type: application/json" \
  -H "X-GitHub-Event: push" \
  -H "X-Hub-Signature-256: sha256=$sig" \
  -d "$payload"
```

Hasil sukses endpoint:
- `HTTP 200` -> deploy langsung dijalankan.
- `HTTP 202` -> deploy masuk antrean fallback, lalu diproses oleh `deploy-consumer.sh` via cron.

Untuk skip build frontend hanya pada push tertentu:
- Tambahkan tag commit message: `[skip-build]` atau `[no-build]`.
- Webhook akan mengantre deploy dengan `skip_npm_build=true`.
- Consumer otomatis menjalankan deploy dengan `DEPLOY_SKIP_NPM_BUILD=true`.

Pantau log deploy:

```bash
tail -f /www/wwwroot/faskesku.my.id/edokter/storage/logs/deploy.log
```

## 5) Catatan penting

- Branch deploy saat ini `master` (bukan `main`).
- Script memakai `git pull --ff-only` agar aman dan tidak rewrite history.
- Jika database lama belum sinkron dengan tabel `migrations`, aktifkan `DEPLOY_SKIP_MIGRATIONS=true`.
- Script menjalankan healthcheck DB `select 1` di akhir deploy saat `DEPLOY_DB_HEALTHCHECK=true`.
- Jika `git pull` gagal karena permission/safe directory, set:

```bash
git config --system --add safe.directory /www/wwwroot/faskesku.my.id/edokter
```

- Untuk repo private, pastikan user eksekusi web server punya akses SSH key ke GitHub (seperti pola jari emas).
