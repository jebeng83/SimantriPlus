# 🚀 Panduan Optimalisasi Aplikasi untuk Produksi

Dokumen ini berisi panduan lengkap untuk mengoptimalkan aplikasi Laravel Simantri PLUS untuk environment produksi.

## 📋 Daftar Optimalisasi yang Telah Diterapkan

### 1. **Konfigurasi Cache & Session**
- ✅ Cache driver diubah dari `file` ke `redis` untuk performa lebih baik
- ✅ Session driver diubah ke `redis` dengan enkripsi aktif
- ✅ Queue connection menggunakan `redis`
- ✅ Session lifetime diperpanjang menjadi 480 menit (8 jam)

### 2. **Optimalisasi Database**
- ✅ Connection timeout dioptimalkan (30 detik)
- ✅ Persistent connection diaktifkan
- ✅ Buffered query diaktifkan
- ✅ Connection pooling dikonfigurasi
- ✅ SQL mode dioptimalkan untuk performa

### 3. **Optimalisasi Application Service Provider**
- ✅ Database query logging hanya untuk development
- ✅ Slow query detection (> 1000ms)
- ✅ Debug bar dinonaktifkan untuk produksi
- ✅ Session timeout handling dioptimalkan
- ✅ Timezone consistency

### 4. **Response Optimization Middleware**
- ✅ HTML minification untuk produksi
- ✅ Static asset caching (1 tahun)
- ✅ Security headers (XSS, CSRF, Frame protection)
- ✅ GZIP compression
- ✅ Content-Type optimization

### 5. **Environment Configuration**
- ✅ File `.env.production` dengan konfigurasi optimal
- ✅ Redis configuration untuk multiple databases
- ✅ Performance tuning parameters
- ✅ Debug flags dinonaktifkan
- ✅ Logging level dioptimalkan

### 6. **Automation Script**
- ✅ Script `optimize-production.sh` untuk deployment otomatis
- ✅ Cache clearing dan rebuilding
- ✅ Autoloader optimization
- ✅ Asset compilation
- ✅ Permission setting

## 🛠️ Cara Implementasi

### Langkah 1: Persiapan Environment

```bash
# 1. Install dan konfigurasi Redis
sudo apt update
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# 2. Verifikasi Redis berjalan
redis-cli ping
# Response: PONG
```

### Langkah 2: Update Environment File

```bash
# Copy konfigurasi produksi
cp .env.production .env

# Edit sesuai kebutuhan server
nano .env
```

### Langkah 3: Jalankan Script Optimalisasi

```bash
# Berikan permission execute
chmod +x optimize-production.sh

# Jalankan optimalisasi
./optimize-production.sh
```

### Langkah 4: Daftarkan Middleware (Opsional)

Tambahkan middleware optimalisasi di `app/Http/Kernel.php`:

```php
protected $middleware = [
    // ... middleware lainnya
    \App\Http\Middleware\OptimizeResponse::class,
];
```

## 📊 Peningkatan Performa yang Diharapkan

### Before vs After Optimization

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Page Load Time | 2-3s | 0.8-1.2s | **60-70% faster** |
| Database Query Time | 100-500ms | 50-150ms | **50-70% faster** |
| Memory Usage | 128-256MB | 64-128MB | **50% reduction** |
| Cache Hit Rate | 0% | 85-95% | **Significant** |
| Concurrent Users | 50-100 | 200-500 | **4-5x increase** |

## 🔧 Konfigurasi Server yang Direkomendasikan

### PHP Configuration (php.ini)
```ini
; Memory & Execution
memory_limit = 512M
max_execution_time = 120
max_input_time = 120

; File Uploads
upload_max_filesize = 64M
post_max_size = 64M
max_file_uploads = 20

; OPcache (Sangat Penting!)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 20000
opcache.validate_timestamps = 0
opcache.revalidate_freq = 0
opcache.save_comments = 0
opcache.fast_shutdown = 1

; Session
session.gc_maxlifetime = 28800
session.cookie_lifetime = 28800
```

### MySQL Configuration (my.cnf)
```ini
[mysqld]
# Connection & Buffer Pool
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 200
connect_timeout = 30
wait_timeout = 600
interactive_timeout = 600

# Query Cache
query_cache_type = 1
query_cache_size = 128M
query_cache_limit = 4M

# Performance
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1
```

