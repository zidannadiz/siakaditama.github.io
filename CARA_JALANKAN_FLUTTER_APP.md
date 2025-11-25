# 🚀 Cara Menjalankan Flutter App

## 📋 Prerequisites

1. ✅ Flutter sudah terinstall dan di PATH
2. ✅ Backend Laravel running
3. ✅ Project Flutter sudah dibuat

## 🎯 Langkah-Langkah

### 1. Pastikan Backend Running

**Terminal 1:**
```bash
cd C:\laragon\www\SIAKAD-BARU
php artisan serve
```

Backend akan running di: `http://127.0.0.1:8000`

### 2. Buka Project Flutter

**Terminal 2 (Baru):**
```bash
cd C:\laragon\www\SIAKAD-BARU\siakad_mobile
```

### 3. Check Devices Available

```bash
flutter devices
```

**Output contoh:**
```
3 connected devices:

Windows (desktop) • windows • windows-x64 • Microsoft Windows [Version 10.0.19044.2965]
Chrome (web)      • chrome  • web-javascript • Google Chrome 142.0.0.0
Edge (web)        • edge    • web-javascript • Microsoft Edge 142.0.0.0
```

### 4. Run Flutter App

**Opsi A: Run di Browser (Paling Mudah - Tidak Perlu Emulator)**
```bash
flutter run -d chrome
```

**Opsi B: Run di Windows Desktop**
```bash
flutter run -d windows
```

**Opsi C: Run di Android Emulator (Jika Ada)**
```bash
flutter run -d emulator-5554
```

**Opsi D: Run di Device Pertama yang Tersedia**
```bash
flutter run
```

### 5. Hot Reload

Saat app running, tekan:
- `r` - Hot reload (refresh UI tanpa restart)
- `R` - Hot restart (restart app)
- `q` - Quit (stop app)

---

## 🔧 Setup Project (Jika Belum)

### 1. Create Project

```bash
cd C:\laragon\www\SIAKAD-BARU
flutter create siakad_mobile
cd siakad_mobile
```

### 2. Install Dependencies

Edit `pubspec.yaml`, tambahkan:
```yaml
dependencies:
  flutter:
    sdk: flutter
  
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

Copy file-file dari folder `mobile_app/`:
- `lib/config/api_config.dart`
- `lib/services/api_service.dart`
- `lib/services/storage_service.dart`
- `lib/screens/auth/login_screen.dart`
- Update `lib/main.dart`

### 4. Update API Config

Edit `lib/config/api_config.dart`:
```dart
// Untuk Windows desktop/browser
static const String baseUrl = 'http://127.0.0.1:8000/api';

// Untuk Android emulator (jika pakai emulator)
// static const String baseUrl = 'http://10.0.2.2:8000/api';
```

---

## 🐛 Troubleshooting

### Flutter Command Not Found

**Masalah:** `flutter` tidak dikenali

**Solusi:**
1. Pastikan sudah restart terminal setelah set PATH
2. Cek dengan: `flutter --version`
3. Jika masih error, tambahkan PATH manual

### No Devices Found

**Masalah:** `flutter devices` tidak menunjukkan device

**Solusi:**
- Untuk web: Install Chrome browser
- Untuk Windows: Flutter sudah support Windows desktop
- Untuk Android: Install Android Studio & setup emulator

### Connection Error

**Masalah:** App tidak bisa connect ke backend

**Solusi:**
1. Pastikan backend running: `php artisan serve`
2. Check API config di `lib/config/api_config.dart`
3. Untuk Android emulator, gunakan `10.0.2.2:8000` instead of `127.0.0.1:8000`

### Build Error

**Masalah:** Error saat build/run

**Solusi:**
```bash
# Clean build
flutter clean
flutter pub get
flutter run
```

---

## 📱 Quick Commands

```bash
# Check Flutter
flutter --version

# Check devices
flutter devices

# Run di browser
flutter run -d chrome

# Run di Windows
flutter run -d windows

# Hot reload (saat app running)
# Tekan 'r' di terminal

# Stop app
# Tekan 'q' di terminal

# Clean & rebuild
flutter clean
flutter pub get
flutter run
```

---

## ✅ Checklist

- [ ] Backend running (`php artisan serve`)
- [ ] Flutter project dibuat
- [ ] Dependencies installed (`flutter pub get`)
- [ ] Starter files sudah di-copy
- [ ] API config sudah di-update
- [ ] Device tersedia (`flutter devices`)
- [ ] App berhasil running

---

## 🎯 Langkah Selanjutnya Setelah App Running

1. **Test Login**
   - Email: `noer@gmail.com`
   - Password: `zidanlangut14`
   - Verifikasi token tersimpan

2. **Implementasi Dashboard**
   - Buat dashboard screens
   - Fetch data dari API
   - Tampilkan sesuai role

3. **Implementasi Navigation**
   - Setup routing
   - Navigate setelah login
   - Logout functionality

---

**Selamat! App sudah running! 🚀**

