# Script untuk menjalankan Laravel dan Flutter secara bersamaan
# Untuk Windows PowerShell

Write-Host "=== Starting SIAKAD Development Servers ===" -ForegroundColor Green
Write-Host ""

# Path ke project Laravel
$laravelPath = "..\SIAKAD-BARU"
$flutterPath = "."

# Check if Laravel path exists
if (-not (Test-Path $laravelPath)) {
    Write-Host "Error: Laravel project not found at $laravelPath" -ForegroundColor Red
    Write-Host "Please update the path in this script" -ForegroundColor Yellow
    exit 1
}

# Start Laravel server in new window
Write-Host "Starting Laravel server..." -ForegroundColor Cyan
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$laravelPath'; Write-Host 'Laravel Server Running on http://127.0.0.1:8000' -ForegroundColor Green; php artisan serve"

# Wait a bit for Laravel to start
Start-Sleep -Seconds 3

# Start Flutter app
Write-Host "Starting Flutter app..." -ForegroundColor Cyan
Write-Host ""
Write-Host "Laravel server is running in a separate window" -ForegroundColor Yellow
Write-Host "You can close this window after Flutter starts" -ForegroundColor Yellow
Write-Host ""

flutter run
