# 🚀 Install Flutter di Windows - Step by Step

## ✅ Status Saat Ini

Flutter **BELUM** terinstall di sistem Anda.

## 📋 Langkah-Langkah Install

### 1. Download Flutter SDK

**Opsi A: Download Manual (Recommended)**

1. Buka browser, kunjungi: https://docs.flutter.dev/get-started/install/windows
2. Scroll ke bagian "Get the Flutter SDK"
3. Klik tombol "Download Flutter SDK"
4. Download file ZIP (sekitar 1.5 GB)
5. Tunggu sampai download selesai

**Opsi B: Download via PowerShell**

```powershell
# Download Flutter SDK
$url = "https://storage.googleapis.com/flutter_infra_release/releases/stable/windows/flutter_windows_3.24.0-stable.zip"
$output = "$env:USERPROFILE\Downloads\flutter.zip"
Invoke-WebRequest -Uri $url -OutFile $output
Write-Host "Download selesai di: $output"
```

### 2. Extract Flutter SDK

**Via File Explorer:**

1. Buka folder Downloads
2. Klik kanan file `flutter_windows_xxx-stable.zip`
3. Pilih "Extract All..."
4. Extract ke: `C:\flutter`
    - **JANGAN** extract ke `C:\Program Files\`
    - **JANGAN** extract ke folder dengan spasi

**Via PowerShell:**

```powershell
# Extract ke C:\flutter
$zipFile = "$env:USERPROFILE\Downloads\flutter_windows_3.24.0-stable.zip"
$extractPath = "C:\flutter"
Expand-Archive -Path $zipFile -DestinationPath $extractPath -Force
Write-Host "Extract selesai di: $extractPath"
```

### 3. Tambahkan Flutter ke PATH

**Via GUI (Recommended):**

1. Tekan `Windows + R`
2. Ketik: `sysdm.cpl` → Enter
3. Tab **Advanced** → Klik **Environment Variables**
4. Di bagian **System variables**, scroll ke bawah, cari `Path`
5. Klik **Edit**
6. Klik **New**
7. Tambahkan: `C:\flutter\bin`
8. Klik **OK** di semua dialog
9. **Restart PowerShell/Terminal** (WAJIB!)

**Via PowerShell (Admin):**

```powershell
# Buka PowerShell sebagai Administrator
# Klik kanan PowerShell → Run as Administrator

# Tambahkan ke System PATH
$flutterPath = "C:\flutter\bin"
$currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")
[Environment]::SetEnvironmentVariable("Path", "$currentPath;$flutterPath", "Machine")
Write-Host "Flutter ditambahkan ke PATH. Silakan restart terminal."
```

**PENTING:** Setelah set PATH, **WAJIB restart terminal/PowerShell!**

### 4. Verifikasi Install

Buka **PowerShell baru** (PENTING: harus baru setelah set PATH), lalu:

```powershell
# Check Flutter version
flutter --version

# Check Flutter doctor
flutter doctor
```

**Expected Output:**

```
Flutter 3.24.0 • channel stable • https://github.com/flutter/flutter.git
Framework • revision xxxxx
Engine • revision xxxxx
Tools • Dart 3.x.x • DevTools 2.x.x
```

### 5. Install Dependencies yang Diperlukan

Flutter akan menunjukkan apa yang perlu diinstall. Install yang diperlukan:

#### A. Git (Jika Belum Ada)

```powershell
# Check apakah Git sudah ada
git --version

