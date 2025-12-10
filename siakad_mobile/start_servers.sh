#!/bin/bash
# Script untuk menjalankan Laravel dan Flutter secara bersamaan
# Untuk Linux/Mac

echo "=== Starting SIAKAD Development Servers ==="
echo ""

# Path ke project Laravel
LARAVEL_PATH="../SIAKAD-BARU"
FLUTTER_PATH="."

# Check if Laravel path exists
if [ ! -d "$LARAVEL_PATH" ]; then
    echo "Error: Laravel project not found at $LARAVEL_PATH"
    echo "Please update the path in this script"
    exit 1
fi

# Start Laravel server in background
echo "Starting Laravel server..."
cd "$LARAVEL_PATH" || exit
php artisan serve > /dev/null 2>&1 &
LARAVEL_PID=$!
cd - > /dev/null || exit

echo "Laravel server started (PID: $LARAVEL_PID)"
echo "Server running on http://127.0.0.1:8000"
echo ""

# Wait a bit for Laravel to start
sleep 3

# Start Flutter app
echo "Starting Flutter app..."
echo ""
echo "Laravel server is running in background (PID: $LARAVEL_PID)"
echo "Press Ctrl+C to stop both servers"
echo ""

# Trap Ctrl+C to kill Laravel server
trap "kill $LARAVEL_PID 2>/dev/null; exit" INT TERM

flutter run

# Kill Laravel server when Flutter exits
kill $LARAVEL_PID 2>/dev/null
