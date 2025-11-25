# 🔧 Setup Flutter Setelah Clone dari GitHub

## ✅ Status
Anda sudah clone Flutter dari GitHub. Sekarang perlu setup PATH agar Flutter bisa digunakan.

## 📋 Langkah-Langkah Setup

### 1. Cari Lokasi Flutter

Flutter yang sudah di-clone biasanya ada di:
- `C:\flutter` (jika clone ke C:\)
- `C:\Users\YourName\flutter` (jika clone ke home)
- `D:\flutter` (jika clone ke D:\)
- Atau lokasi lain sesuai tempat Anda clone

**Cara cari:**
```powershell
# Cari folder flutter
Get-ChildItem -Path C:\ -Directory -Filter "flutter" -ErrorAction SilentlyContinue | Select-Object FullName

# Atau cari di D:\
Get-ChildItem -Path D:\ -Directory -Filter "flutter" -ErrorAction SilentlyContinue | Select-Object FullName
```

**Atau cek manual:**
- Buka File Explorer
- Cari folder bernama `flutter`
- Catat path lengkapnya (misal: `C:\Users\malik\flutter`)

### 2. Verifikasi Flutter

Setelah menemukan lokasi, verifikasi:
```powershell
# Ganti PATH dengan lokasi Flutter Anda
$flutterPath = "C:\Users\malik\flutter"  # GANTI dengan path Anda
Test-Path "$flutterPath\bin\flutter.bat"
```

Harus return: `True`

### 3. Tambahkan ke PATH

**Cara A: Via GUI (Recommended)**

1. Tekan `Windows + R`
2. Ketik: `sysdm.cpl` → Enter
3. Tab **Advanced** → **Environment Variables**
4. Di **System variables**, cari `Path` → **Edit**
5. Klik **New**
6. Tambahkan path: `C:\Users\malik\flutter\bin` (ganti dengan path Flutter Anda)
7. Klik **OK** di semua dialog
8. **Restart PowerShell/Terminal** (WAJIB!)

**Cara B: Via PowerShell (Admin)**

```powershell
# Buka PowerShell sebagai Administrator
# Klik kanan PowerShell → Run as Administrator

# Ganti dengan path Flutter Anda
$flutterPath = "C:\Users\malik\flutter\bin"  # GANTI!

# Tambahkan ke System PATH
$currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")
[Environment]::SetEnvironmentVariable("Path", "$currentPath;$flutterPath", "Machine")

Write-Host "Flutter ditambahkan ke PATH: $flutterPath" -ForegroundColor Green
Write-Host "Silakan RESTART terminal setelah ini!" -ForegroundColor Yellow
```

### 4. Verifikasi Setup

**Buka PowerShell baru** (PENTING: harus baru setelah set PATH), lalu:

```powershell
# Check Flutter
flutter --version

# Check status
flutter doctor
```

**Expected Output:**
```
Flutter 3.x.x • channel stable
Framework • revision xxxxx
Engine • revision xxxxx
```

### 5. Install Dependencies

Flutter akan menunjukkan apa yang perlu diinstall:

```powershell
flutter doctor
```

Install yang diperlukan:
- ✅ Git (jika belum): https://git-scm.com/download/win
- ✅ Android Studio (untuk Android): https://developer.android.com/studio

### 6. Accept Android Licenses (Jika Install Android Studio)

```powershell
flutter doctor --android-licenses
# Tekan 'y' untuk semua license
```

---

## 🔧 Script Helper

Saya sudah membuat script `install_flutter.ps1` yang bisa membantu. Tapi karena Flutter sudah di-clone, gunakan script ini:

**Buat file `setup_flutter_path.ps1`:**

```powershell
# Setup Flutter PATH
# Jalankan sebagai Administrator

# GANTI dengan path Flutter Anda
$flutterPath = "C:\Users\malik\flutter"  # GANTI INI!

# Check apakah folder ada
if (-not (Test-Path "$flutterPath\bin\flutter.bat")) {
    Write-Host "✗ Flutter tidak ditemukan di: $flutterPath" -ForegroundColor Red
    Write-Host "Silakan ganti `$flutterPath dengan path Flutter Anda" -ForegroundColor Yellow
    exit 1
}

Write-Host "✓ Flutter ditemukan di: $flutterPath" -ForegroundColor Green

# Check PATH
$flutterBinPath = "$flutterPath\bin"
$currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")

if ($currentPath -like "*$flutterBinPath*") {
    Write-Host "✓ Flutter sudah ada di PATH" -ForegroundColor Green
} else {
    Write-Host "Menambahkan Flutter ke PATH..." -ForegroundColor Yellow
    $newPath = "$currentPath;$flutterBinPath"
    [Environment]::SetEnvironmentVariable("Path", $newPath, "Machine")
    Write-Host "✓ Flutter ditambahkan ke PATH" -ForegroundColor Green
    Write-Host ""
    Write-Host "⚠️  PENTING: Restart terminal setelah ini!" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Setelah restart terminal, jalankan:" -ForegroundColor Cyan
Write-Host "  flutter --version" -ForegroundColor White
Write-Host "  flutter doctor" -ForegroundColor White
```

---

## ✅ Checklist

- [ ] Lokasi Flutter ditemukan
- [ ] `flutter.bat` ada di `{path}\bin\flutter.bat`
- [ ] Flutter ditambahkan ke PATH
- [ ] Terminal di-restart
- [ ] `flutter --version` berhasil
- [ ] `flutter doctor` berhasil
- [ ] Dependencies diinstall (Git, Android Studio)
- [ ] Android licenses accepted (jika install Android Studio)

---

## 🚀 Setelah Setup Selesai

### 1. Test Flutter

```powershell
flutter --version
flutter doctor
```

### 2. Create SIAKAD Mobile Project

```powershell
cd C:\laragon\www\SIAKAD-BARU
flutter create siakad_mobile
cd siakad_mobile
```

### 3. Install Dependencies

Edit `pubspec.yaml`, tambahkan dependencies, lalu:
```powershell
flutter pub get
```

### 4. Copy Starter Files

Copy file-file dari folder `mobile_app/` ke project Flutter.

---

## ❓ Jika Masih Ada Masalah

**Tolong beri tahu:**
1. Di mana lokasi folder Flutter yang sudah di-clone? (path lengkap)
2. Apakah folder `bin\flutter.bat` ada di sana?
3. Error apa yang muncul saat jalankan `flutter --version`?

Dengan informasi ini, saya bisa bantu troubleshoot lebih detail.

