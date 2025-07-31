#!/bin/bash

# WhatsApp Gateway Node.js Server Status Checker
# This script checks the status of the Node.js server

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PID_FILE="$SCRIPT_DIR/server.pid"
PORT=8100

# Function to check if server process is running
check_process() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p "$PID" > /dev/null 2>&1; then
            echo "Process running with PID: $PID"
            return 0
        else
            echo "PID file exists but process not running"
            rm -f "$PID_FILE"
            return 1
        fi
    else
        # Check if any node process with appJM.js is running
        RUNNING_PID=$(pgrep -f "node.*appJM.js" 2>/dev/null | head -1)
        if [ -n "$RUNNING_PID" ]; then
            echo "Process running with PID: $RUNNING_PID (no PID file)"
            echo "$RUNNING_PID" > "$PID_FILE"
            return 0
        else
            echo "No process running"
            return 1
        fi
    fi
}

# Function to check if port is in use
check_port() {
    if lsof -i :$PORT > /dev/null 2>&1; then
        echo "Port $PORT is in use"
        return 0
    else
        echo "Port $PORT is free"
        return 1
    fi
}

# Function to check server HTTP response
check_http() {
    if curl -s "http://localhost:$PORT/uptime" > /dev/null 2>&1; then
        UPTIME=$(curl -s "http://localhost:$PORT/uptime" 2>/dev/null)
        echo "Server responding: $UPTIME"
        return 0
    else
        echo "Server not responding to HTTP requests"
        return 1
    fi
}

# Function to get server logs
get_logs() {
    LOG_FILE="$SCRIPT_DIR/server.log"
    if [ -f "$LOG_FILE" ]; then
        echo "Last 5 lines of server log:"
        tail -5 "$LOG_FILE"
    else
        echo "No log file found"
    fi
}

# Main status check
echo "=== WhatsApp Gateway Server Status ==="
echo "Checking server status..."
echo "======================================"

# Check process
if check_process; then
    PROCESS_STATUS="running"
else
    PROCESS_STATUS="stopped"
fi

# Check port
if check_port; then
    PORT_STATUS="in_use"
else
    PORT_STATUS="free"
fi

# Check HTTP response
if check_http; then
    HTTP_STATUS="responding"
else
    HTTP_STATUS="not_responding"
fi

# Output JSON status if requested
if [ "$1" = "--json" ]; then
    echo "{"
    echo "  \"process_status\": \"$PROCESS_STATUS\","
    echo "  \"port_status\": \"$PORT_STATUS\","
    echo "  \"http_status\": \"$HTTP_STATUS\","
    echo "  \"port\": $PORT,"
    echo "  \"pid_file\": \"$PID_FILE\","
    echo "  \"timestamp\": \"$(date -u +%Y-%m-%dT%H:%M:%SZ)\""
    echo "}"
else
    echo "Process Status: $PROCESS_STATUS"
    echo "Port Status: $PORT_STATUS"
    echo "HTTP Status: $HTTP_STATUS"
    echo ""
    get_logs
fi

# Exit with appropriate code
if [ "$PROCESS_STATUS" = "running" ] && [ "$HTTP_STATUS" = "responding" ]; then
    exit 0
else
    exit 1
fi