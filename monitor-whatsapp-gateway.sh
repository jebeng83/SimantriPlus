#!/bin/bash

# WhatsApp Gateway Monitoring Script
# Monitors the health and performance of WhatsApp Node.js Gateway
# Version: 1.0

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
WA_PORT=8100
WA_DIR="public/wagateway/node_mrlee"
LOG_FILE="$WA_DIR/wa-gateway.log"
PID_FILE="$WA_DIR/wa-gateway.pid"
STATUS_URL="http://localhost:$WA_PORT/status"
TROUBLESHOOT_URL="http://localhost:$WA_PORT/troubleshoot"

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

print_header() {
    echo -e "${GREEN}$1${NC}"
    echo "═══════════════════════════════════════════════════════════════"
}

# Function to check if port is open
check_port() {
    if lsof -i :$WA_PORT >/dev/null 2>&1; then
        return 0
    else
        return 1
    fi
}

# Function to check API status
check_api_status() {
    if curl -s --connect-timeout 5 "$STATUS_URL" >/dev/null 2>&1; then
        return 0
    else
        return 1
    fi
}

# Function to get API response
get_api_response() {
    curl -s --connect-timeout 5 "$1" 2>/dev/null || echo '{"error": "API not responding"}'
}

# Function to check system resources
check_system_resources() {
    print_header "🖥️  SYSTEM RESOURCES"
    
    # Memory usage
    echo "Memory Usage:"
    free -h | grep -E "Mem:|Swap:"
    echo ""
    
    # CPU usage
    echo "CPU Usage:"
    top -bn1 | grep "Cpu(s)" | awk '{print $2}' | sed 's/%us,//'
    echo ""
    
    # Disk usage
    echo "Disk Usage:"
    df -h | grep -E "/$|/dev/shm"
    echo ""
    
    # /dev/shm specifically (important for Puppeteer)
    echo "/dev/shm Status:"
    df -h /dev/shm 2>/dev/null || print_warning "/dev/shm not found or not mounted"
    echo ""
}

# Function to check Node.js processes
check_node_processes() {
    print_header "🔍 NODE.JS PROCESSES"
    
    echo "Node.js processes:"
    ps aux | grep -E "node|appJM.js" | grep -v grep || print_warning "No Node.js processes found"
    echo ""
    
    echo "Port $WA_PORT usage:"
    lsof -i :$WA_PORT 2>/dev/null || print_warning "Port $WA_PORT is not in use"
    echo ""
}

# Function to check WhatsApp Gateway status
check_whatsapp_status() {
    print_header "📱 WHATSAPP GATEWAY STATUS"
    
    # Check if port is open
    if check_port; then
        print_success "Port $WA_PORT is open"
    else
        print_error "Port $WA_PORT is not open"
        return 1
    fi
    
    # Check API response
    if check_api_status; then
        print_success "API is responding"
        
        # Get detailed status
        echo ""
        echo "API Status Response:"
        get_api_response "$STATUS_URL" | jq . 2>/dev/null || get_api_response "$STATUS_URL"
        echo ""
        
    else
        print_error "API is not responding"
        return 1
    fi
}

# Function to check troubleshooting info
check_troubleshoot_info() {
    print_header "🔧 TROUBLESHOOTING INFO"
    
    if check_api_status; then
        echo "Troubleshooting Information:"
        get_api_response "$TROUBLESHOOT_URL" | jq . 2>/dev/null || get_api_response "$TROUBLESHOOT_URL"
        echo ""
    else
        print_warning "Cannot get troubleshooting info - API not responding"
    fi
}

# Function to check log files
check_log_files() {
    print_header "📄 LOG FILES"
    
    if [ -f "$LOG_FILE" ]; then
        print_success "Log file found: $LOG_FILE"
        echo "Last 10 lines of log:"
        echo "─────────────────────────────────────────────────────────────"
        tail -10 "$LOG_FILE" 2>/dev/null || print_warning "Cannot read log file"
        echo "─────────────────────────────────────────────────────────────"
        echo ""
        
        # Check for errors in recent logs
        echo "Recent errors in log (last 50 lines):"
        tail -50 "$LOG_FILE" 2>/dev/null | grep -i "error\|failed\|exception" | tail -5 || echo "No recent errors found"
        echo ""
    else
        print_warning "Log file not found: $LOG_FILE"
    fi
    
    # Check PID file
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE" 2>/dev/null)
        if kill -0 "$PID" 2>/dev/null; then
            print_success "PID file found and process is running (PID: $PID)"
        else
            print_warning "PID file found but process is not running (PID: $PID)"
        fi
    else
        print_warning "PID file not found: $PID_FILE"
    fi
    echo ""
}

