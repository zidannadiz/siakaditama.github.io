# 📱 SIAKAD Mobile App

Aplikasi mobile untuk Sistem Informasi Akademik (SIAKAD) menggunakan Flutter.

## 🚀 Quick Start

### ⚠️ PENTING: Jalankan Server Laravel Terlebih Dahulu!

**Masalah umum:** Error "Failed to fetch" saat login karena server Laravel tidak running.

### ✅ Solusi: Gunakan 2 Terminal

#### Terminal 1 - Laravel Server:

```bash
cd c:\laragon\www\SIAKAD-BARU
php artisan serve
```

#### Terminal 2 - Flutter App:

```bash
cd siakad_mobile
flutter run
```

### 🎯 Atau Gunakan Script Otomatis:

#### Windows PowerShell:

```bash
.\start_servers.ps1
```

#### Windows CMD:

```bash
start_servers.bat
```

#### Linux/Mac:

```bash
chmod +x start_servers.sh
./start_servers.sh
```

---

## 📋 Prerequisites

-   Flutter SDK (3.0.0+)
-   PHP 8.1+
-   Laravel 10+
-   Backend SIAKAD running

---

## 🔧 Installation

1. **Install Dependencies:**

    ```bash
    flutter pub get
    ```

2. **Konfigurasi API URL:**
   Edit `lib/config/api_config.dart`:

    ```dart
    // Windows/Mac/Linux
    static const String baseUrl = 'http://127.0.0.1:8000/api';

    // Android Emulator
    static const String baseUrl = 'http://10.0.2.2:8000/api';
    ```

3. **Run App:**
    ```bash
    flutter run
    ```

---

## 👤 Test Users

-   **Admin:** `admin@test.com` / `password`
-   **Dosen:** `dosen@test.com` / `password`
-   **Mahasiswa:** `mahasiswa@test.com` / `password`

---

## 📚 Dokumentasi

-   [QUICK_START.md](QUICK_START.md) - Quick start guide
-   [CARA_JALANKAN_SERVER.md](CARA_JALANKAN_SERVER.md) - Panduan menjalankan server
-   [CARA_LOGIN.md](CARA_LOGIN.md) - Panduan login lengkap
-   [README_LOGIN.md](README_LOGIN.md) - Quick guide login

---

## 🎯 Features

### Mahasiswa:

-   ✅ Dashboard
-   ✅ KRS Management (List, Add, Delete)
-   ✅ KHS (View nilai per semester)
-   ✅ Profile (View & Edit)

### Dosen:

-   ✅ Dashboard
-   ✅ Input Nilai (Tugas, UTS, UAS)
-   ✅ Input Presensi (Hadir, Izin, Sakit, Alpa)
-   ✅ Profile (View & Edit)

### Admin:

-   ✅ Dashboard dengan statistik
-   ✅ Profile (View & Edit)

---

## 🐛 Troubleshooting

### Error: "Failed to fetch"

-   Pastikan Laravel server running di terminal terpisah
-   Cek `http://127.0.0.1:8000` bisa diakses
-   Cek API URL di `api_config.dart`

### Error: "Connection refused"

-   Laravel server belum running
-   Untuk Android emulator, gunakan `10.0.2.2` bukan `127.0.0.1`

---

## 📝 Development

### Project Structure:

```
lib/
├── main.dart                 # Entry point
├── config/
│   └── api_config.dart      # API configuration
├── services/
│   ├── api_service.dart     # API service
│   └── storage_service.dart # Local storage
├── screens/
│   ├── auth/                # Authentication
│   ├── dashboard/           # Dashboard per role
│   ├── profile/             # Profile management
│   ├── mahasiswa/           # Mahasiswa features
│   └── dosen/               # Dosen features
└── widgets/                 # Reusable widgets
```

---

## 🔗 Links

-   [Flutter Documentation](https://docs.flutter.dev/)
-   [API Documentation](../API_DOCUMENTATION.md)

---

**Happy Coding! 🚀**
