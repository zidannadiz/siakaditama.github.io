# 🚀 Cara Melihat Hasil Aplikasi - Quick Guide

## ⚡ **Cara Tercepat (1 Command)**

### **Windows PowerShell:**

```powershell
cd siakad_mobile
.\start_servers.ps1
```

### **Windows CMD:**

```cmd
cd siakad_mobile
start_servers.bat
```

### **Linux/Mac:**

```bash
cd siakad_mobile
chmod +x start_servers.sh && ./start_servers.sh
```

**Script ini akan otomatis:**

-   ✅ Menjalankan Laravel backend (port 8000)
-   ✅ Menjalankan Flutter app
-   ✅ Membuka aplikasi di device/emulator

---

## 📱 **Cara Manual (2 Terminal)**

### **Terminal 1 - Laravel Backend:**

```bash
cd c:\laragon\www\SIAKAD-BARU
php artisan serve
```

**Tunggu sampai muncul:**

```
Laravel development server started: http://127.0.0.1:8000
```

### **Terminal 2 - Flutter App:**

```bash
cd siakad_mobile
flutter run
```

**Flutter akan:**

-   ✅ Build aplikasi
-   ✅ Install ke device/emulator
-   ✅ Menjalankan aplikasi

---

## 🔑 **Login untuk Testing**

### **Mahasiswa:**

-   Email: `mahasiswa@example.com`
-   Password: `password`

### **Dosen:**

-   Email: `dosen@example.com`
-   Password: `password`

### **Admin:**

-   Email: `admin@example.com`
-   Password: `password`

---

## ✅ **Fitur yang Bisa Dilihat**

### **Semua Role:**

1. ✅ Dashboard
2. ✅ Profile
3. ✅ Notifikasi
4. ✅ Pengumuman
5. ✅ Chat
6. ✅ Payment
7. ✅ Forum
8. ✅ Q&A

### **Mahasiswa:**

9. ✅ KRS
10. ✅ KHS
11. ✅ Presensi

### **Dosen:**

12. ✅ Input Nilai
13. ✅ Input Presensi

---

## 📱 **Device Options**

### **Option 1: Android Emulator**

1. Buka Android Studio
2. Tools → Device Manager
3. Create/Start emulator
4. `flutter run` akan otomatis detect

### **Option 2: Real Android Device**

1. Aktifkan USB Debugging
2. Hubungkan via USB
3. `flutter run` akan install & run

### **Option 3: Build APK**

```bash
cd siakad_mobile
flutter build apk --debug
```

APK ada di: `build/app/outputs/flutter-apk/app-debug.apk`

---

## ⚙️ **Konfigurasi API (PENTING!)**

Cek file: `siakad_mobile/lib/config/api_config.dart`

### **Untuk Android Emulator:**

```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

### **Untuk Real Device (WiFi):**

```dart
static const String baseUrl = 'http://192.168.x.x:8000/api';  // IP komputer Anda
```

### **Untuk Real Device (USB):**

```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

**Cara cek IP komputer:**

-   Windows: `ipconfig` → cari IPv4 Address
-   Linux/Mac: `ifconfig` atau `ip addr`

---

## 🐛 **Troubleshooting**

### **Error: "Failed to fetch"**

✅ Pastikan Laravel backend running di terminal terpisah

### **Error: "No devices found"**

✅ Buka Android Studio → AVD Manager → Start emulator

### **Error: "Connection refused"**

✅ Cek API URL di `api_config.dart` sesuai device type

### **Error: "Package not found"**

```bash
cd siakad_mobile
flutter pub get
```

---

## 🎯 **Quick Test Checklist**

-   [ ] Laravel running (`php artisan serve`)
-   [ ] Flutter dependencies installed (`flutter pub get`)
-   [ ] Device/Emulator ready
-   [ ] API URL sudah benar
-   [ ] Login dengan test user

---

## 💡 **Tips**

1. **Hot Reload:** Tekan `r` di terminal Flutter untuk reload cepat
2. **Hot Restart:** Tekan `R` untuk restart aplikasi
3. **DevTools:** Tekan `d` untuk open debugging tools
4. **Quit:** Tekan `q` untuk stop aplikasi

---

**Selamat Testing! 🎉**
