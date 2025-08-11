#!/bin/bash

# Script untuk memperbaiki error "Call to a member function getOptimizedQuery() on null"

echo "=== Memperbaiki Error Null OptimizationService ==="
echo "Tanggal: $(date)"
echo

echo "1. Membersihkan cache aplikasi..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

echo
echo "2. Cache berhasil dibersihkan!"
echo
echo "3. Perubahan yang telah dilakukan:"
echo "   - Menambahkan pengecekan null untuk optimizationService"
echo "   - Membuat helper method initializeOptimizationService()"
echo "   - Memastikan service selalu terinisialisasi sebelum digunakan"
echo "   - Menerapkan perbaikan di method builder() dan refreshData()"
echo
echo "4. Silakan test aplikasi sekarang!"
echo "   URL: http://localhost/register atau sesuai konfigurasi Anda"
echo
echo "=== Perbaikan Error Null Service Selesai ==="