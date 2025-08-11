#!/bin/bash

# Script untuk menjalankan optimasi RegPeriksa
# Author: AI Assistant
# Date: 2025-01-08

echo "=== RegPeriksa Query Optimization Script ==="
echo "Starting optimization implementation..."
echo ""

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

# Check if we're in Laravel project directory
if [ ! -f "artisan" ]; then
    print_error "artisan file not found. Please run this script from Laravel project root directory."
    exit 1
fi

print_success "Laravel project detected."

# Step 1: Backup database (optional but recommended)
print_status "Step 1: Database backup (recommended)"
read -p "Do you want to backup database before optimization? (y/n): " backup_choice
if [ "$backup_choice" = "y" ] || [ "$backup_choice" = "Y" ]; then
    print_status "Creating database backup..."
    timestamp=$(date +"%Y%m%d_%H%M%S")
    backup_file="backup_before_optimization_${timestamp}.sql"
    
    # Get database credentials from .env
    DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
    DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
    DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
    DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)
    
    if command -v mysqldump &> /dev/null; then
        mysqldump -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$backup_file"
        if [ $? -eq 0 ]; then
            print_success "Database backup created: $backup_file"
        else
            print_warning "Database backup failed, but continuing with optimization..."
        fi
    else
        print_warning "mysqldump not found. Skipping backup."
    fi
else
    print_warning "Skipping database backup."
fi

echo ""

# Step 2: Check current database indexes
print_status "Step 2: Checking current database indexes..."
php artisan tinker --execute="
\$indexes = \Illuminate\Support\Facades\DB::select('SHOW INDEX FROM reg_periksa');
foreach (\$indexes as \$index) {
    echo \$index->Key_name . ' - ' . \$index->Column_name . PHP_EOL;
}"

echo ""

# Step 3: Run migration
print_status "Step 3: Running optimization migration..."
php artisan migrate --path=database/migrations/2025_01_08_000001_add_indexes_for_reg_periksa_optimization.php

if [ $? -eq 0 ]; then
    print_success "Migration completed successfully!"
else
    print_error "Migration failed. Please check the error messages above."
    exit 1
fi

echo ""

# Step 4: Verify indexes were created
print_status "Step 4: Verifying new indexes..."
print_status "New indexes that should be created:"
echo "  - idx_reg_periksa_stts_tgl"
echo "  - idx_reg_periksa_stts_tgl_poli"
echo "  - idx_reg_periksa_stts_tgl_dokter"
echo "  - idx_reg_periksa_tgl_jam"
echo "  - idx_reg_periksa_no_rkm_medis"
echo "  - idx_reg_periksa_kd_dokter"
echo "  - idx_reg_periksa_kd_poli"
echo "  - idx_reg_periksa_kd_pj"

print_status "Current indexes after migration:"
php artisan tinker --execute="
\$indexes = \Illuminate\Support\Facades\DB::select('SHOW INDEX FROM reg_periksa');
foreach (\$indexes as \$index) {
    echo \$index->Key_name . ' - ' . \$index->Column_name . PHP_EOL;
}"

echo ""

# Step 5: Clear caches
print_status "Step 5: Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

if [ $? -eq 0 ]; then
    print_success "Caches cleared successfully!"
else
    print_warning "Some caches might not have been cleared properly."
fi

echo ""

# Step 6: Test query performance
print_status "Step 6: Testing query performance..."
print_status "Running optimized query test..."

php artisan tinker --execute="
\$start = microtime(true);
\$service = new \App\Services\RegPeriksaOptimizationService();
\$query = \$service->getOptimizedQuery();
\$results = \$query->limit(10)->get();
\$end = microtime(true);
\$time = (\$end - \$start) * 1000;
echo 'Query executed in: ' . number_format(\$time, 2) . 'ms' . PHP_EOL;
echo 'Results count: ' . \$results->count() . PHP_EOL;
"

echo ""

# Step 7: Test caching
print_status "Step 7: Testing caching functionality..."
php artisan tinker --execute="
\$service = new \App\Services\RegPeriksaOptimizationService();
echo 'Testing cache functions...' . PHP_EOL;
\$total = \$service->getTotalPasienHariIni();
echo 'Total pasien hari ini: ' . \$total . PHP_EOL;
\$belum = \$service->getTotalPasienBelumPeriksa();
echo 'Total pasien belum periksa: ' . \$belum . PHP_EOL;
"

echo ""

# Step 8: Performance recommendations
print_status "Step 8: Performance recommendations"
echo "After optimization, you should:"
echo "  1. Monitor query performance in logs"
echo "  2. Check cache hit rates"
echo "  3. Monitor memory usage"
echo "  4. Consider implementing query result pagination for large datasets"
echo "  5. Regular database maintenance (OPTIMIZE TABLE)"

echo ""

# Step 9: Final verification
print_status "Step 9: Final verification"
print_status "Checking if RegPeriksaOptimizationService is working..."
php artisan tinker --execute="
try {
    \$service = new \App\Services\RegPeriksaOptimizationService();
    echo 'RegPeriksaOptimizationService: OK' . PHP_EOL;
} catch (Exception \$e) {
    echo 'RegPeriksaOptimizationService: ERROR - ' . \$e->getMessage() . PHP_EOL;
}
"

print_status "Checking if RegPeriksaTable component is working..."
php artisan tinker --execute="
try {
    \$component = new \App\Http\Livewire\RegPeriksaTable();
    \$component->mount();
    echo 'RegPeriksaTable component: OK' . PHP_EOL;
} catch (Exception \$e) {
    echo 'RegPeriksaTable component: ERROR - ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
print_success "=== Optimization Implementation Completed! ==="
print_status "Please check the OPTIMIZATION_IMPLEMENTATION.md file for detailed documentation."
print_status "Monitor your application performance and adjust cache durations as needed."

echo ""
print_status "Next steps:"
echo "  1. Test the registration page to ensure everything works correctly"
echo "  2. Monitor slow query logs to verify improvement"
echo "  3. Adjust cache durations in RegPeriksaOptimizationService if needed"
echo "  4. Consider implementing additional optimizations based on usage patterns"

echo ""
print_success "Optimization script completed successfully!"