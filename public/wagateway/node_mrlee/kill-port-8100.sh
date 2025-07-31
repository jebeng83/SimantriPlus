#!/bin/bash

# Script untuk menghentikan semua proses yang menggunakan port 8100
# Author: Mr. Lee
# Version: 1.0 - Enhanced port killer

echo "=== Kill Port 8100 - Enhanced Version ==="

# Function untuk kill proses dengan retry
kill_process_with_retry() {
    local pid=$1
    local max_attempts=5
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if ! ps -p $pid > /dev/null 2>&1; then
            echo "✅ Proses dengan PID $pid sudah tidak berjalan."
            return 0
        fi
        
        echo "🔄 Attempt $attempt: Menghentikan proses PID $pid..."
        
        if [ $attempt -le 3 ]; then
            # Gunakan SIGTERM untuk 3 attempt pertama
            kill $pid 2>/dev/null
            echo "   Menggunakan SIGTERM..."
        else
            # Gunakan SIGKILL untuk attempt terakhir
            echo "   Menggunakan SIGKILL (force kill)..."
            kill -9 $pid 2>/dev/null
        fi
        
        # Tunggu proses berhenti
        sleep 2
        
        # Cek apakah proses masih berjalan
        if ! ps -p $pid > /dev/null 2>&1; then
            echo "✅ Proses berhasil dihentikan pada attempt $attempt."
            return 0
        fi
        
        attempt=$((attempt + 1))
    done
    
    echo "❌ Error: Gagal menghentikan proses setelah $max_attempts attempts!"
    return 1
}

# Cek apakah port 8100 digunakan
echo "🔍 Mencari proses yang menggunakan port 8100..."
PORT_PIDS=$(lsof -ti:8100 2>/dev/null)

if [ -z "$PORT_PIDS" ]; then
    echo "ℹ️  Tidak ada proses yang menggunakan port 8100."
    echo "✅ Port 8100 sudah bebas dan siap digunakan."
    exit 0
fi

echo "🎯 Ditemukan proses yang menggunakan port 8100: $PORT_PIDS"
echo ""

# Tampilkan detail proses yang menggunakan port 8100
echo "📋 Detail proses yang menggunakan port 8100:"
lsof -Pi :8100 -sTCP:LISTEN 2>/dev/null
echo ""

# Kill setiap PID yang menggunakan port 8100
for pid in $PORT_PIDS; do
    # Cek apakah PID masih berjalan
    if ps -p $pid > /dev/null 2>&1; then
        echo "🎯 Menghentikan proses PID: $pid"
        kill_process_with_retry $pid
        echo ""
    else
        echo "ℹ️  PID $pid sudah tidak berjalan."
    fi
done

# Final check - pastikan port 8100 benar-benar bebas
echo "🔍 Melakukan pengecekan final..."
sleep 1

if lsof -Pi :8100 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️  Warning: Port 8100 masih digunakan oleh proses lain!"
    echo "📋 Proses yang masih menggunakan port 8100:"
    lsof -Pi :8100 -sTCP:LISTEN
    echo ""
    echo "💥 Mencoba force kill semua proses di port 8100..."
    lsof -ti:8100 | xargs -r kill -9 2>/dev/null
    sleep 2
    
    if lsof -Pi :8100 -sTCP:LISTEN -t >/dev/null 2>&1; then
        echo "❌ Error: Gagal membebaskan port 8100 setelah force kill!"
        echo "📋 Proses yang masih tersisa:"
        lsof -Pi :8100 -sTCP:LISTEN
        exit 1
    else
        echo "✅ Port 8100 berhasil dibebaskan dengan force kill."
    fi
else
    echo "✅ Port 8100 sudah bebas dan siap digunakan."
fi

echo ""
echo "🎉 === Kill Port 8100 selesai ==="