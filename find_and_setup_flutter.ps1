# Script untuk mencari dan setup Flutter setelah clone
# Jalankan sebagai Administrator untuk set PATH

Write-Host "=== Mencari Flutter di Sistem ===" -ForegroundColor Green
Write-Host ""

# Lokasi-lokasi umum Flutter
$searchPaths = @(
    "C:\flutter",
    "C:\Users\$env:USERNAME\flutter",
    "C:\dev\flutter",
    "D:\flutter",
    "C:\tools\flutter",
    "$env:USERPROFILE\flutter",
    "$env:USERPROFILE\Documents\flutter",
    "$env:USERPROFILE\Downloads\flutter"
)

$foundFlutter = $null

Write-Host "Mencari Flutter di lokasi umum..." -ForegroundColor Yellow
foreach ($path in $searchPaths) {
    if (Test-Path $path) {
        $flutterBat = Join-Path $path "bin\flutter.bat"
        if (Test-Path $flutterBat) {
            Write-Host "✓ Flutter ditemukan di: $path" -ForegroundColor Green
            $foundFlutter = $path
            break
        }
    }
}

# Jika tidak ditemukan di lokasi umum, cari di C:\
if (-not $foundFlutter) {
    Write-Host ""
    Write-Host "Mencari di C:\ (mungkin butuh waktu)..." -ForegroundColor Yellow
    $flutterDirs = Get-ChildItem -Path C:\ -Directory -Filter "flutter" -ErrorAction SilentlyContinue -Depth 2 | Select-Object -First 5
    
    foreach ($dir in $flutterDirs) {
        $flutterBat = Join-Path $dir.FullName "bin\flutter.bat"
        if (Test-Path $flutterBat) {
            Write-Host "✓ Flutter ditemukan di: $($dir.FullName)" -ForegroundColor Green
            $foundFlutter = $dir.FullName
            break
        }
    }
}

if (-not $foundFlutter) {
    Write-Host ""
    Write-Host "✗ Flutter tidak ditemukan di lokasi umum" -ForegroundColor Red
    Write-Host ""
    Write-Host "Tolong beri tahu:" -ForegroundColor Yellow
    Write-Host "1. Di mana lokasi folder Flutter yang sudah di-clone?" -ForegroundColor White
    Write-Host "2. Atau jalankan script ini dengan path:" -ForegroundColor White
    Write-Host "   .\find_and_setup_flutter.ps1 -FlutterPath 'C:\path\to\flutter'" -ForegroundColor Cyan
    exit 1
}

Write-Host ""
Write-Host "=== Setup PATH ===" -ForegroundColor Green
Write-Host ""

$flutterBinPath = Join-Path $foundFlutter "bin"

# Check apakah sudah di PATH
$currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")
if ($currentPath -like "*$flutterBinPath*") {
    Write-Host "✓ Flutter sudah ada di PATH" -ForegroundColor Green
} else {
    Write-Host "Menambahkan Flutter ke PATH..." -ForegroundColor Yellow
    
    try {
        $newPath = "$currentPath;$flutterBinPath"
        [Environment]::SetEnvironmentVariable("Path", $newPath, "Machine")
        Write-Host "✓ Flutter ditambahkan ke PATH: $flutterBinPath" -ForegroundColor Green
        Write-Host ""
        Write-Host "⚠️  PENTING: Restart terminal/PowerShell setelah ini!" -ForegroundColor Yellow
    } catch {
        Write-Host "✗ Gagal menambahkan ke PATH" -ForegroundColor Red
        Write-Host "Error: $_" -ForegroundColor Red
        Write-Host ""
        Write-Host "Coba tambahkan manual:" -ForegroundColor Yellow
        Write-Host "1. Tekan Windows + R" -ForegroundColor White
        Write-Host "2. Ketik: sysdm.cpl" -ForegroundColor White
        Write-Host "3. Tab Advanced → Environment Variables" -ForegroundColor White
        Write-Host "4. Edit Path → Tambahkan: $flutterBinPath" -ForegroundColor White
    }
}

Write-Host ""
Write-Host "=== Test Flutter ===" -ForegroundColor Green
Write-Host ""

# Test dengan path langsung (tidak perlu restart)
$flutterBat = Join-Path $foundFlutter "bin\flutter.bat"
if (Test-Path $flutterBat) {
    Write-Host "Testing Flutter..." -ForegroundColor Yellow
    & $flutterBat --version
} else {
    Write-Host "✗ flutter.bat tidak ditemukan" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Selesai ===" -ForegroundColor Green
Write-Host ""
Write-Host "Langkah selanjutnya:" -ForegroundColor Yellow
Write-Host "1. RESTART terminal/PowerShell (WAJIB!)" -ForegroundColor White
Write-Host "2. Jalankan: flutter --version" -ForegroundColor White
Write-Host "3. Jalankan: flutter doctor" -ForegroundColor White
Write-Host "4. Install dependencies yang ditunjukkan" -ForegroundColor White

