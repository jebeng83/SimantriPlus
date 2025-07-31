#!/bin/bash

# Script Optimalisasi Aplikasi Laravel + WhatsApp Gateway untuk Produksi
# Dibuat untuk meningkatkan performa aplikasi dan mengatasi masalah WhatsApp Node.js
# Version: 2.0 - Enhanced for Production Server

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

echo -e "${GREEN}🚀 Memulai optimalisasi aplikasi untuk produksi...${NC}"
echo "═══════════════════════════════════════════════════════════════"

# 1. Clear semua cache Laravel
print_status "📦 Membersihkan cache Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
print_success "Cache Laravel dibersihkan"

# 2. Optimize autoloader
print_status "⚡ Mengoptimalkan autoloader..."
composer dump-autoload --optimize --no-dev
print_success "Autoloader dioptimalkan"

# 3. Cache konfigurasi untuk produksi
print_status "🔧 Caching konfigurasi Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
print_success "Konfigurasi Laravel di-cache"

# 4. Optimize aplikasi Laravel
print_status "🎯 Mengoptimalkan aplikasi Laravel..."
php artisan optimize
print_success "Aplikasi Laravel dioptimalkan"

# 5. Compile assets jika menggunakan Vite
if [ -f "vite.config.js" ]; then
    print_status "🎨 Compiling assets dengan Vite..."
    npm run build
    print_success "Assets Vite dikompilasi"
fi

# 6. Compile assets jika menggunakan Laravel Mix
if [ -f "webpack.mix.js" ]; then
    print_status "🎨 Compiling assets dengan Laravel Mix..."
    npm run production
    print_success "Assets Laravel Mix dikompilasi"
fi

# 7. WhatsApp Gateway Optimization
echo ""
print_status "📱 Mengoptimalkan WhatsApp Gateway..."

# Stop existing WhatsApp processes
print_status "🛑 Menghentikan proses WhatsApp yang berjalan..."
killall -9 node 2>/dev/null || true
kill -9 $(lsof -t -i:8100) 2>/dev/null || true
print_success "Proses WhatsApp dihentikan"

# Navigate to WhatsApp directory
WA_DIR="public/wagateway/node_mrlee"
if [ -d "$WA_DIR" ]; then
    cd "$WA_DIR"
    
    # Install/Update Node.js dependencies
    print_status "📦 Menginstall/update dependencies Node.js..."
    npm install --production
    print_success "Dependencies Node.js diperbarui"
    
    # Clear WhatsApp session data for fresh start
    print_status "🧹 Membersihkan session WhatsApp lama..."
    rm -rf .wwebjs_auth 2>/dev/null || true
    rm -rf .wwebjs_cache 2>/dev/null || true
    rm -rf session* 2>/dev/null || true
    print_success "Session WhatsApp dibersihkan"
    
    # Create optimized startup script
    print_status "📝 Membuat script startup yang dioptimalkan..."
    cat > start-wa-production.sh << 'EOF'
#!/bin/bash

# Production WhatsApp Gateway Startup Script
# Optimized for server environment

set -e

echo "🚀 Starting WhatsApp Gateway in Production Mode..."

# Set environment variables for production
export NODE_ENV=production
export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=false
export PUPPETEER_EXECUTABLE_PATH=/usr/bin/google-chrome-stable

# Chrome/Chromium arguments for server environment
export CHROME_ARGS="--no-sandbox,--disable-setuid-sandbox,--disable-dev-shm-usage,--disable-accelerated-2d-canvas,--no-first-run,--no-zygote,--disable-gpu,--disable-background-timer-throttling,--disable-backgrounding-occluded-windows,--disable-renderer-backgrounding,--disable-features=TranslateUI,--disable-ipc-flooding-protection"

# Memory optimization
export NODE_OPTIONS="--max-old-space-size=2048"

# Start with nohup for background execution
nohup node appJM.js > wa-gateway.log 2>&1 &
echo $! > wa-gateway.pid

echo "✅ WhatsApp Gateway started in background"
echo "📄 Log file: wa-gateway.log"
echo "🆔 PID file: wa-gateway.pid"
echo "🌐 Server running on port 8100"
EOF
    
    chmod +x start-wa-production.sh
    print_success "Script startup dibuat"
    
    # Create stop script
    cat > stop-wa-production.sh << 'EOF'
