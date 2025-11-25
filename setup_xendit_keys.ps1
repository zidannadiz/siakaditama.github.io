# Script untuk setup Xendit API Keys
Write-Host "=== Setup Xendit API Keys ===" -ForegroundColor Cyan
Write-Host ""

# Baca file .env
$envPath = ".env"
if (-not (Test-Path $envPath)) {
    Write-Host "Error: File .env tidak ditemukan!" -ForegroundColor Red
    exit
}

# Input Secret Key
Write-Host "Masukkan XENDIT_SECRET_KEY (atau tekan Enter untuk skip):" -ForegroundColor Yellow
$secretKey = Read-Host
if ($secretKey) {
    # Update atau tambahkan XENDIT_SECRET_KEY
    $content = Get-Content $envPath
    $updated = $false
    $newContent = @()
    
    foreach ($line in $content) {
        if ($line -match "^XENDIT_SECRET_KEY=") {
            $newContent += "XENDIT_SECRET_KEY=$secretKey"
            $updated = $true
        } else {
            $newContent += $line
        }
    }
    
    if (-not $updated) {
        $newContent += "XENDIT_SECRET_KEY=$secretKey"
    }
    
    Set-Content -Path $envPath -Value $newContent
    Write-Host "✓ XENDIT_SECRET_KEY sudah diupdate" -ForegroundColor Green
} else {
    Write-Host "Secret Key tidak diupdate" -ForegroundColor Yellow
}

Write-Host ""

# Input Public Key
Write-Host "Masukkan XENDIT_PUBLIC_KEY (atau tekan Enter untuk skip):" -ForegroundColor Yellow
$publicKey = Read-Host
if ($publicKey) {
    $content = Get-Content $envPath
    $updated = $false
    $newContent = @()
    
    foreach ($line in $content) {
        if ($line -match "^XENDIT_PUBLIC_KEY=") {
            $newContent += "XENDIT_PUBLIC_KEY=$publicKey"
            $updated = $true
        } else {
            $newContent += $line
        }
    }
    
    if (-not $updated) {
        $newContent += "XENDIT_PUBLIC_KEY=$publicKey"
    }
    
    Set-Content -Path $envPath -Value $newContent
    Write-Host "✓ XENDIT_PUBLIC_KEY sudah diupdate" -ForegroundColor Green
} else {
    Write-Host "Public Key tidak diupdate" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Setup Selesai ===" -ForegroundColor Cyan
Write-Host "Jalankan 'php artisan config:clear' untuk apply perubahan" -ForegroundColor Yellow

