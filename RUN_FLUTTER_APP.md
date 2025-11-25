# 🚀 Cara Menjalankan Flutter App - Step by Step

## 📋 Langkah-Langkah Lengkap

### Step 1: Create Flutter Project

Buka terminal/PowerShell, lalu:

```bash
cd C:\laragon\www\SIAKAD-BARU
flutter create siakad_mobile
```

Tunggu sampai selesai (butuh beberapa menit).

### Step 2: Install Dependencies

```bash
cd siakad_mobile
```

Edit file `pubspec.yaml`, tambahkan di bagian `dependencies`:

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

Lalu install:
```bash
flutter pub get
```

### Step 3: Copy Starter Files

Copy file-file dari folder `mobile_app/` ke project:

**Struktur yang perlu dibuat:**
```
siakad_mobile/
├── lib/
│   ├── main.dart (update)
│   ├── config/
│   │   └── api_config.dart (copy dari mobile_app/)
│   ├── services/
│   │   ├── api_service.dart (copy)
│   │   └── storage_service.dart (copy)
│   └── screens/
│       └── auth/
│           └── login_screen.dart (copy)
```

**Atau buat manual:**
1. Buat folder: `lib/config`, `lib/services`, `lib/screens/auth`
2. Copy file-file dari `mobile_app/lib/` ke `siakad_mobile/lib/`

### Step 4: Update API Config

Edit `lib/config/api_config.dart`, pastikan:
```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

### Step 5: Pastikan Backend Running

**Terminal 1:**
```bash
cd C:\laragon\www\SIAKAD-BARU
php artisan serve
```

Backend harus running di: `http://127.0.0.1:8000`

### Step 6: Check Available Devices

**Terminal 2 (Baru):**
```bash
cd C:\laragon\www\SIAKAD-BARU\siakad_mobile
flutter devices
```

**Output:**
```
3 connected devices:

Windows (desktop) • windows • windows-x64
Chrome (web)      • chrome  • web-javascript
Edge (web)        • edge    • web-javascript
```

### Step 7: Run Flutter App

**Opsi A: Run di Browser Chrome (Paling Mudah)**
```bash
flutter run -d chrome
```

**Opsi B: Run di Windows Desktop**
```bash
flutter run -d windows
```

**Opsi C: Run di Device Pertama**
```bash
flutter run
```

### Step 8: Test Login

1. App akan terbuka di browser/desktop
2. Masukkan:
   - Email: `noer@gmail.com`
   - Password: `zidanlangut14`
3. Klik "Masuk"
4. Verifikasi login berhasil

---

## 🎮 Hot Reload (Saat App Running)

Saat app sedang running, di terminal bisa tekan:
- `r` - Hot reload (refresh UI cepat)
- `R` - Hot restart (restart app)
- `q` - Quit (stop app)

---

## 🔧 Troubleshooting

### Error: Flutter command not found
**Solusi:** Restart terminal setelah set PATH

### Error: No devices found
**Solusi:** 
- Install Chrome untuk web
- Atau install Android Studio untuk Android

### Error: Connection refused
**Solusi:**
- Pastikan backend running: `php artisan serve`
- Check API config: `lib/config/api_config.dart`

### Error: Build failed
**Solusi:**
```bash
flutter clean
flutter pub get
flutter run
```

---

## ✅ Quick Checklist

- [ ] Project dibuat: `flutter create siakad_mobile`
- [ ] Dependencies installed: `flutter pub get`
- [ ] Starter files di-copy
- [ ] Backend running: `php artisan serve`
- [ ] Device tersedia: `flutter devices`
- [ ] App running: `flutter run -d chrome`

---

**Ikuti langkah-langkah di atas secara berurutan! 🚀**