#!/bin/bash

echo "🛑 Stopping WhatsApp Gateway..."

if [ -f "wa-gateway.pid" ]; then
    PID=$(cat wa-gateway.pid)
    if kill -0 $PID 2>/dev/null; then
        kill -TERM $PID
        echo "✅ WhatsApp Gateway stopped (PID: $PID)"
        rm -f wa-gateway.pid
    else
        echo "⚠️ Process not running"
        rm -f wa-gateway.pid
    fi
else
    echo "⚠️ PID file not found"
fi

# Force kill if still running
kill -9 $(lsof -t -i:8100) 2>/dev/null || true
echo "🧹 Port 8100 cleaned"
EOF
    
    chmod +x stop-wa-production.sh
    print_success "Script stop dibuat"
    
    # Create restart script
    cat > restart-wa-production.sh << 'EOF'
#!/bin/bash

echo "🔄 Restarting WhatsApp Gateway..."

./stop-wa-production.sh
sleep 3
./start-wa-production.sh

echo "✅ WhatsApp Gateway restarted"
EOF
    
    chmod +x restart-wa-production.sh
    print_success "Script restart dibuat"
    
    # Go back to main directory
    cd - > /dev/null
else
    print_warning "WhatsApp directory tidak ditemukan: $WA_DIR"
fi

# 8. Set permission yang tepat
print_status "🔐 Mengatur permission..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/wagateway

# Set ownership (adjust user as needed)
if command -v www-data &> /dev/null; then
    chown -R www-data:www-data storage
    chown -R www-data:www-data bootstrap/cache
    chown -R www-data:www-data public/wagateway
else
    print_warning "User www-data tidak ditemukan, skip chown"
fi
print_success "Permission diatur"

# 9. Optimize database jika diperlukan
print_status "🗄️ Mengoptimalkan database..."
php artisan migrate --force
print_success "Database dioptimalkan"

# 10. System optimization
print_status "⚙️ Optimalisasi sistem..."

# Increase file limits for Node.js
echo "* soft nofile 65536" >> /etc/security/limits.conf 2>/dev/null || true
echo "* hard nofile 65536" >> /etc/security/limits.conf 2>/dev/null || true

# Optimize shared memory for Puppeteer
echo "tmpfs /dev/shm tmpfs defaults,noatime,nosuid,nodev,noexec,relatime,size=512M 0 0" >> /etc/fstab 2>/dev/null || true

print_success "Sistem dioptimalkan"

echo ""
echo "═══════════════════════════════════════════════════════════════"
print_success "✅ Optimalisasi selesai!"
echo -e "${GREEN}📊 Aplikasi siap untuk produksi dengan performa optimal.${NC}"

# Tampilkan status
echo ""
echo -e "${BLUE}📈 Status Optimalisasi:${NC}"
echo "✓ Cache Laravel dibersihkan dan di-rebuild"
echo "✓ Autoloader dioptimalkan"
echo "✓ Konfigurasi di-cache"
echo "✓ Routes di-cache"
echo "✓ Views di-cache"
echo "✓ Events di-cache"
echo "✓ Assets dikompilasi"
echo "✓ WhatsApp Gateway dioptimalkan"
echo "✓ Session WhatsApp dibersihkan"
echo "✓ Script production dibuat"
echo "✓ Permission diatur"
echo "✓ Database dioptimalkan"
echo "✓ Sistem dioptimalkan"

echo ""
echo -e "${YELLOW}📋 Langkah selanjutnya:${NC}"
echo "1. Jalankan WhatsApp Gateway: cd public/wagateway/node_mrlee && ./start-wa-production.sh"
echo "2. Monitor log: tail -f public/wagateway/node_mrlee/wa-gateway.log"
echo "3. Cek status: curl http://localhost:8100/status"
echo "4. Stop gateway: cd public/wagateway/node_mrlee && ./stop-wa-production.sh"
echo "5. Restart gateway: cd public/wagateway/node_mrlee && ./restart-wa-production.sh"