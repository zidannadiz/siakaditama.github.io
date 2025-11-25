# Script untuk menjalankan Flutter App
# Pastikan backend Laravel sudah running: php artisan serve

Write-Host "=== Menjalankan Flutter App ===" -ForegroundColor Green
Write-Host ""

# Set PATH untuk Flutter
$flutterPath = "C:\laragon\www\SIAKAD-BARU\flutter\bin"
$env:PATH = "$flutterPath;$env:PATH"

# Check backend
Write-Host "Pastikan backend Laravel running di: http://127.0.0.1:8000" -ForegroundColor Yellow
Write-Host ""

# Navigate to project
cd C:\laragon\www\SIAKAD-BARU\siakad_mobile

# Check devices
Write-Host "Checking available devices..." -ForegroundColor Cyan
flutter devices
Write-Host ""

# Run app
Write-Host "Menjalankan app di Chrome..." -ForegroundColor Cyan
Write-Host "Tekan 'r' untuk hot reload, 'R' untuk hot restart, 'q' untuk quit" -ForegroundColor Yellow
Write-Host ""

flutter run -d chrome

