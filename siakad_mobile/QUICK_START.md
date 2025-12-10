# ⚡ Quick Start - Menjalankan Aplikasi

## 🚨 Masalah Umum: "Failed to fetch" saat Login

**Penyebab:** Server Laravel tidak running karena terminal digunakan untuk `flutter run`.

## ✅ Solusi Cepat

### **Cara 1: Gunakan 2 Terminal (Paling Mudah)**

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

---

### **Cara 2: Gunakan Script Otomatis**

#### Windows PowerShell:

```bash
cd siakad_mobile
.\start_servers.ps1
```

#### Windows CMD:

```bash
cd siakad_mobile
start_servers.bat
```

#### Linux/Mac:

```bash
cd siakad_mobile
chmod +x start_servers.sh
./start_servers.sh
```

---

## 📋 Langkah-Langkah Lengkap

### 1. Pastikan Backend Running

```bash
cd c:\laragon\www\SIAKAD-BARU
php artisan serve
```

**Expected:** `Server running on [http://127.0.0.1:8000]`

### 2. Test Backend (Opsional)

Buka browser: `http://127.0.0.1:8000`

-   Harus bisa diakses (tidak error)

### 3. Run Flutter App

```bash
cd siakad_mobile
flutter run
```

### 4. Login

-   Email: `mahasiswa@test.com` / `dosen@test.com` / `admin@test.com`
-   Password: `password`

---

## ⚙️ Konfigurasi API URL

**PENTING:** Sesuaikan di `lib/config/api_config.dart`

### Windows/Mac/Linux:

```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

### Android Emulator:

```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

---

## ❌ Troubleshooting

### Error: "Failed to fetch"

-   ✅ Pastikan Laravel server running di terminal terpisah
-   ✅ Cek `http://127.0.0.1:8000` bisa diakses
-   ✅ Cek API URL di `api_config.dart`

### Error: "Connection refused"

-   ✅ Laravel server belum running
-   ✅ Port 8000 sudah digunakan (ganti port)
-   ✅ Untuk Android emulator, gunakan `10.0.2.2`

---

## 📚 Dokumentasi Lengkap

-   `CARA_JALANKAN_SERVER.md` - Panduan lengkap menjalankan server
-   `CARA_LOGIN.md` - Panduan login lengkap
-   `README_LOGIN.md` - Quick guide login

---

**Setelah kedua server running, aplikasi siap digunakan!** 🎉
