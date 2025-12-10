# 🔐 Cara Login - Quick Guide

## 📱 Cara Login

1. **Buka aplikasi** - Aplikasi akan menampilkan halaman login
2. **Masukkan email dan password**
3. **Klik tombol "Masuk"**
4. **Akan redirect ke dashboard** sesuai role Anda

---

## 👤 Test Users

### Admin

-   Email: `admin@test.com`
-   Password: `password`

### Dosen

-   Email: `dosen@test.com`
-   Password: `password`

### Mahasiswa

-   Email: `mahasiswa@test.com`
-   Password: `password`

---

## ⚙️ Konfigurasi API URL

**PENTING:** Sesuaikan API URL berdasarkan platform:

### Windows/Linux/Mac (Development)

File: `lib/config/api_config.dart`

```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

### Android Emulator

File: `lib/config/api_config.dart`

```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

### iOS Simulator

File: `lib/config/api_config.dart`

```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

---

## 🚀 Langkah-Langkah

### 1. Pastikan Backend Running

```bash
cd c:\laragon\www\SIAKAD-BARU
php artisan serve
```

### 2. Run Flutter App

```bash
cd siakad_mobile
flutter run
```

### 3. Login

-   Masukkan email dan password
-   Klik "Masuk"
-   Akan redirect ke dashboard

---

## ✅ Auto-Login

Setelah login pertama kali:

-   Token akan tersimpan otomatis
-   Saat buka aplikasi lagi, akan auto-login
-   Tidak perlu login ulang setiap kali

---

## ❌ Troubleshooting

### Error: "Connection refused"

-   Pastikan backend running: `php artisan serve`
-   Cek API URL di `api_config.dart`
-   Untuk Android emulator, gunakan `10.0.2.2`

### Error: "Email atau password salah"

-   Pastikan email dan password benar
-   Buat test user: `php create_test_users.php`

### Auto-login tidak bekerja

-   Logout dan login ulang
-   Hapus data aplikasi

---

## 📝 Membuat Test Users

```bash
php create_test_users.php
```

Atau manual di tinker:

```bash
php artisan tinker
```

Lihat file `CARA_LOGIN.md` untuk detail lengkap.
