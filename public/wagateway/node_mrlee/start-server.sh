#!/bin/bash

# WhatsApp Gateway Node.js Server Startup Script
# Enhanced version for better reliability
# Author: Mr. Lee
# Version: 2.0

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_FILE="$SCRIPT_DIR/appJM.js"
LOG_FILE="$SCRIPT_DIR/server.log"
PID_FILE="$SCRIPT_DIR/server.pid"
PORT=8100

echo "=== WhatsApp Gateway Node.js Server ==="
echo "Enhanced startup script v2.0"
echo "Script directory: $SCRIPT_DIR"
echo "App file: $APP_FILE"
echo "Log file: $LOG_FILE"
echo "PID file: $PID_FILE"
echo "Port: $PORT"
echo "======================================"

# Function to check if Node.js is installed
check_node() {
    if ! command -v node &> /dev/null; then
        echo "Error: Node.js tidak ditemukan!"
        echo "Silakan install Node.js terlebih dahulu."
        exit 1
    fi
    echo "✓ Node.js version: $(node --version)"
}

# Function to check if npm is installed
check_npm() {
    if ! command -v npm &> /dev/null; then
        echo "Error: npm tidak ditemukan!"
        echo "Silakan install npm terlebih dahulu."
        exit 1
    fi
    echo "✓ npm version: $(npm --version)"
}

# Function to check and install dependencies
check_dependencies() {
    if [ -f "$SCRIPT_DIR/package.json" ] && [ ! -d "$SCRIPT_DIR/node_modules" ]; then
        echo "Installing dependencies..."
        cd "$SCRIPT_DIR"
        npm install
        if [ $? -ne 0 ]; then
            echo "Error: Failed to install dependencies"
            exit 1
        fi
    fi
    echo "✓ Dependencies check: OK"
}

# Function to check and free port if needed
check_port() {
    if lsof -i :$PORT &> /dev/null; then
        echo "Warning: Port $PORT sudah digunakan!"
        echo "Attempting to free port..."
        
        # Try to kill existing processes
        pkill -f "node.*appJM.js" 2>/dev/null || true
        lsof -ti:$PORT | xargs kill -9 2>/dev/null || true
        
        sleep 2
        
        if lsof -i :$PORT &> /dev/null; then
            echo "Error: Unable to free port $PORT"
            echo "Silakan hentikan proses yang menggunakan port tersebut secara manual"
            exit 1
        fi
        echo "✓ Port $PORT berhasil dibebaskan"
    else
        echo "✓ Port $PORT tersedia"
    fi
}

# Function to start server in background mode
start_background() {
    cd "$SCRIPT_DIR"
    
    # Set environment variables
    export NODE_ENV=production
    export PORT=$PORT
    
    # Start the server in background
    echo "Starting Node.js server in background..."
    nohup node "$APP_FILE" > "$LOG_FILE" 2>&1 &
    
    # Get the PID
    SERVER_PID=$!
    echo $SERVER_PID > "$PID_FILE"
    
    echo "✓ Server started with PID: $SERVER_PID"
    
    # Wait a moment for server to start
    sleep 3
    
    # Check if process is still running
    if ! ps -p $SERVER_PID > /dev/null 2>&1; then
        echo "Error: Server process died immediately"
        echo "Check log file: $LOG_FILE"
        if [ -f "$LOG_FILE" ]; then
            echo "Last 10 lines of log:"
            tail -10 "$LOG_FILE"
        fi
        exit 1
    fi
    
    echo "✓ Server process is running (PID: $SERVER_PID)"
    
    # Test server connectivity
    echo "Testing server connectivity..."
    for i in {1..10}; do
        if curl -s "http://localhost:$PORT/uptime" > /dev/null 2>&1; then
            echo "✓ Server is responding on port $PORT"
            echo "✓ Startup successful!"
            echo "Server URL: http://localhost:$PORT"
            echo "Log file: $LOG_FILE"
            echo "PID file: $PID_FILE"
            return 0
        fi
        echo "Attempt $i/10: Server not yet responding, waiting..."
        sleep 2
    done
    
    echo "Warning: Server started but not responding to HTTP requests"
    echo "Check log file: $LOG_FILE"
    echo "Server PID: $SERVER_PID"
    return 1
}

# Function to start server in foreground mode
start_foreground() {
    cd "$SCRIPT_DIR"
    
    # Set environment variables
    export NODE_ENV=development
    export PORT=$PORT
    
    echo "Starting WhatsApp Gateway on port $PORT..."
    echo "Tekan Ctrl+C untuk menghentikan server"
    echo "======================================"
    
    node "$APP_FILE"
    
    echo "Server stopped."
}

# Main execution
echo "Checking system requirements..."
check_node
check_npm
check_dependencies
check_port

# Check if background mode is requested
if [ "$1" = "--background" ] || [ "$1" = "-bg" ]; then
    start_background
else
    start_foreground
fi