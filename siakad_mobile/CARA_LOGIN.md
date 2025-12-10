# 🔐 Cara Login - SIAKAD Mobile App

## 📱 Cara Login di Aplikasi Mobile

### 1. Buka Aplikasi

-   Jalankan aplikasi Flutter: `flutter run`
-   Aplikasi akan otomatis menampilkan halaman login

### 2. Masukkan Kredensial

Gunakan email dan password yang sudah terdaftar di sistem.

#### Test Users (Default):

-   **Admin:**

    -   Email: `admin@test.com`
    -   Password: `password`

-   **Dosen:**

    -   Email: `dosen@test.com`
    -   Password: `password`

-   **Mahasiswa:**
    -   Email: `mahasiswa@test.com`
    -   Password: `password`

### 3. Klik Tombol "Masuk"

-   Aplikasi akan memvalidasi email dan password
-   Jika berhasil, akan redirect ke dashboard sesuai role

### 4. Auto-Login

-   Setelah login pertama kali, token akan tersimpan
-   Saat membuka aplikasi lagi, akan otomatis login jika token masih valid
-   Tidak perlu login ulang setiap kali buka aplikasi

---

## ⚙️ Konfigurasi API URL

### Untuk Development (Windows/Linux/Mac):

File: `lib/config/api_config.dart`

```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

### Untuk Android Emulator:

File: `lib/config/api_config.dart`

```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

### Untuk iOS Simulator:

File: `lib/config/api_config.dart`

```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

### Untuk Production:

File: `lib/config/api_config.dart`

```dart
static const String baseUrl = 'https://yourdomain.com/api';
```

---

## 🔧 Troubleshooting Login

### 1. Error: "Connection refused" atau "Failed to connect"

**Solusi:**

-   Pastikan backend Laravel sudah running: `php artisan serve`
-   Cek API URL di `lib/config/api_config.dart`
-   Untuk Android emulator, gunakan `10.0.2.2` bukan `127.0.0.1`

### 2. Error: "Email atau password salah"

**Solusi:**

-   Pastikan email dan password benar
-   Cek apakah user sudah terdaftar di database
-   Buat test user dengan script: `php create_test_users.php`

### 3. Error: "CORS Error"

**Solusi:**

-   Pastikan CORS sudah dikonfigurasi di backend Laravel
-   Cek file `bootstrap/app.php` untuk middleware CORS

### 4. Auto-login tidak bekerja

**Solusi:**

-   Hapus data aplikasi dan login ulang
-   Cek apakah token tersimpan di SharedPreferences
-   Logout dan login ulang

### 5. Redirect ke login terus menerus

**Solusi:**

-   Token mungkin expired
-   Logout dan login ulang
-   Hapus cache aplikasi

---

## 📝 Membuat Test Users

### Cara 1: Menggunakan Script

```bash
php create_test_users.php
```

### Cara 2: Menggunakan Tinker

```bash
php artisan tinker
```

Kemudian jalankan:

```php
// Buat Admin
$admin = \App\Models\User::create([
    'name' => 'Admin Test',
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);

// Buat Dosen
$dosenUser = \App\Models\User::create([
    'name' => 'Dosen Test',
    'email' => 'dosen@test.com',
    'password' => bcrypt('password'),
    'role' => 'dosen'
]);

$dosen = \App\Models\Dosen::create([
    'user_id' => $dosenUser->id,
    'nidn' => '1234567890',
    'nama' => 'Dosen Test',
    'email' => 'dosen@test.com',
    'status' => 'aktif'
]);

// Buat Mahasiswa
$prodi = \App\Models\Prodi::firstOrCreate([
    'kode_prodi' => 'TI',
    'nama_prodi' => 'Teknik Informatika'
]);

$mahasiswaUser = \App\Models\User::create([
    'name' => 'Mahasiswa Test',
    'email' => 'mahasiswa@test.com',
    'password' => bcrypt('password'),
    'role' => 'mahasiswa'
]);

$mahasiswa = \App\Models\Mahasiswa::create([
    'user_id' => $mahasiswaUser->id,
    'nim' => '1234567890',
    'nama' => 'Mahasiswa Test',
    'prodi_id' => $prodi->id,
    'status' => 'aktif'
]);
```

---

## 🚀 Langkah-Langkah Testing Login

### 1. Pastikan Backend Running

```bash
cd c:\laragon\www\SIAKAD-BARU
php artisan serve
```

### 2. Test Login dengan cURL (Opsional)

```bash
curl -X POST http://127.0.0.1:8000/api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@test.com\",\"password\":\"password\"}"
```

### 3. Run Flutter App

```bash
cd siakad_mobile
flutter run
```

### 4. Test Login di App

-   Masukkan email: `admin@test.com`
-   Masukkan password: `password`
-   Klik "Masuk"
-   Harus redirect ke dashboard admin

---

## ✅ Checklist Login

-   [ ] Backend Laravel running (`php artisan serve`)
-   [ ] Test users sudah dibuat
-   [ ] API URL sudah dikonfigurasi dengan benar
-   [ ] CORS sudah dikonfigurasi di backend
-   [ ] Flutter app bisa connect ke API
-   [ ] Login berhasil dan redirect ke dashboard
-   [ ] Token tersimpan dan auto-login bekerja

---

## 📞 Support

Jika masih ada masalah:

1. Cek console/log untuk error message
2. Pastikan semua dependencies sudah terinstall
3. Cek koneksi internet/network
4. Restart backend dan Flutter app
