# WhatsApp Gateway Troubleshooting Guide

## 🚨 Masalah Umum: ProtocolError - Execution Context Destroyed

### Deskripsi Masalah
Error yang sering muncul di server produksi:
```
Failed to initialize WhatsApp client: ProtocolError: Protocol error (Runtime.callFunctionOn): Execution context was destroyed.
```

### Penyebab Utama
1. **Keterbatasan Resource Server**: Memory dan CPU tidak mencukupi
2. **Konfigurasi Puppeteer**: Tidak optimal untuk environment server
3. **Chrome/Chromium**: Tidak terinstall atau tidak kompatibel
4. **Shared Memory**: /dev/shm terlalu kecil
5. **Network Issues**: Koneksi ke WhatsApp Web tidak stabil

## 🔧 Solusi yang Telah Diimplementasikan

### 1. Enhanced Puppeteer Configuration
```javascript
const puppeteerConfig = {
  headless: true,
  args: [
    '--no-sandbox',
    '--disable-setuid-sandbox',
    '--disable-dev-shm-usage',
    '--single-process', // Penting untuk server
    '--disable-gpu',
    '--memory-pressure-off',
    '--max_old_space_size=4096'
    // ... dan banyak lagi
  ],
  timeout: 60000,
  protocolTimeout: 60000
};
```

### 2. Retry Mechanism
- Otomatis retry hingga 3 kali
- Delay 10 detik antar retry
- Auto cleanup session jika gagal

### 3. Enhanced Error Handling
- Tidak exit saat error di production
- Logging yang lebih detail
- Graceful shutdown

### 4. Production Scripts
- `start-wa-production.sh`: Start dengan konfigurasi optimal
- `stop-wa-production.sh`: Stop dengan cleanup
- `restart-wa-production.sh`: Restart lengkap

## 📋 Langkah Troubleshooting

### Step 1: Jalankan Optimalisasi
```bash
# Di root directory aplikasi
./optimize-production.sh
```

### Step 2: Start WhatsApp Gateway
```bash
cd public/wagateway/node_mrlee
./start-wa-production.sh
```

### Step 3: Monitor Logs
```bash
# Monitor real-time
tail -f public/wagateway/node_mrlee/wa-gateway.log

# Atau check via API
curl http://localhost:8100/status
```

### Step 4: Troubleshoot via API
```bash
# Dapatkan info troubleshooting
curl http://localhost:8100/troubleshoot

# Restart manual jika perlu
curl http://localhost:8100/restart-client
```

## 🛠️ Manual Troubleshooting

### Cek Chrome/Chromium Installation
```bash
# Cek apakah Chrome terinstall
which google-chrome-stable
which chromium-browser

# Install jika belum ada (Ubuntu/Debian)
sudo apt update
sudo apt install -y google-chrome-stable

# Atau Chromium
sudo apt install -y chromium-browser
```

### Cek Memory dan Resources
```bash
# Cek memory usage
free -h

# Cek /dev/shm size (minimal 512MB)
df -h /dev/shm

# Increase jika perlu
sudo mount -o remount,size=512M /dev/shm
```

### Cek Port dan Processes
```bash
# Cek port 8100
lsof -i :8100

# Kill process jika perlu
kill -9 $(lsof -t -i:8100)

# Cek Node.js processes
ps aux | grep node
```

### Clear Session Data
```bash
cd public/wagateway/node_mrlee
rm -rf .wwebjs_auth .wwebjs_cache session*
```

## 🔍 Monitoring dan Debugging

### API Endpoints untuk Monitoring
- `GET /status` - Status server dan WhatsApp
- `GET /troubleshoot` - Info troubleshooting lengkap
- `GET /restart-client` - Restart WhatsApp client
- `POST /WA-QrCode` - Dapatkan QR Code

### Log Files
- `wa-gateway.log` - Log aplikasi utama
- `wa-gateway.pid` - Process ID file

### Environment Variables
```bash
export NODE_ENV=production
export PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome-stable
export NODE_OPTIONS="--max-old-space-size=2048"
```

## 🚀 Best Practices untuk Production

### 1. Server Requirements
- **RAM**: Minimal 2GB, recommended 4GB+
- **CPU**: Minimal 2 cores
- **Storage**: SSD recommended
- **Network**: Stable internet connection

### 2. System Optimization
```bash
# Increase file limits
echo "* soft nofile 65536" >> /etc/security/limits.conf
echo "* hard nofile 65536" >> /etc/security/limits.conf

# Optimize shared memory
echo "tmpfs /dev/shm tmpfs defaults,noatime,nosuid,nodev,noexec,relatime,size=512M 0 0" >> /etc/fstab
```

### 3. Process Management
```bash
# Gunakan PM2 untuk production
npm install -g pm2

# Start dengan PM2
pm2 start appJM.js --name whatsapp-gateway

# Auto restart on reboot
pm2 startup
pm2 save
```

### 4. Monitoring
```bash
# Setup monitoring script
watch -n 30 'curl -s http://localhost:8100/status | jq .'

# Log rotation
sudo logrotate -f /etc/logrotate.conf
```

## 🆘 Emergency Recovery

Jika semua cara gagal:

1. **Full Reset**:
   ```bash
   ./stop-wa-production.sh
   rm -rf .wwebjs_auth .wwebjs_cache session*
   killall -9 node
   ./start-wa-production.sh
   ```

2. **Reboot Server**:
   ```bash
   sudo reboot
   ```

3. **Reinstall Dependencies**:
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   ```

## 📞 Support

Jika masalah masih berlanjut:
1. Cek log file: `wa-gateway.log`
2. Jalankan: `curl http://localhost:8100/troubleshoot`
3. Screenshot error message
4. Dokumentasikan langkah yang sudah dicoba

## 📚 Referensi
- [WhatsApp Web.js Documentation](https://wwebjs.dev/)
- [Puppeteer Troubleshooting](https://pptr.dev/troubleshooting)
- [Node.js Production Best Practices](https://nodejs.org/en/docs/guides/nodejs-docker-webapp/)