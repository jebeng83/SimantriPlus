# Panduan Deployment Production

## Masalah CORS dengan Vite Development Server

### Deskripsi Masalah
Ketika aplikasi dijalankan di live server, muncul error CORS:
```
Access to script at 'http://127.0.0.1:5174/@vite/client' from origin 'https://faskesku.my.id' has been blocked by CORS policy
```

### Penyebab
- Aplikasi mencoba mengakses Vite development server (`127.0.0.1:5174`) dari domain production
- `@viteReactRefresh` masih aktif di production environment
- Asset belum di-build untuk production

### Solusi yang Telah Diterapkan

#### 1. Konfigurasi Vite (`vite.config.js`)
```javascript
export default defineConfig({
    server: {
        host: '0.0.0.0', // Allow external connections
        cors: {
            origin: [
                'http://localhost:8000',
                'http://127.0.0.1:8000',
                'https://faskesku.my.id',
                'http://kerjo.faskesku.com',
                /^https?:\/\/.*\.faskesku\..*$/
            ],
            credentials: true
        }
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
    }
});
```

#### 2. Environment Check untuk @viteReactRefresh
Semua file Blade template telah diperbarui untuk hanya memuat `@viteReactRefresh` di development:
```blade
@if(app()->environment('local', 'development'))
    @viteReactRefresh
@endif
```

#### 3. Build Assets untuk Production
```bash
npm run build
```

## Langkah Deployment

### 1. Persiapan Environment
```bash
# Copy environment file
cp .env.production .env

# Pastikan APP_ENV=production
APP_ENV=production
APP_DEBUG=false
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install
```

### 3. Build Assets
```bash
# Build production assets
npm run build
```

### 4. Optimize Laravel
```bash
# Clear dan cache konfigurasi
php artisan config:clear
php artisan config:cache

# Clear dan cache routes
php artisan route:clear
php artisan route:cache

# Clear dan cache views
php artisan view:clear
php artisan view:cache

# Optimize autoloader
php artisan optimize
```

### 5. Set Permissions
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Verifikasi Deployment

### 1. Check Environment
```bash
php artisan env
```

### 2. Check Assets
Pastikan folder `public/build` berisi file-file hasil build:
- `manifest.json`
- `assets/*.css`
- `assets/*.js`

### 3. Test Application
- Akses aplikasi melalui browser
- Check console untuk memastikan tidak ada error CORS
- Pastikan React components berfungsi dengan baik

## Troubleshooting

### Jika Masih Ada Error CORS
1. Pastikan `APP_ENV=production` di file `.env`
2. Clear cache: `php artisan config:clear`
3. Rebuild assets: `npm run build`
4. Restart web server

### Jika Assets Tidak Termuat
1. Check apakah folder `public/build` ada dan berisi file
2. Check permissions folder `public/build`
3. Pastikan web server dapat mengakses folder `public/build`

### Jika React Components Tidak Berfungsi
1. Check console browser untuk error JavaScript
2. Pastikan semua dependencies ter-install dengan benar
3. Rebuild assets dengan `npm run build`

## File yang Telah Dimodifikasi

1. `vite.config.js` - Konfigurasi CORS dan build
2. Semua file Blade template - Environment check untuk `@viteReactRefresh`
3. `fix_vite_refresh.sh` - Script untuk memperbaiki template files

## Catatan Penting

- **Jangan** jalankan `npm run dev` di production server
- **Selalu** gunakan `npm run build` untuk production
- **Pastikan** `APP_ENV=production` di file `.env`
- **Check** bahwa `@viteReactRefresh` hanya dimuat di development environment