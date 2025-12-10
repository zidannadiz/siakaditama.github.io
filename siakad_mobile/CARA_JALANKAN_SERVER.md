# 🚀 Cara Menjalankan Server Laravel dan Flutter Bersamaan

## ❌ Masalah

Tidak bisa login karena server Laravel tidak dijalankan, karena terminal digunakan untuk menjalankan `flutter run`.

## ✅ Solusi

### **Opsi 1: Gunakan Terminal Terpisah (Paling Mudah)**

#### Windows:

1. **Terminal 1** - Jalankan Laravel:

    ```bash
    cd c:\laragon\www\SIAKAD-BARU
    php artisan serve
    ```

2. **Terminal 2** - Jalankan Flutter:
    ```bash
    cd siakad_mobile
    flutter run
    ```

#### Linux/Mac:

1. **Terminal 1** - Jalankan Laravel:

    ```bash
    cd /path/to/SIAKAD-BARU
    php artisan serve
    ```

2. **Terminal 2** - Jalankan Flutter:
    ```bash
    cd siakad_mobile
    flutter run
    ```

---

### **Opsi 2: Gunakan Script Otomatis (Recommended)**

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

Script ini akan:

-   ✅ Membuka terminal baru untuk Laravel server
-   ✅ Menjalankan Flutter di terminal saat ini
-   ✅ Otomatis start kedua server

---

### **Opsi 3: Jalankan Laravel di Background (Linux/Mac)**

```bash
# Jalankan Laravel di background
cd c:\laragon\www\SIAKAD-BARU
php artisan serve &

# Jalankan Flutter
cd siakad_mobile
flutter run
```

Untuk stop Laravel:

```bash
# Cari PID Laravel
ps aux | grep "php artisan serve"

# Kill process
kill <PID>
```

---

### **Opsi 4: Gunakan VS Code Terminal Terpisah**

1. Buka VS Code
2. Tekan `` Ctrl+` `` untuk buka terminal
3. Klik tombol **"+"** untuk buka terminal baru
4. **Terminal 1**: Jalankan `php artisan serve`
5. **Terminal 2**: Jalankan `flutter run`

---

## 📋 Checklist

-   [ ] Laravel server running di terminal 1 (`php artisan serve`)
-   [ ] Flutter app running di terminal 2 (`flutter run`)
-   [ ] Backend accessible di `http://127.0.0.1:8000`
-   [ ] Login berhasil tanpa error "Failed to fetch"

---

## 🔧 Troubleshooting

### Error: "Failed to fetch" atau "Connection refused"

**Solusi:**

1. Pastikan Laravel server sudah running
2. Cek di browser: `http://127.0.0.1:8000` harus bisa diakses
3. Cek API URL di `lib/config/api_config.dart`
4. Untuk Android emulator, gunakan `10.0.2.2` bukan `127.0.0.1`

### Laravel server tidak start

**Solusi:**

```bash
# Cek apakah port 8000 sudah digunakan
netstat -ano | findstr :8000  # Windows
lsof -i :8000                 # Linux/Mac

# Jika port digunakan, gunakan port lain
php artisan serve --port=8001
```

Lalu update API URL di `lib/config/api_config.dart`:

```dart
static const String baseUrl = 'http://127.0.0.1:8001/api';
```

### Script tidak bekerja

**Solusi:**

1. Pastikan path ke Laravel project benar di script
2. Update path di script sesuai lokasi project Anda
3. Pastikan PHP dan Flutter sudah di PATH

---

## 💡 Tips

1. **Gunakan VS Code dengan multiple terminals** - Lebih mudah untuk monitor kedua server
2. **Bookmark terminal commands** - Simpan command untuk quick access
3. **Gunakan script** - Otomatis start kedua server dengan satu command
4. **Check server status** - Pastikan Laravel running sebelum test login

---

## 🎯 Quick Start (Recommended)

### Windows:

```bash
# Terminal 1
cd c:\laragon\www\SIAKAD-BARU
php artisan serve

# Terminal 2 (buka terminal baru)
cd siakad_mobile
flutter run
```

### Atau gunakan script:

```bash
cd siakad_mobile
.\start_servers.ps1
```

---

**Setelah kedua server running, login akan berhasil!** ✅
