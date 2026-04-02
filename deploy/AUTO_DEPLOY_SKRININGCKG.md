# Auto Deploy `skriningckg.faskesku.my.id`

Panduan ini mengikuti pola yang sama seperti SIJARIEMAS (GitHub Webhook -> Laravel endpoint -> `deploy/deploy.sh`).

## 1) Parameter yang dipakai

```bash
APP_DOMAIN="skriningckg.faskesku.my.id"
APP_DIR="/www/wwwroot/${APP_DOMAIN}/edokter"
APP_BRANCH="master"
GITHUB_REPO_SSH="git@github-skriningckg:jebeng83/SimantriPlus.git"
```

## 2) Update kode terbaru di server

```bash
cd /www/wwwroot/skriningckg.faskesku.my.id/edokter
git pull --rebase origin master
```

Pastikan route webhook sudah ada:

```bash
php artisan route:list --path=webhook-deploy
```

Harus muncul:

```text
POST webhook-deploy ... DeployWebhookController@handle
```

## 3) Set ENV untuk auto deploy

Edit `.env`:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://skriningckg.faskesku.my.id
ASSET_URL=https://skriningckg.faskesku.my.id

DEPLOY_WEBHOOK_SECRET=ISI_SECRET_ACAK_PANJANG
DEPLOY_WEBHOOK_BRANCH=master
DEPLOY_SCRIPT_PATH=/www/wwwroot/skriningckg.faskesku.my.id/edokter/deploy/deploy.sh
DEPLOY_LOG_PATH=/www/wwwroot/skriningckg.faskesku.my.id/edokter/storage/logs/deploy.log
DEPLOY_QUEUE_PATH=/www/wwwroot/skriningckg.faskesku.my.id/edokter/storage/app/deploy-webhook.queue
DEPLOY_SKIP_MIGRATIONS=false
DEPLOY_DB_HEALTHCHECK=true
DEPLOY_AUTO_RESET_ENV_PRODUCTION=true

# Opsional (jika pakai Octane/Supervisor)
# DEPLOY_RESTART_COMMAND=sudo -n /usr/local/bin/restart-skriningckg-octane.sh
```

Generate secret jika belum punya:

```bash
openssl rand -hex 32
```

Refresh cache config:

```bash
php artisan optimize:clear
php artisan config:cache
```

## 4) Siapkan SSH deploy key untuk user `www-data`

```bash
ssh-keygen -t ed25519 -C "deploy-skriningckg@faskesku" -f /root/.ssh/id_ed25519_skriningckg -N ""

cat >> /root/.ssh/config << 'EOF'
Host github-skriningckg
    HostName github.com
    User git
    IdentityFile /root/.ssh/id_ed25519_skriningckg
EOF

mkdir -p /var/www/.ssh
cp /root/.ssh/id_ed25519_skriningckg /var/www/.ssh/
cp /root/.ssh/id_ed25519_skriningckg.pub /var/www/.ssh/
cp /root/.ssh/config /var/www/.ssh/
chown -R www-data:www-data /var/www/.ssh
chmod 700 /var/www/.ssh
chmod 600 /var/www/.ssh/id_ed25519_skriningckg
chmod 600 /var/www/.ssh/config
```

Tambahkan public key ke GitHub repo (Settings -> Deploy keys, aktifkan write access jika diperlukan):

```bash
cat /root/.ssh/id_ed25519_skriningckg.pub
```

## 5) Pastikan git remote menggunakan SSH alias

```bash
cd /www/wwwroot/skriningckg.faskesku.my.id/edokter
git config --global --add safe.directory "$(pwd)"
git config --system --add safe.directory "$(pwd)"
git remote set-url origin git@github-skriningckg:jebeng83/SimantriPlus.git
sudo -u www-data ssh -T github-skriningckg
```

## 6) Permission + executable deploy script

```bash
cd /www/wwwroot/skriningckg.faskesku.my.id/edokter
mkdir -p storage/logs storage/app
touch storage/logs/deploy.log storage/app/deploy-webhook.queue

chmod +x deploy/deploy.sh deploy/deploy-consumer.sh
chown -R www-data:www-data storage bootstrap/cache /var/www/.ssh
chmod -R 775 storage bootstrap/cache
```

## 7) Opsional: fallback consumer via cron (disarankan)

Tambahkan cron untuk `www-data`:

```bash
crontab -u www-data -e
```

Isi:

```cron
* * * * * cd /www/wwwroot/skriningckg.faskesku.my.id/edokter && /usr/bin/bash deploy/deploy-consumer.sh >/dev/null 2>&1
```

## 8) Daftarkan webhook di GitHub

- Payload URL: `https://skriningckg.faskesku.my.id/webhook-deploy`
- Content type: `application/json`
- Secret: sama dengan `DEPLOY_WEBHOOK_SECRET`
- Events: `Just the push event`
- Active: centang

## 9) Test manual webhook dari server

```bash
cd /www/wwwroot/skriningckg.faskesku.my.id/edokter
SECRET="ISI_DEPLOY_WEBHOOK_SECRET"
PAYLOAD='{"ref":"refs/heads/master","commits":[]}'
SIG="sha256=$(echo -n "$PAYLOAD" | openssl dgst -sha256 -hmac "$SECRET" | cut -d' ' -f2)"

curl -s -w "\nHTTP: %{http_code}\n" -X POST \
  https://skriningckg.faskesku.my.id/webhook-deploy \
  -H "Content-Type: application/json" \
  -H "X-GitHub-Event: push" \
  -H "X-Hub-Signature-256: $SIG" \
  -d "$PAYLOAD"

sleep 5
tail -50 storage/logs/deploy.log
```

Jika sukses:
- Response webhook `HTTP 200`
- Log berisi `Deploy selesai.`
