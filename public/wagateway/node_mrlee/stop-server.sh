#!/bin/bash

# Script untuk menghentikan WhatsApp Gateway Node.js Server
# Author: Mr. Lee
# Version: 2.0 - Enhanced with robust PID checking

echo "=== Stop WhatsApp Gateway Node.js Server ==="

# Function untuk kill proses dengan retry
kill_process_with_retry() {
    local pid=$1
    local max_attempts=5
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if ! ps -p $pid > /dev/null 2>&1; then
            echo "Proses dengan PID $pid sudah tidak berjalan."
            return 0
        fi
        
        echo "Attempt $attempt: Menghentikan proses PID $pid..."
        
        if [ $attempt -le 3 ]; then
            # Gunakan SIGTERM untuk 3 attempt pertama
            kill $pid 2>/dev/null
        else
            # Gunakan SIGKILL untuk attempt terakhir
            echo "Menggunakan force kill (SIGKILL)..."
            kill -9 $pid 2>/dev/null
        fi
        
        # Tunggu proses berhenti
        sleep 2
        
        # Cek apakah proses masih berjalan
        if ! ps -p $pid > /dev/null 2>&1; then
            echo "Proses berhasil dihentikan pada attempt $attempt."
            return 0
        fi
        
        attempt=$((attempt + 1))
    done
    
    echo "Error: Gagal menghentikan proses setelah $max_attempts attempts!"
    return 1
}

# Cari semua proses Node.js yang menjalankan appJM.js
echo "Mencari proses Node.js yang menjalankan appJM.js..."
PIDS=$(ps aux | grep "node.*appJM.js" | grep -v grep | awk '{print $2}')

if [ -z "$PIDS" ]; then
    echo "Tidak ada server Node.js appJM.js yang sedang berjalan."
else
    echo "Ditemukan proses Node.js appJM.js dengan PID: $PIDS"
    
    # Kill setiap PID yang ditemukan
    for pid in $PIDS; do
        echo "Menghentikan proses dengan PID: $pid"
        kill_process_with_retry $pid
    done
fi

# Cari dan kill proses yang menggunakan port 8100
echo ""
echo "Mencari proses yang menggunakan port 8100..."
PORT_PIDS=$(lsof -ti:8100 2>/dev/null)

if [ -n "$PORT_PIDS" ]; then
    echo "Ditemukan proses yang menggunakan port 8100: $PORT_PIDS"
    
    for pid in $PORT_PIDS; do
        # Cek apakah PID masih berjalan
        if ps -p $pid > /dev/null 2>&1; then
            echo "Menghentikan proses yang menggunakan port 8100 (PID: $pid)..."
            kill_process_with_retry $pid
        fi
    done
else
    echo "Tidak ada proses yang menggunakan port 8100."
fi

# Final check - pastikan port 8100 benar-benar bebas
echo ""
echo "Melakukan pengecekan final..."
sleep 1

if lsof -Pi :8100 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️  Warning: Port 8100 masih digunakan oleh proses lain!"
    echo "Proses yang masih menggunakan port 8100:"
    lsof -Pi :8100 -sTCP:LISTEN
    echo ""
    echo "Mencoba force kill semua proses di port 8100..."
    lsof -ti:8100 | xargs -r kill -9 2>/dev/null
    sleep 1
    
    if lsof -Pi :8100 -sTCP:LISTEN -t >/dev/null 2>&1; then
        echo "❌ Error: Gagal membebaskan port 8100!"
        exit 1
    else
        echo "✅ Port 8100 berhasil dibebaskan dengan force kill."
    fi
else
    echo "✅ Port 8100 sudah bebas dan siap digunakan."
fi

echo ""
echo "=== Stop server selesai ==="