# Function to check Chrome/Chromium
check_chrome() {
    print_header "🌐 CHROME/CHROMIUM STATUS"
    
    # Check Chrome installations
    if command -v google-chrome-stable &> /dev/null; then
        print_success "Google Chrome Stable found: $(which google-chrome-stable)"
        google-chrome-stable --version 2>/dev/null || print_warning "Cannot get Chrome version"
    else
        print_warning "Google Chrome Stable not found"
    fi
    
    if command -v chromium-browser &> /dev/null; then
        print_success "Chromium Browser found: $(which chromium-browser)"
        chromium-browser --version 2>/dev/null || print_warning "Cannot get Chromium version"
    else
        print_warning "Chromium Browser not found"
    fi
    
    # Check if any Chrome process is running
    echo ""
    echo "Chrome/Chromium processes:"
    ps aux | grep -E "chrome|chromium" | grep -v grep || print_warning "No Chrome/Chromium processes found"
    echo ""
}

# Function to perform health check
perform_health_check() {
    print_header "🏥 HEALTH CHECK SUMMARY"
    
    local issues=0
    
    # Check port
    if ! check_port; then
        print_error "❌ Port $WA_PORT is not open"
        ((issues++))
    else
        print_success "✅ Port $WA_PORT is open"
    fi
    
    # Check API
    if ! check_api_status; then
        print_error "❌ API is not responding"
        ((issues++))
    else
        print_success "✅ API is responding"
    fi
    
    # Check memory (warn if less than 1GB free)
    local free_mem=$(free -m | awk 'NR==2{printf "%.0f", $7}')
    if [ "$free_mem" -lt 1024 ]; then
        print_warning "⚠️ Low memory: ${free_mem}MB free"
        ((issues++))
    else
        print_success "✅ Memory OK: ${free_mem}MB free"
    fi
    
    # Check /dev/shm
    local shm_usage=$(df /dev/shm 2>/dev/null | awk 'NR==2 {print $5}' | sed 's/%//')
    if [ -n "$shm_usage" ] && [ "$shm_usage" -gt 80 ]; then
        print_warning "⚠️ /dev/shm usage high: ${shm_usage}%"
        ((issues++))
    else
        print_success "✅ /dev/shm OK"
    fi
    
    # Check Chrome
    if ! command -v google-chrome-stable &> /dev/null && ! command -v chromium-browser &> /dev/null; then
        print_error "❌ No Chrome/Chromium found"
        ((issues++))
    else
        print_success "✅ Chrome/Chromium available"
    fi
    
    echo ""
    if [ $issues -eq 0 ]; then
        print_success "🎉 All checks passed! WhatsApp Gateway appears healthy."
    else
        print_warning "⚠️ Found $issues issue(s). Check details above."
    fi
    echo ""
}

# Function to show quick actions
show_quick_actions() {
    print_header "⚡ QUICK ACTIONS"
    
    echo "Available actions:"
    echo "1. Restart WhatsApp Gateway: cd $WA_DIR && ./restart-wa-production.sh"
    echo "2. View live logs: tail -f $LOG_FILE"
    echo "3. Check API status: curl $STATUS_URL"
    echo "4. Get troubleshoot info: curl $TROUBLESHOOT_URL"
    echo "5. Manual restart via API: curl $STATUS_URL/restart-client"
    echo "6. Kill port process: kill -9 \$(lsof -t -i:$WA_PORT)"
    echo "7. Full optimization: ./optimize-production.sh"
    echo ""
}

# Main function
main() {
    clear
    echo -e "${GREEN}🚀 WhatsApp Gateway Monitor${NC}"
    echo "═══════════════════════════════════════════════════════════════"
    echo "Timestamp: $(date)"
    echo "Directory: $(pwd)"
    echo ""
    
    # Check if we're in the right directory
    if [ ! -d "$WA_DIR" ]; then
        print_error "WhatsApp directory not found: $WA_DIR"
        print_error "Please run this script from the application root directory"
        exit 1
    fi
    
    # Perform all checks
    check_system_resources
    check_node_processes
    check_whatsapp_status
    check_chrome
    check_log_files
    check_troubleshoot_info
    perform_health_check
    show_quick_actions
    
    echo "Monitor completed at $(date)"
}

# Handle command line arguments
case "${1:-}" in
    "health")
        perform_health_check
        ;;
    "logs")
        check_log_files
        ;;
    "status")
        check_whatsapp_status
        ;;
    "system")
        check_system_resources
        ;;
    "chrome")
        check_chrome
        ;;
    "help")
        echo "Usage: $0 [health|logs|status|system|chrome|help]"
        echo "  health  - Quick health check only"
        echo "  logs    - Check log files only"
        echo "  status  - Check WhatsApp status only"
        echo "  system  - Check system resources only"
        echo "  chrome  - Check Chrome/Chromium only"
        echo "  help    - Show this help"
        echo "  (no args) - Full monitoring report"
        ;;
    *)
        main
        ;;
esac