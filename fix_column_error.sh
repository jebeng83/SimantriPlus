#!/bin/bash

# Script untuk memperbaiki error kolom reg_periksa.no_peserta dan reg_periksa.no_ktp

echo "=== Memperbaiki Error Kolom reg_periksa ==="
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
echo "   - Kolom no_peserta dan no_ktp dipindahkan dari reg_periksa ke pasien"
echo "   - Query di RegPeriksaOptimizationService telah diperbaiki"
echo "   - Dokumentasi telah diperbarui"
echo
echo "4. Silakan test aplikasi sekarang!"
echo "   URL: http://localhost/register atau sesuai konfigurasi Anda"
echo
echo "=== Perbaikan Selesai ==="