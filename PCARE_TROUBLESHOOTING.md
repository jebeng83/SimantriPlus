# PCare API Troubleshooting Guide

## Masalah: Server BPJS PCare Berfungsi di kerjo.faskesku.com tapi Tidak di mojo2.faskesku.my.id

### Analisis Situasi

Berdasarkan log dan informasi yang tersedia:
- Server BPJS PCare sedang berjalan normal
- Aplikasi yang sama berfungsi di `kerjo.faskesku.com`
- Aplikasi di `mojo2.faskesku.my.id` mendapat response HTML error dengan status 400
- Kredensial yang terdeteksi: `cons_id: 12385`, `username: riski-11251919`

### Kemungkinan Penyebab

#### 1. **Perbedaan Kredensial PCare**
```bash
# Kredensial yang mungkin berbeda:
- BPJS_PCARE_CONS_ID
- BPJS_PCARE_CONS_PWD (secret key)
- BPJS_PCARE_USER
- BPJS_PCARE_PASS
- BPJS_PCARE_USER_KEY
```

#### 2. **Format Password**
```php
// Gunakan password sesuai dengan yang dikonfigurasi di environment
// PCare tidak selalu memerlukan '#' di akhir password
// Pastikan menggunakan password yang benar sesuai kredensial BPJS
```

#### 3. **Whitelist IP Address**
- Server `mojo2.faskesku.my.id` mungkin belum di-whitelist di BPJS
- IP address server berbeda dengan `kerjo.faskesku.com`

#### 4. **Konfigurasi Environment yang Berbeda**
- File `.env` di production mungkin menggunakan kredensial yang salah
- Kredensial di `.env.production` masih menggunakan placeholder `xxxxx`

### Langkah Troubleshooting

#### Step 1: Jalankan Debug Script
```bash
cd /path/to/edokter
php debug-pcare-connection.php
```

#### Step 2: Bandingkan Kredensial
1. **Ambil kredensial dari server yang berfungsi (kerjo.faskesku.com)**
2. **Bandingkan dengan kredensial di mojo2.faskesku.my.id**
3. **Pastikan format password sudah benar (dengan '#')**

#### Step 3: Cek IP Whitelist
```bash
# Cek IP address server mojo2.faskesku.my.id
curl -s https://ipinfo.io/ip

# Bandingkan dengan IP kerjo.faskesku.com
# Pastikan kedua IP sudah di-whitelist di BPJS
```

#### Step 4: Update Environment Variables
```bash
# Edit file .env di server production
nano .env

# Atau update .env.production jika digunakan
nano .env.production

# Pastikan kredensial sesuai dengan yang berfungsi
BPJS_PCARE_CONS_ID=12385
BPJS_PCARE_CONS_PWD=secret_key_yang_benar
BPJS_PCARE_USER=riski-11251919
BPJS_PCARE_PASS=password_yang_benar#
BPJS_PCARE_USER_KEY=user_key_yang_benar
```

#### Step 5: Clear Cache dan Restart
```bash
# Clear application cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Restart web server jika perlu
sudo systemctl restart nginx
# atau
sudo systemctl restart apache2
```

### Debugging Lanjutan

#### Cek Response Detail
```php
// Tambahkan logging detail di PcareTrait.php
Log::info('PCare Request Debug', [
    'url' => $fullUrl,
    'headers' => $headers,
    'timestamp' => $timestamp,
    'signature' => substr($signature, 0, 10) . '...',
    'authorization' => substr($authorization, 0, 10) . '...'
]);
```

#### Test Manual dengan cURL
```bash
# Test endpoint provider
curl -X GET "https://apijkn.bpjs-kesehatan.go.id/pcare-rest/provider" \
  -H "X-cons-id: 12385" \
  -H "X-timestamp: $(date +%s)" \
  -H "X-signature: SIGNATURE_HERE" \
  -H "X-authorization: Basic AUTH_HERE" \
  -H "user_key: USER_KEY_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

### Solusi Berdasarkan Hasil Debug

#### Jika Provider Endpoint Berhasil tapi Dokter Gagal
- Masalah di endpoint spesifik atau parameter
- Cek format parameter start/limit
- Cek permission untuk endpoint dokter

#### Jika Semua Endpoint Gagal dengan HTML Response
- Masalah di kredensial atau authentication
- Update kredensial sesuai server yang berfungsi
- Cek IP whitelist

#### Jika Timeout atau Connection Error
- Masalah network atau firewall
- Cek koneksi internet server
- Cek firewall rules

### Checklist Verifikasi

- [ ] Kredensial PCare sudah sesuai dengan server yang berfungsi
- [ ] Format password sudah benar (dengan '#')
- [ ] IP address server sudah di-whitelist BPJS
- [ ] Environment variables sudah di-load dengan benar
- [ ] Cache aplikasi sudah di-clear
- [ ] Debug script menunjukkan hasil yang sama dengan server yang berfungsi

### Kontak Support

Jika masalah masih berlanjut:
1. **BPJS Support**: Untuk whitelist IP atau reset kredensial
2. **Tim DevOps**: Untuk konfigurasi server dan network
3. **Tim Development**: Untuk debugging aplikasi lebih lanjut

### Log Monitoring

Setelah perbaikan, monitor log berikut:
```bash
# Monitor log real-time
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log | grep -i pcare

# Cari error patterns
grep -i "pcare.*error\|pcare.*exception" storage/logs/laravel-*.log
```

### Expected Success Response

Setelah perbaikan, log harus menunjukkan:
```
[timestamp] production.INFO: PCare API Response {"status":200,"timestamp":"..."}
[timestamp] production.INFO: PCare Get Dokter Paginated Response {"status":200,"message":"OK","count":10}
```

Bukan:
```
[timestamp] production.ERROR: PCare API Error {"message":"Server BPJS PCare mengembalikan halaman error HTML..."}
```