# 🚀 Cara Melihat Hasil Aplikasi Mobile

## 📋 Prasyarat

1. ✅ **Laravel Backend sudah berjalan**
2. ✅ **Flutter SDK sudah terinstall**
3. ✅ **Device/Emulator siap**

---

## 🎯 **Cara 1: Menggunakan Script Otomatis (Recommended)**

### **Windows (PowerShell)**

```powershell
cd siakad_mobile
.\start_servers.ps1
```

### **Windows (CMD)**

```cmd
cd siakad_mobile
start_servers.bat
```

### **Linux/Mac**

```bash
cd siakad_mobile
chmod +x start_servers.sh
./start_servers.sh
```

**Script ini akan:**

-   ✅ Menjalankan Laravel backend server (port 8000)
-   ✅ Menjalankan Flutter app secara otomatis

---

## 🎯 **Cara 2: Manual (Step by Step)**

### **Step 1: Jalankan Laravel Backend**

Buka terminal pertama:

```bash
cd c:\laragon\www\SIAKAD-BARU
php artisan serve
```

Tunggu sampai muncul:

```
Laravel development server started: http://127.0.0.1:8000
```

### **Step 2: Jalankan Flutter App**

Buka terminal kedua:

```bash
cd c:\laragon\www\SIAKAD-BARU\siakad_mobile
flutter run
```

**Atau jika ingin pilih device:**

```bash
flutter devices  # Lihat daftar device
flutter run -d <device-id>  # Jalankan di device tertentu
```

---

## 📱 **Cara 3: Build APK untuk Testing**

### **Build APK Debug**

```bash
cd siakad_mobile
flutter build apk --debug
```

APK akan tersimpan di:

```
siakad_mobile\build\app\outputs\flutter-apk\app-debug.apk
```

### **Install ke Device**

1. Transfer file `app-debug.apk` ke Android device
2. Install APK (aktifkan "Install from Unknown Sources" jika perlu)
3. Buka aplikasi

---

## 🔧 **Troubleshooting**

### **Error: "Failed to fetch" atau "Connection refused"**

✅ **Solusi:** Pastikan Laravel backend sudah berjalan di port 8000

### **Error: "No devices found"**

✅ **Solusi:**

-   Buka Android Studio → AVD Manager → Start emulator
-   Atau hubungkan device via USB dengan USB Debugging aktif

### **Error: "Flutter not found"**

✅ **Solusi:**

-   Install Flutter SDK
-   Tambahkan Flutter ke PATH environment variable

### **Error: "Package not found"**

✅ **Solusi:**

```bash
cd siakad_mobile
flutter pub get
```

---

## 📝 **Test User untuk Login**

### **Mahasiswa**

-   Email: `mahasiswa@example.com`
-   Password: `password`

### **Dosen**

-   Email: `dosen@example.com`
-   Password: `password`

### **Admin**

-   Email: `admin@example.com`
-   Password: `password`

---

## ✅ **Fitur yang Bisa Dilihat**

### **Fitur Umum (Semua Role)**

1. ✅ Dashboard
2. ✅ Profile (View, Edit, Change Password)
3. ✅ Notifikasi
4. ✅ Pengumuman
5. ✅ Chat
6. ✅ Payment/Pembayaran
7. ✅ Forum
8. ✅ Q&A

### **Fitur Mahasiswa**

9. ✅ KRS (List, Add, Delete)
10. ✅ KHS (View per semester)
11. ✅ Presensi (View per jadwal)

### **Fitur Dosen**

12. ✅ Input Nilai (Tugas, UTS, UAS)
13. ✅ Input Presensi (Hadir, Izin, Sakit, Alpa)

---

## 🎨 **Tips Testing**

1. **Test di Emulator:**

    - Buka Android Studio
    - AVD Manager → Create/Start emulator
    - `flutter run` akan otomatis detect emulator

2. **Test di Real Device:**

    - Aktifkan USB Debugging di Android device
    - Hubungkan via USB
    - `flutter devices` untuk cek device
    - `flutter run` untuk install & run

3. **Hot Reload:**

    - Tekan `r` di terminal untuk hot reload
    - Tekan `R` untuk hot restart
    - Tekan `q` untuk quit

4. **Debug Mode:**
    - Tekan `d` untuk open DevTools
    - Atau buka Chrome: `chrome://inspect`

---

## 📊 **Checklist Sebelum Testing**

-   [ ] Laravel backend running (http://127.0.0.1:8000)
-   [ ] Flutter dependencies installed (`flutter pub get`)
-   [ ] Device/Emulator ready
-   [ ] API base URL sudah benar di `lib/config/api_config.dart`

---

## 🚀 **Quick Start (1 Command)**

**Windows:**

```powershell
cd siakad_mobile; .\start_servers.ps1
```

**Linux/Mac:**

```bash
cd siakad_mobile && ./start_servers.sh
```

---

**Selamat Testing! 🎉**
