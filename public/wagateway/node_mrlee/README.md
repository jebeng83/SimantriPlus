# WhatsApp Gateway Node.js

WhatsApp Gateway menggunakan Node.js dan whatsapp-web.js untuk integrasi dengan aplikasi Laravel.

## Fitur

- ✅ Kirim pesan teks
- ✅ Kirim file/dokumen
- ✅ QR Code untuk autentikasi
- ✅ Status koneksi real-time
- ✅ API RESTful
- ✅ Integrasi dengan Laravel
- ✅ Queue management
- ✅ Auto-retry gagal kirim

## Persyaratan

- Node.js v14 atau lebih baru
- npm atau yarn
- Chrome/Chromium browser (untuk headless mode)
- Port 8100 harus tersedia

## Instalasi

### 1. Install Dependencies

```bash
cd /path/to/edokter/public/wagateway/node_mrlee
npm install
```

### 2. Jalankan Server

#### Menggunakan Script (Recommended)
```bash
./start-server.sh
```

#### Manual
```bash
node appJM.js
```

### 3. Hentikan Server

#### Menggunakan Script (Enhanced)
```bash
# Hentikan server Node.js appJM.js dengan robust checking
./stop-server.sh

# Atau hanya kill proses yang menggunakan port 8100
./kill-port-8100.sh
```

#### Manual
- Tekan `Ctrl+C` di terminal
- Atau gunakan API: `POST http://localhost:8100/StopWAG`

## Konfigurasi

### Environment Variables

Tambahkan ke file `.env` Laravel:

```env
# WhatsApp Node.js Gateway
WHATSAPP_NODE_API_URL=http://localhost:8100
WHATSAPP_NODE_ENABLED=true
```

### Port Configuration

Ubah port di file `appJM.js` jika diperlukan:

```javascript
const port = 8100; // Ubah sesuai kebutuhan
```

## API Endpoints

### 1. Status Server
```http
GET http://localhost:8100/
GET http://localhost:8100/uptime
```

### 2. QR Code
```http
POST http://localhost:8100/WA-QrCode
```

Response:
```json
{
  "status": true,
  "message": "QR Ready",
  "qrBarCode": "QR_CODE_STRING"
}
```

### 3. Kirim Pesan
```http
POST http://localhost:8100/send-message
Content-Type: application/json

{
  "number": "628123456789",
  "message": "Hello World!"
}
```

### 4. Kirim File dari URL
```http
POST http://localhost:8100/send-fileurl
Content-Type: application/json

{
  "number": "628123456789",
  "fileurl": "https://example.com/file.pdf",
  "caption": "File caption"
}
```

### 5. Kirim File Lokal
```http
POST http://localhost:8100/send-file
Content-Type: application/json

{
  "number": "628123456789",
  "namafile": "document.pdf",
  "caption": "File caption"
}
```

### 6. Hentikan Server
```http
POST http://localhost:8100/StopWAG
```

## Integrasi dengan Laravel

### 1. Dashboard Node.js

Akses dashboard melalui:
```
http://your-laravel-app/ilp/whatsapp/node/dashboard
```

### 2. Menggunakan Controller

```php
use App\Http\Controllers\WhatsAppNodeController;

// Kirim pesan
$controller = new WhatsAppNodeController();
$response = $controller->sendMessage($request);

// Dapatkan QR Code
$qrResponse = $controller->getQrCode();

// Cek status server
$status = $controller->getServerStatus();
```

### 3. Proses Queue via Node.js

```php
// Proses antrean melalui Node.js
$controller = new WhatsAppNodeController();
$result = $controller->processQueueViaNode($request);
```

## File Structure

```
node_mrlee/
├── appJM.js              # Main server file
├── formatter.js          # Phone number formatter
├── package.json          # Dependencies
├── package-lock.json     # Lock file
├── start-server.sh       # Start script
├── stop-server.sh        # Enhanced stop script with robust PID checking
├── kill-port-8100.sh     # Enhanced port killer script
├── media/                # Directory untuk file lokal
├── nodejs/               # Node.js runtime (Windows)
└── README.md             # Dokumentasi ini
```

## Troubleshooting

### 1. Server Tidak Bisa Start

**Problem**: Port 8100 sudah digunakan
```bash
# Gunakan script enhanced untuk kill port 8100
./kill-port-8100.sh

# Atau manual:
# Cek proses yang menggunakan port 8100
lsof -i :8100

# Hentikan proses
kill -9 <PID>
```

**Problem**: Proses tidak berhenti sempurna
```bash
# Gunakan enhanced stop script yang memiliki retry mechanism
./stop-server.sh

# Script ini akan:
# - Mencari semua proses Node.js appJM.js
# - Menggunakan SIGTERM terlebih dahulu (3 attempts)
# - Menggunakan SIGKILL jika diperlukan (2 attempts)
# - Memastikan port 8100 benar-benar bebas
```

**Problem**: Node.js tidak ditemukan
```bash
# Install Node.js
brew install node  # macOS
# atau download dari https://nodejs.org
```

### 2. QR Code Tidak Muncul

- Pastikan server sudah running
- Tunggu beberapa detik setelah start
- Refresh halaman dashboard
- Cek logs di terminal

### 3. Pesan Tidak Terkirim

- Pastikan WhatsApp sudah terkoneksi (scan QR code)
- Cek format nomor telepon (harus dimulai dengan 62)
- Pastikan nomor terdaftar di WhatsApp
- Cek logs untuk error detail

### 4. Koneksi Terputus

- Server akan otomatis reconnect
- Scan ulang QR code jika diperlukan
- Restart server jika masalah berlanjut

## Logs

### Server Logs
Logs ditampilkan di terminal saat server running:

```
WAG listening on port 8100
QR-> <QR_CODE_STRING>
LOADING.. chats 50%
AUTHENTICATED
WA Gate is ready!
```

### Laravel Logs
Logs Laravel tersimpan di `storage/logs/laravel.log`

## Security

### 1. Firewall
- Pastikan port 8100 hanya dapat diakses dari localhost
- Gunakan reverse proxy jika perlu akses eksternal

### 2. Authentication
- Implementasikan API key jika diperlukan
- Gunakan HTTPS untuk production

### 3. Rate Limiting
- Implementasikan rate limiting untuk mencegah spam
- Monitor penggunaan API

## Production Deployment

### 1. Process Manager

Gunakan PM2 untuk production:

```bash
# Install PM2
npm install -g pm2

# Start dengan PM2
pm2 start appJM.js --name "whatsapp-gateway"

# Auto start on boot
pm2 startup
pm2 save
```

### 2. Monitoring

```bash
# Monitor dengan PM2
pm2 monit

# Logs
pm2 logs whatsapp-gateway

# Restart
pm2 restart whatsapp-gateway
```

### 3. Environment

- Gunakan environment variables untuk konfigurasi
- Set NODE_ENV=production
- Konfigurasi logging yang proper

## Support

Jika mengalami masalah:

1. Cek dokumentasi ini
2. Lihat logs server dan Laravel
3. Cek issue di repository whatsapp-web.js
4. Hubungi developer

## Changelog

### v1.0.0
- Initial release
- Basic WhatsApp functionality
- Laravel integration
- Dashboard interface
- Queue management

---

**Author**: Mr. Lee  
**Version**: 1.0.0  
**Last Updated**: 2024