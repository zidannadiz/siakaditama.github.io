# Script Install Flutter untuk Windows
# Jalankan sebagai Administrator: .\install_flutter.ps1

Write-Host "=== Install Flutter untuk Windows ===" -ForegroundColor Green
Write-Host ""

# Check apakah sudah ada Flutter
Write-Host "1. Checking Flutter installation..." -ForegroundColor Yellow
try {
    $flutterVersion = flutter --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Flutter sudah terinstall!" -ForegroundColor Green
        Write-Host $flutterVersion
        exit 0
    }
} catch {
    Write-Host "✗ Flutter belum terinstall" -ForegroundColor Red
}

# Check folder C:\flutter
Write-Host ""
Write-Host "2. Checking Flutter folder..." -ForegroundColor Yellow
$flutterPath = "C:\flutter"
if (Test-Path $flutterPath) {
    Write-Host "✓ Folder C:\flutter sudah ada" -ForegroundColor Green
} else {
    Write-Host "✗ Folder C:\flutter belum ada" -ForegroundColor Red
    Write-Host ""
    Write-Host "Langkah selanjutnya:" -ForegroundColor Yellow
    Write-Host "1. Download Flutter SDK dari: https://docs.flutter.dev/get-started/install/windows"
    Write-Host "2. Extract ke C:\flutter"
    Write-Host "3. Jalankan script ini lagi"
    exit 1
}

# Check flutter.bat
$flutterBat = "$flutterPath\bin\flutter.bat"
if (Test-Path $flutterBat) {
    Write-Host "✓ flutter.bat ditemukan" -ForegroundColor Green
} else {
    Write-Host "✗ flutter.bat tidak ditemukan di $flutterBat" -ForegroundColor Red
    Write-Host "Pastikan Flutter SDK sudah di-extract dengan benar"
    exit 1
}

# Check PATH
Write-Host ""
Write-Host "3. Checking PATH..." -ForegroundColor Yellow
$flutterBinPath = "$flutterPath\bin"
$currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")
if ($currentPath -like "*$flutterBinPath*") {
    Write-Host "✓ Flutter sudah ada di PATH" -ForegroundColor Green
} else {
    Write-Host "✗ Flutter belum ada di PATH" -ForegroundColor Red
    Write-Host ""
    Write-Host "Menambahkan Flutter ke PATH..." -ForegroundColor Yellow
    
    try {
        $newPath = "$currentPath;$flutterBinPath"
        [Environment]::SetEnvironmentVariable("Path", $newPath, "Machine")
        Write-Host "✓ Flutter ditambahkan ke PATH" -ForegroundColor Green
        Write-Host ""
        Write-Host "⚠️  PENTING: Restart terminal/PowerShell setelah ini!" -ForegroundColor Yellow
        Write-Host "Setelah restart, jalankan: flutter doctor"
    } catch {
        Write-Host "✗ Gagal menambahkan ke PATH" -ForegroundColor Red
        Write-Host "Error: $_" -ForegroundColor Red
        Write-Host ""
        Write-Host "Coba tambahkan manual:" -ForegroundColor Yellow
        Write-Host "1. Tekan Windows + R"
        Write-Host "2. Ketik: sysdm.cpl"
        Write-Host "3. Tab Advanced → Environment Variables"
        Write-Host "4. Edit Path → Tambahkan: $flutterBinPath"
    }
}

# Check Git
Write-Host ""
Write-Host "4. Checking Git..." -ForegroundColor Yellow
try {
    $gitVersion = git --version 2>&1
    Write-Host "✓ Git terinstall: $gitVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Git belum terinstall" -ForegroundColor Red
    Write-Host "Download dari: https://git-scm.com/download/win" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Selesai ===" -ForegroundColor Green
Write-Host ""
Write-Host "Langkah selanjutnya:" -ForegroundColor Yellow
Write-Host "1. RESTART terminal/PowerShell (WAJIB!)"
Write-Host "2. Jalankan: flutter doctor"
Write-Host "3. Install dependencies yang ditunjukkan"
Write-Host "4. Jalankan: flutter doctor --android-licenses (jika install Android Studio)"

