# 📱 Panduan Install Flutter untuk Windows

## 🎯 Tujuan
Install Flutter SDK untuk development aplikasi mobile SIAKAD.

## 📋 Langkah-Langkah Install

### 1. Download Flutter SDK

**Cara 1: Download Manual**
1. Buka: https://docs.flutter.dev/get-started/install/windows
2. Klik "Download Flutter SDK"
3. Download file ZIP (sekitar 1.5 GB)
4. Extract ke folder (misal: `C:\flutter`)

**Cara 2: Git Clone (Lebih Update)**
```bash
git clone https://github.com/flutter/flutter.git -b stable C:\flutter
```

### 2. Extract Flutter SDK

1. Extract ZIP file ke folder (misal: `C:\flutter`)
2. Pastikan path: `C:\flutter\bin\flutter.bat` ada

**JANGAN extract ke:**
- ❌ `C:\Program Files\` (perlu admin permission)
- ❌ Folder dengan spasi di nama
- ❌ Folder yang di-protect Windows

**REKOMENDASI:**
- ✅ `C:\flutter`
- ✅ `C:\dev\flutter`
- ✅ `D:\flutter`

### 3. Tambahkan ke PATH

#### Cara A: Via GUI (Recommended)

1. Tekan `Windows + R`
2. Ketik: `sysdm.cpl` → Enter
3. Tab **Advanced** → Klik **Environment Variables**
4. Di bagian **System variables**, cari `Path` → Klik **Edit**
5. Klik **New** → Tambahkan: `C:\flutter\bin`
6. Klik **OK** di semua dialog
7. **Restart terminal/command prompt** (PENTING!)

#### Cara B: Via PowerShell (Admin)

```powershell
# Buka PowerShell sebagai Administrator
[Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\flutter\bin", "Machine")
```

**Restart terminal setelah ini!**

### 4. Install Dependencies

Flutter memerlukan:
- ✅ Git (untuk download packages)
- ✅ Android Studio (untuk Android development)

#### Install Git (Jika Belum)

1. Download: https://git-scm.com/download/win
2. Install dengan default settings
3. Restart terminal

#### Install Android Studio (Untuk Android)

1. Download: https://developer.android.com/studio
2. Install Android Studio
3. Buka Android Studio → **More Actions** → **SDK Manager**
4. Install:
   - ✅ Android SDK
   - ✅ Android SDK Platform
   - ✅ Android Virtual Device (AVD)

### 5. Verifikasi Install

Buka **terminal baru** (PENTING: harus baru setelah set PATH), lalu:

```bash
flutter doctor
```

Output akan menunjukkan apa yang sudah OK dan apa yang perlu diinstall.

**Contoh Output:**
```
[✓] Flutter (Channel stable, 3.x.x)
[✓] Windows Version (Installed version of Windows is version 10 or higher)
[✓] Android toolchain - develop for Android devices
[✓] Chrome - develop for the web
[✓] Visual Studio - develop for Windows
[✓] Android Studio (version 2023.x)
[✓] VS Code (optional)
[✓] Connected device (1 available)
```

### 6. Accept Android Licenses

```bash
flutter doctor --android-licenses
```

Tekan `y` untuk semua license.

### 7. Setup Android Emulator (Opsional)

**Via Android Studio:**
1. Buka Android Studio
2. **More Actions** → **Virtual Device Manager**
3. Klik **Create Device**
4. Pilih device (misal: Pixel 5)
5. Pilih system image (misal: Android 13)
6. Klik **Finish**

**Atau via Command Line:**
```bash
flutter emulators --create
```

### 8. Test Flutter

```bash
# Check Flutter version
flutter --version

# Check devices
flutter devices

# Create test project
flutter create test_app
cd test_app
flutter run
```

---

## 🔧 Troubleshooting

### Flutter Command Not Found

**Masalah:** `flutter` tidak dikenali di terminal

**Solusi:**
1. Pastikan PATH sudah ditambahkan
2. **Restart terminal/command prompt** (WAJIB!)
3. Cek dengan: `echo %PATH%` (harus ada `C:\flutter\bin`)
4. Jika masih tidak bisa, restart komputer

### Flutter Doctor Error

**Masalah:** `flutter doctor` menunjukkan error

**Solusi:**
- Install missing dependencies yang ditunjukkan
- Run `flutter doctor -v` untuk detail lebih lengkap

### Android License Error

**Masalah:** `flutter doctor` menunjukkan Android license tidak accepted

**Solusi:**
```bash
flutter doctor --android-licenses
# Tekan 'y' untuk semua
```

### Git Not Found

**Masalah:** Flutter memerlukan Git

**Solusi:**
1. Install Git: https://git-scm.com/download/win
2. Restart terminal
3. Run `flutter doctor` lagi

---

## ✅ Checklist Install

- [ ] Flutter SDK downloaded & extracted
- [ ] Flutter ditambahkan ke PATH
- [ ] Terminal di-restart
- [ ] `flutter doctor` berhasil
- [ ] Android licenses accepted
- [ ] Android Studio installed (untuk Android)
- [ ] Test project berhasil dibuat & run

---

## 🚀 Setelah Install Selesai

### 1. Create Project SIAKAD

```bash
flutter create siakad_mobile
cd siakad_mobile
```

### 2. Install Dependencies

Edit `pubspec.yaml`, tambahkan:
```yaml
dependencies:
  http: ^1.1.0
  shared_preferences: ^2.2.0
  provider: ^6.0.0
  go_router: ^12.0.0
  intl: ^0.18.0
```

Lalu:
```bash
flutter pub get
```

### 3. Copy Starter Files

Copy file-file dari folder `mobile_app/` ke project Flutter.

### 4. Run App

```bash
# Pastikan backend running
php artisan serve

# Run Flutter (di terminal lain)
cd siakad_mobile
flutter run
```

---

## 📚 Resources

- **Flutter Docs**: https://docs.flutter.dev/get-started/install/windows
- **Flutter Community**: https://flutter.dev/community
- **Flutter Cookbook**: https://docs.flutter.dev/cookbook

---

## ⚠️ Catatan Penting

1. **Restart Terminal**: Setelah set PATH, WAJIB restart terminal
2. **Android Studio**: Diperlukan untuk Android development
3. **Xcode**: Hanya untuk iOS development (perlu Mac)
4. **Disk Space**: Flutter memerlukan sekitar 2-3 GB space

---

**Setelah install selesai, lanjutkan ke `LANGKAH_SELANJUTNYA_MOBILE.md`! 🚀**

