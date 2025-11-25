# 🚀 Cara Menjalankan Flutter App

## ✅ Setup Selesai!

Project Flutter sudah dibuat dan siap dijalankan.

---

## 📋 Langkah-Langkah Menjalankan

### 1. Pastikan Backend Running

**Terminal 1:**
```bash
cd C:\laragon\www\SIAKAD-BARU
php artisan serve
```

Backend harus running di: `http://127.0.0.1:8000`

### 2. Jalankan Flutter App

**Opsi A: Menggunakan Script (Paling Mudah)**

Buka PowerShell di folder project, lalu:
```powershell
.\run_flutter_app.ps1
```

**Opsi B: Manual**

**Terminal 2 (Baru):**
```powershell
cd C:\laragon\www\SIAKAD-BARU\siakad_mobile

# Set PATH untuk Flutter
$env:PATH = "C:\laragon\www\SIAKAD-BARU\flutter\bin;$env:PATH"

# Check devices
flutter devices

# Run di Chrome
flutter run -d chrome
```

**Opsi C: Run di Windows Desktop**
```powershell
flutter run -d windows
```

---

## 🎮 Hot Reload (Saat App Running)

Saat app sedang running, di terminal bisa tekan:
- `r` - Hot reload (refresh UI cepat)
- `R` - Hot restart (restart app)
- `q` - Quit (stop app)

---

## 🧪 Test Login

1. App akan terbuka di browser/desktop
2. Masukkan:
   - **Email:** `noer@gmail.com`
   - **Password:** `zidanlangut14`
3. Klik "Masuk"
4. Verifikasi login berhasil (akan muncul snackbar)

---

## 🔧 Troubleshooting

### Error: Flutter command not found
**Solusi:** 
- Gunakan script `run_flutter_app.ps1`
- Atau set PATH manual: `$env:PATH = "C:\laragon\www\SIAKAD-BARU\flutter\bin;$env:PATH"`

### Error: No devices found
**Solusi:** 
- Install Chrome browser untuk web
- Atau install Android Studio untuk Android

### Error: Connection refused
**Solusi:**
- Pastikan backend running: `php artisan serve`
- Check API config: `siakad_mobile/lib/config/api_config.dart`

### Error: Building with plugins requires symlink support
**Solusi:**
- Ini hanya warning, tidak menghalangi
- Untuk enable symlink: `start ms-settings:developers` (enable Developer Mode)

---

## 📁 Struktur Project

```
siakad_mobile/
├── lib/
│   ├── main.dart
│   ├── config/
│   │   └── api_config.dart
│   ├── services/
│   │   ├── api_service.dart
│   │   └── storage_service.dart
│   └── screens/
│       └── auth/
│           └── login_screen.dart
├── pubspec.yaml
└── run_flutter_app.ps1
```

---

## ✅ Quick Commands

```powershell
# Set PATH (jika belum)
$env:PATH = "C:\laragon\www\SIAKAD-BARU\flutter\bin;$env:PATH"

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
```

---

## 🎯 Next Steps

Setelah app running dan login berhasil:
1. Implementasi dashboard untuk setiap role
2. Setup navigation dengan go_router
3. Implementasi fitur sesuai role (Admin, Dosen, Mahasiswa)

---

**Selamat! App sudah siap dijalankan! 🚀**