# Jika belum ada, download dari:
# https://git-scm.com/download/win
```

#### B. Android Studio (Untuk Android Development)

1. Download: https://developer.android.com/studio
2. Install dengan default settings
3. Buka Android Studio
4. **More Actions** → **SDK Manager**
5. Install:
    - ✅ Android SDK
    - ✅ Android SDK Platform
    - ✅ Android Virtual Device (AVD)

#### C. Accept Android Licenses

```powershell
flutter doctor --android-licenses
# Tekan 'y' untuk semua license
```

### 6. Setup Android Emulator (Opsional)

**Via Android Studio:**

1. Buka Android Studio
2. **More Actions** → **Virtual Device Manager**
3. Klik **Create Device**
4. Pilih device (misal: Pixel 5)
5. Pilih system image (misal: Android 13)
6. Klik **Finish**

**Atau via Command Line:**

```powershell
flutter emulators --create
```

---

## 🔧 Troubleshooting

### Flutter Command Not Found

**Masalah:** Setelah set PATH, `flutter` masih tidak dikenali

**Solusi:**

1. **Restart terminal/PowerShell** (WAJIB!)
2. Cek PATH dengan:

    ```powershell
    $env:PATH -split ';' | Select-String -Pattern "flutter"
    ```

    Harus muncul: `C:\flutter\bin`

3. Jika tidak muncul, cek apakah folder `C:\flutter\bin` ada:

    ```powershell
    Test-Path "C:\flutter\bin\flutter.bat"
    ```

    Harus return: `True`

4. Jika masih tidak bisa, restart komputer

### Flutter Doctor Error

**Masalah:** `flutter doctor` menunjukkan banyak error

**Solusi:**

-   Install missing dependencies yang ditunjukkan
-   Run `flutter doctor -v` untuk detail lebih lengkap
-   Ikuti saran dari output `flutter doctor`

### Android License Error

**Masalah:** Android licenses tidak accepted

**Solusi:**

```powershell
flutter doctor --android-licenses
# Tekan 'y' untuk semua license yang muncul
```

### Git Not Found

**Masalah:** Flutter memerlukan Git

**Solusi:**

1. Download Git: https://git-scm.com/download/win
2. Install dengan default settings
3. Restart terminal
4. Run `flutter doctor` lagi

---

## ✅ Checklist Install

Setelah install, verifikasi dengan checklist ini:

-   [ ] Flutter SDK downloaded & extracted ke `C:\flutter`
-   [ ] Folder `C:\flutter\bin` ada dan berisi `flutter.bat`
-   [ ] Flutter ditambahkan ke PATH (`C:\flutter\bin`)
-   [ ] Terminal/PowerShell di-restart
-   [ ] `flutter --version` berhasil (menampilkan version)
-   [ ] `flutter doctor` berhasil (menampilkan status)
-   [ ] Android licenses accepted (jika install Android Studio)
-   [ ] Git terinstall (jika belum, install dulu)

---

## 🚀 Setelah Install Selesai

### 1. Test Flutter

```powershell
# Check version
flutter --version

# Check doctor
flutter doctor

# Check devices
flutter devices
```

### 2. Create Test Project

```powershell
# Create test project
flutter create test_app
cd test_app

# Run di browser (tidak perlu emulator)
flutter run -d chrome
```

### 3. Create SIAKAD Mobile Project

```powershell
# Kembali ke folder project
cd C:\laragon\www\SIAKAD-BARU

# Create Flutter project
flutter create siakad_mobile
cd siakad_mobile

# Install dependencies
# Edit pubspec.yaml, tambahkan dependencies, lalu:
flutter pub get
```

---

## 📝 Quick Commands

```powershell
# Check Flutter
flutter --version

# Check status
flutter doctor

# Check devices
flutter devices

# Create project
flutter create siakad_mobile

# Run app
flutter run

# Run di specific device
flutter run -d chrome        # Browser
flutter run -d windows      # Windows desktop
flutter devices             # List semua devices
```

---

## ⚠️ Catatan Penting

1. **Restart Terminal**: Setelah set PATH, WAJIB restart terminal
2. **Disk Space**: Flutter memerlukan sekitar 2-3 GB space
3. **Internet**: Diperlukan untuk download packages pertama kali
4. **Android Studio**: Diperlukan untuk Android development
5. **Xcode**: Hanya untuk iOS (perlu Mac)

---

**Ikuti langkah-langkah di atas secara berurutan! 🚀**