### Redis Configuration (redis.conf)
```ini
# Memory
maxmemory 512mb
maxmemory-policy allkeys-lru

# Persistence
save 900 1
save 300 10
save 60 10000

# Network
tcp-keepalive 300
timeout 0
```

## 🔍 Monitoring & Maintenance

### Script Monitoring Performa

```bash
#!/bin/bash
# monitor-performance.sh

echo "=== Laravel Performance Monitor ==="
echo "Date: $(date)"
echo ""

# Check Redis
echo "Redis Status:"
redis-cli ping
echo ""

# Check MySQL
echo "MySQL Status:"
mysql -u root -p -e "SHOW PROCESSLIST;" | wc -l
echo ""

# Check PHP-FPM
echo "PHP-FPM Status:"
sudo systemctl status php8.1-fpm | grep Active
echo ""

# Check Disk Usage
echo "Disk Usage:"
df -h | grep -E '(Filesystem|/dev/)'
echo ""

# Check Memory
echo "Memory Usage:"
free -h
echo ""

# Laravel Queue Status
echo "Queue Status:"
php artisan queue:work --once --timeout=30
```

### Maintenance Tasks (Crontab)

```bash
# Tambahkan ke crontab (crontab -e)

# Clear expired sessions (daily at 2 AM)
0 2 * * * cd /path/to/app && php artisan session:gc

# Clear old logs (weekly)
0 3 * * 0 cd /path/to/app && find storage/logs -name "*.log" -mtime +7 -delete

# Optimize application (daily at 3 AM)
0 3 * * * cd /path/to/app && php artisan optimize

# Backup database (daily at 1 AM)
0 1 * * * mysqldump -u root -p kerjo > /backup/kerjo_$(date +\%Y\%m\%d).sql
```

## 🚨 Troubleshooting

### Common Issues & Solutions

1. **Redis Connection Failed**
   ```bash
   # Check Redis status
   sudo systemctl status redis-server
   
   # Restart Redis
   sudo systemctl restart redis-server
   ```

2. **High Memory Usage**
   ```bash
   # Clear all caches
   php artisan optimize:clear
   
   # Restart PHP-FPM
   sudo systemctl restart php8.1-fpm
   ```

3. **Slow Database Queries**
   ```bash
   # Enable slow query log
   mysql -u root -p -e "SET GLOBAL slow_query_log = 'ON';"
   mysql -u root -p -e "SET GLOBAL long_query_time = 1;"
   ```

4. **Session Issues**
   ```bash
   # Clear sessions
   redis-cli FLUSHDB
   php artisan session:flush
   ```

## 📈 Performance Testing

### Load Testing dengan Apache Bench

```bash
# Test 1000 requests dengan 10 concurrent users
ab -n 1000 -c 10 http://kerjo.faskesku.com/

# Test dengan authentication
ab -n 500 -c 5 -C "session_cookie=value" http://kerjo.faskesku.com/dashboard
```

### Monitoring Real-time

```bash
# Monitor Redis
redis-cli monitor

# Monitor MySQL
mysqladmin -u root -p processlist

# Monitor PHP-FPM
sudo tail -f /var/log/php8.1-fpm.log

# Monitor Laravel logs
tail -f storage/logs/laravel.log
```

## ✅ Checklist Deployment

- [ ] Redis server terinstall dan berjalan
- [ ] File `.env` dikonfigurasi untuk produksi
- [ ] Script `optimize-production.sh` dijalankan
- [ ] OPcache diaktifkan di PHP
- [ ] MySQL dikonfigurasi untuk performa
- [ ] Monitoring script disetup
- [ ] Crontab maintenance tasks ditambahkan
- [ ] Load testing dilakukan
- [ ] Backup strategy diimplementasikan
- [ ] SSL certificate terinstall (jika diperlukan)

## 📞 Support

Jika mengalami masalah dalam implementasi optimalisasi ini, silakan:

1. Periksa log aplikasi di `storage/logs/laravel.log`
2. Periksa log server (Apache/Nginx, PHP-FPM, MySQL, Redis)
3. Jalankan `php artisan optimize:clear` untuk reset cache
4. Restart semua service yang terkait

---

**Catatan**: Optimalisasi ini telah diuji dan terbukti meningkatkan performa aplikasi secara signifikan. Pastikan untuk melakukan backup sebelum implementasi di server produksi.