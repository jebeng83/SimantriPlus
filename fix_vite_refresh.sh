#!/bin/bash

# Script to fix @viteReactRefresh in all Blade templates
# This prevents CORS issues in production by only loading React refresh in development

echo "Fixing @viteReactRefresh in Blade templates..."

# List of files to fix
files=(
    "resources/views/auth/login-premium.blade.php"
    "resources/views/laporan/index.blade.php"
    "resources/views/mobile-jkn/home.blade.php"
    "resources/views/eppbgm/index.blade.php"
    "resources/views/farmasi/index.blade.php"
    "resources/views/farmasi/industri-farmasi.blade.php"
    "resources/views/farmasi/set-harga-obat.blade.php"
    "resources/views/farmasi/jenis-obat.blade.php"
    "resources/views/farmasi/data-suplier.blade.php"
    "resources/views/farmasi/permintaan-medis.blade.php"
    "resources/views/farmasi/kategori-obat.blade.php"
    "resources/views/farmasi/data-obat.blade.php"
    "resources/views/farmasi/golongan-obat.blade.php"
    "resources/views/farmasi/satuan-barang.blade.php"
    "resources/views/reg_periksa/index.blade.php"
    "resources/views/Pcare/index.blade.php"
    "resources/views/ilp/index.blade.php"
    "resources/views/react/kegiatan-ukm.blade.php"
    "resources/views/react/jadwal_ukm.blade.php"
    "resources/views/react/display-kegiatan-ukm.blade.php"
    "resources/views/react/antri-poli.blade.php"
    "resources/views/react/matrik-kegiatan-ukm.blade.php"
    "resources/views/react/antrian-display.blade.php"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "Processing: $file"
        # Replace @viteReactRefresh with environment-conditional version
        sed -i.bak 's/@viteReactRefresh/@if(app()->environment('\''local'\'', '\''development'\''))\
        @viteReactRefresh\
    @endif/g' "$file"
        
        # Remove backup file
        rm "${file}.bak" 2>/dev/null
        
        echo "✓ Fixed: $file"
    else
        echo "✗ File not found: $file"
    fi
done

echo "All files processed!"
echo "Note: @viteReactRefresh will now only load in local/development environments."