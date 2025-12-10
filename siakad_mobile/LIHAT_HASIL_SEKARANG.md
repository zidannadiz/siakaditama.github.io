# 🎯 Cara Melihat Hasil Aplikasi - Langsung Bisa!

## 🚀 **Cara Paling Mudah (Recommended)**

### **Step 1: Buka 2 Terminal**

#### **Terminal 1 - Jalankan Laravel:**

```powershell
cd c:\laragon\www\SIAKAD-BARU
php artisan serve
```

**Tunggu sampai muncul:**

```
✓ Server running on [http://127.0.0.1:8000]
```

#### **Terminal 2 - Jalankan Flutter:**

```powershell
cd c:\laragon\www\SIAKAD-BARU\siakad_mobile
flutter run
```

**Flutter akan:**

-   Build aplikasi
-   Install ke device/emulator
-   Menjalankan aplikasi otomatis

---

## 📱 **Pilih Device**

Saat `flutter run`, akan muncul pilihan device:

```
Multiple devices found:
1. Chrome (chrome)
2. Windows (windows)
3. Edge (edge)
4. Android SDK built for x86 (emulator-5554)
5. SM A505F (device-id)

Please choose one (1-5):
```

**Pilih nomor device yang ingin digunakan.**

---

## 🔑 **Login untuk Testing**

Setelah aplikasi terbuka, login dengan:

### **Sebagai Mahasiswa:**

-   Email: `mahasiswa@example.com`
-   Password: `password`

### **Sebagai Dosen:**

-   Email: `dosen@example.com`
-   Password: `password`

### **Sebagai Admin:**

-   Email: `admin@example.com`
-   Password: `password`

---

## ✅ **Fitur yang Bisa Dilihat**

### **Dashboard:**

-   ✅ Statistik dan info user
-   ✅ Menu navigasi ke semua fitur

### **Fitur Umum:**

1. ✅ **Profile** - Edit profil & ganti password
2. ✅ **Notifikasi** - List notifikasi dengan badge count
3. ✅ **Pengumuman** - List pengumuman dengan filter & search
4. ✅ **Chat** - Chat real-time dengan user lain
5. ✅ **Payment** - Buat pembayaran & tracking status
6. ✅ **Forum** - Diskusi forum dengan topik & balasan
7. ✅ **Q&A** - Tanya jawab dengan best answer

### **Fitur Mahasiswa:**

8. ✅ **KRS** - List & tambah mata kuliah
9. ✅ **KHS** - Lihat nilai per semester
10. ✅ **Presensi** - Lihat presensi per jadwal

### **Fitur Dosen:**

11. ✅ **Input Nilai** - Input nilai Tugas, UTS, UAS
12. ✅ **Input Presensi** - Input presensi mahasiswa

---

## ⚙️ **Konfigurasi API (Jika Error Connection)**

Edit file: `siakad_mobile/lib/config/api_config.dart`

### **Untuk Android Emulator:**

```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

### **Untuk Real Device (WiFi):**

1. Cek IP komputer: `ipconfig` (Windows) atau `ifconfig` (Linux/Mac)
2. Update API URL:

```dart
static const String baseUrl = 'http://192.168.1.100:8000/api';  // Ganti dengan IP Anda
```

### **Untuk Real Device (USB):**

```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

---

## 🐛 **Troubleshooting**

### **❌ Error: "Failed to fetch"**

**Solusi:**

1. Pastikan Laravel backend running di Terminal 1
2. Test di browser: `http://127.0.0.1:8000` harus bisa diakses
3. Cek API URL di `api_config.dart`

### **❌ Error: "No devices found"**

**Solusi:**

1. Buka Android Studio
2. Tools → Device Manager
3. Create/Start emulator
4. Atau hubungkan Android device via USB dengan USB Debugging aktif

### **❌ Error: "Connection refused"**

**Solusi:**

-   Untuk Android Emulator: gunakan `10.0.2.2` bukan `127.0.0.1`
-   Untuk Real Device: gunakan IP komputer (bukan localhost)

### **❌ Error: "Package not found"**

**Solusi:**

```bash
cd siakad_mobile
flutter pub get
```

---

## 🎨 **Tips Testing**

1. **Hot Reload:** Tekan `r` di terminal Flutter untuk reload cepat
2. **Hot Restart:** Tekan `R` untuk restart aplikasi
3. **Stop App:** Tekan `q` untuk quit

---

## 📋 **Checklist Sebelum Run**

-   [ ] Laravel backend running (`php artisan serve`)
-   [ ] Flutter dependencies installed (`flutter pub get`)
-   [ ] Device/Emulator ready
-   [ ] API URL sudah benar di `api_config.dart`

---

## 🚀 **Quick Start (Copy-Paste)**

### **Terminal 1:**

```powershell
cd c:\laragon\www\SIAKAD-BARU
php artisan serve
```

### **Terminal 2:**

```powershell
cd c:\laragon\www\SIAKAD-BARU\siakad_mobile
flutter run
```

---

**Setelah aplikasi terbuka, login dan explore semua fitur! 🎉**
