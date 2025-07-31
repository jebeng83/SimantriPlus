#!/bin/bash

# Script Monitoring Performa Aplikasi Laravel
# Untuk memantau kesehatan aplikasi setelah optimalisasi

echo "🔍 ===== LARAVEL PERFORMANCE MONITOR ====="
echo "📅 Date: $(date)"
echo "🏥 Application: Simantri PLUS"
echo ""

# Function untuk menampilkan status dengan warna
print_status() {
    if [ $1 -eq 0 ]; then
        echo "✅ $2: OK"
    else
        echo "❌ $2: FAILED"
    fi
}

# Function untuk mengecek service
check_service() {
    systemctl is-active --quiet $1
    print_status $? "$1 Service"
}

# 1. Check System Resources
echo "💻 SYSTEM RESOURCES:"
echo "-------------------"
echo "🧠 Memory Usage:"
free -h | grep -E '(Mem|Swap)'
echo ""
echo "💾 Disk Usage:"
df -h | grep -E '(Filesystem|/dev/)' | head -5
echo ""
echo "⚡ CPU Load:"
uptime
echo ""

# 2. Check Services Status
echo "🔧 SERVICES STATUS:"
echo "------------------"
check_service "mysql"
check_service "redis-server"
check_service "php8.1-fpm"
check_service "apache2"
echo ""

# 3. Check Redis
echo "🔴 REDIS STATUS:"
echo "---------------"
redis_ping=$(redis-cli ping 2>/dev/null)
if [ "$redis_ping" = "PONG" ]; then
    echo "✅ Redis: Connected"
    echo "📊 Redis Info:"
    redis-cli info memory | grep -E '(used_memory_human|used_memory_peak_human)'
    redis-cli info stats | grep -E '(total_commands_processed|instantaneous_ops_per_sec)'
else
    echo "❌ Redis: Connection Failed"
fi
echo ""

# 4. Check MySQL
echo "🗄️ MySQL STATUS:"
echo "---------------"
mysql_status=$(mysqladmin -u root ping 2>/dev/null)
if [[ $mysql_status == *"alive"* ]]; then
    echo "✅ MySQL: Connected"
    echo "📊 MySQL Processes:"
    mysql -u root -e "SHOW PROCESSLIST;" 2>/dev/null | wc -l
    echo "📊 MySQL Status:"
    mysql -u root -e "SHOW STATUS LIKE 'Threads_connected';" 2>/dev/null
    mysql -u root -e "SHOW STATUS LIKE 'Queries';" 2>/dev/null
else
    echo "❌ MySQL: Connection Failed"
fi
echo ""

# 5. Check Laravel Application
echo "🚀 LARAVEL APPLICATION:"
echo "----------------------"
cd "$(dirname "$0")"

# Check if Laravel is accessible
if [ -f "artisan" ]; then
    echo "✅ Laravel: Found"
    
    # Check environment
    env_status=$(php artisan env 2>/dev/null)
    echo "🌍 Environment: $env_status"
    
    # Check cache status
    echo "📦 Cache Status:"
    php artisan cache:table 2>/dev/null && echo "✅ Cache table exists" || echo "ℹ️ Using file/redis cache"
    
    # Check queue status
    echo "📋 Queue Status:"
    php artisan queue:work --once --timeout=5 2>/dev/null && echo "✅ Queue working" || echo "⚠️ Queue may have issues"
    
    # Check storage permissions
    if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
        echo "✅ Storage: Writable"
    else
        echo "❌ Storage: Permission issues"
    fi
    
else
    echo "❌ Laravel: Not found or not in correct directory"
fi
echo ""

# 6. Check Application Response Time
echo "⏱️ RESPONSE TIME TEST:"
echo "---------------------"
if command -v curl &> /dev/null; then
    app_url="http://localhost"
    if [ ! -z "$1" ]; then
        app_url="$1"
    fi
    
    echo "🌐 Testing: $app_url"
    response_time=$(curl -o /dev/null -s -w "%{time_total}" "$app_url" 2>/dev/null)
    if [ $? -eq 0 ]; then
        echo "✅ Response Time: ${response_time}s"
        # Evaluate response time
        if (( $(echo "$response_time < 1.0" | bc -l) )); then
            echo "🚀 Performance: Excellent"
        elif (( $(echo "$response_time < 2.0" | bc -l) )); then
            echo "👍 Performance: Good"
        elif (( $(echo "$response_time < 3.0" | bc -l) )); then
            echo "⚠️ Performance: Average"
        else
            echo "🐌 Performance: Slow"
        fi
    else
        echo "❌ Application: Not accessible"
    fi
else
    echo "⚠️ curl not available for response time test"
fi
echo ""

# 7. Check Log Files
echo "📝 LOG FILES STATUS:"
echo "-------------------"
if [ -f "storage/logs/laravel.log" ]; then
    log_size=$(du -h storage/logs/laravel.log | cut -f1)
    echo "📄 Laravel Log Size: $log_size"
    
    # Check for recent errors
    recent_errors=$(tail -100 storage/logs/laravel.log | grep -i "error" | wc -l)
    if [ $recent_errors -gt 0 ]; then
        echo "⚠️ Recent Errors: $recent_errors (check logs)"
    else
        echo "✅ Recent Errors: None"
    fi
else
    echo "ℹ️ No Laravel log file found"
fi
echo ""

# 8. Performance Recommendations
echo "💡 PERFORMANCE RECOMMENDATIONS:"
echo "------------------------------"

# Check OPcache
php_opcache=$(php -m | grep -i opcache)
if [ ! -z "$php_opcache" ]; then
    echo "✅ OPcache: Enabled"
else
    echo "⚠️ OPcache: Not enabled - Consider enabling for better performance"
fi

# Check if running in production
if [ -f ".env" ]; then
    app_env=$(grep "APP_ENV" .env | cut -d '=' -f2)
    if [ "$app_env" = "production" ]; then
        echo "✅ Environment: Production mode"
    else
        echo "⚠️ Environment: Not in production mode"
    fi
fi

# Check cache drivers
if [ -f ".env" ]; then
    cache_driver=$(grep "CACHE_DRIVER" .env | cut -d '=' -f2)
    session_driver=$(grep "SESSION_DRIVER" .env | cut -d '=' -f2)
    
    if [ "$cache_driver" = "redis" ]; then
        echo "✅ Cache Driver: Redis (Optimal)"
    else
        echo "⚠️ Cache Driver: $cache_driver (Consider Redis for better performance)"
    fi
    
    if [ "$session_driver" = "redis" ]; then
        echo "✅ Session Driver: Redis (Optimal)"
    else
        echo "⚠️ Session Driver: $session_driver (Consider Redis for better performance)"
    fi
fi

echo ""
echo "🏁 MONITORING COMPLETE"
echo "======================"
echo "📊 For detailed analysis, check:"
echo "   - Laravel logs: storage/logs/laravel.log"
echo "   - System logs: /var/log/syslog"
echo "   - Apache logs: /var/log/apache2/"
echo "   - PHP-FPM logs: /var/log/php8.1-fpm.log"
echo ""
echo "🔄 Run this script regularly to monitor application health"
echo "⏰ Recommended: Add to crontab for automated monitoring"
echo ""
echo "📞 If issues persist, check OPTIMALISASI-PRODUKSI.md for troubleshooting"