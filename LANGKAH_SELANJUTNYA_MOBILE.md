# 📱 Langkah Selanjutnya - Development Mobile App

## 🎯 Tujuan
Membangun aplikasi mobile SIAKAD yang terhubung dengan API backend yang sudah ada.

## 📋 Checklist Langkah-Langkah

### ✅ Phase 1: Setup & Preparation (Hari 1)

#### 1. Install Flutter (Jika Belum)
- [ ] Download Flutter SDK: https://flutter.dev/docs/get-started/install/windows
- [ ] Extract ke folder (misal: `C:\flutter`)
- [ ] Tambahkan ke PATH environment variable
- [ ] Install Android Studio (untuk Android development)
- [ ] Verifikasi: `flutter doctor`

#### 2. Create Flutter Project
```bash
flutter create siakad_mobile
cd siakad_mobile
```

#### 3. Install Dependencies
Edit `pubspec.yaml`, tambahkan:
```yaml
dependencies:
  http: ^1.1.0
  shared_preferences: ^2.2.0
  provider: ^6.0.0
  go_router: ^12.0.0
  intl: ^0.18.0
```

Lalu:
```bash
flutter pub get
```

#### 4. Copy Starter Files
Copy file-file dari folder `mobile_app/` di project ini ke project Flutter:
- [ ] `lib/config/api_config.dart`
- [ ] `lib/services/api_service.dart`
- [ ] `lib/services/storage_service.dart`
- [ ] `lib/screens/auth/login_screen.dart`
- [ ] Update `lib/main.dart`

#### 5. Test Connection
- [ ] Pastikan backend running: `php artisan serve`
- [ ] Run Flutter app: `flutter run`
- [ ] Test login dengan: `noer@gmail.com` / `zidanlangut14`
- [ ] Verifikasi token tersimpan

---

### ✅ Phase 2: Authentication & Navigation (Hari 2-3)

#### 1. Setup Navigation
- [ ] Install `go_router`: sudah di dependencies
- [ ] Buat file `lib/routes/app_router.dart`
- [ ] Setup routes: Login → Dashboard (berdasarkan role)
- [ ] Implementasi auto-logout jika token expired

#### 2. Implementasi Dashboard Screens
Buat 3 dashboard screens:
- [ ] `lib/screens/dashboard/admin_dashboard.dart`
- [ ] `lib/screens/dashboard/dosen_dashboard.dart`
- [ ] `lib/screens/dashboard/mahasiswa_dashboard.dart`

#### 3. Fetch Dashboard Data
- [ ] Panggil API: `ApiService.getDashboard()`
- [ ] Tampilkan data sesuai role
- [ ] Handle loading & error states

#### 4. Profile Screen
- [ ] Buat `lib/screens/profile/profile_screen.dart`
- [ ] Fetch user data dari API
- [ ] Implementasi update profile
- [ ] Implementasi change password

---

### ✅ Phase 3: Core Features - Mahasiswa (Hari 4-7)

#### 1. KRS Management
- [ ] Buat `lib/screens/mahasiswa/krs_list_screen.dart`
- [ ] Fetch KRS list dari API: `GET /api/mahasiswa/krs`
- [ ] Tampilkan status (pending/disetujui/ditolak)
- [ ] Buat `lib/screens/mahasiswa/krs_add_screen.dart`
- [ ] Fetch available courses
- [ ] Implementasi add KRS: `POST /api/mahasiswa/krs`
- [ ] Implementasi delete KRS: `DELETE /api/mahasiswa/krs/{id}`

#### 2. KHS View
- [ ] Buat `lib/screens/mahasiswa/khs_screen.dart`
- [ ] Fetch semester list: `GET /api/mahasiswa/khs`
- [ ] Fetch KHS per semester: `GET /api/mahasiswa/khs/{semester_id}`
- [ ] Tampilkan nilai per mata kuliah
- [ ] Tampilkan IPK dan total SKS

#### 3. Presensi View
- [ ] Buat `lib/screens/mahasiswa/presensi_screen.dart`
- [ ] Fetch presensi data: `GET /api/mahasiswa/presensi`
- [ ] Tampilkan statistik presensi
- [ ] Filter by jadwal

---

### ✅ Phase 4: Core Features - Dosen (Hari 8-10)

#### 1. Input Nilai
- [ ] Buat `lib/screens/dosen/nilai_list_screen.dart`
- [ ] Fetch list jadwal: `GET /api/dosen/nilai`
- [ ] Buat `lib/screens/dosen/nilai_input_screen.dart`
- [ ] Fetch list mahasiswa per jadwal
- [ ] Implementasi input nilai (Tugas, UTS, UAS)
- [ ] Implementasi edit nilai

#### 2. Input Presensi
- [ ] Buat `lib/screens/dosen/presensi_list_screen.dart`
- [ ] Fetch list jadwal: `GET /api/dosen/presensi`
- [ ] Buat `lib/screens/dosen/presensi_input_screen.dart`
- [ ] Implementasi input presensi
- [ ] Tampilkan statistik presensi

---

### ✅ Phase 5: Additional Features (Hari 11-12)

#### 1. Notifikasi
- [ ] Buat `lib/screens/notifikasi/notifikasi_screen.dart`
- [ ] Fetch notifikasi: `GET /api/notifikasi`
- [ ] Implementasi mark as read
- [ ] Display unread count badge

#### 2. Pengumuman
- [ ] Buat `lib/screens/pengumuman/pengumuman_screen.dart`
- [ ] Fetch pengumuman: `GET /api/pengumuman`
- [ ] Tampilkan pinned pengumuman

---

### ✅ Phase 6: Polish & Testing (Hari 13-14)

#### 1. UI/UX Improvements
- [ ] Improve loading states
- [ ] Improve error messages
- [ ] Add pull-to-refresh
- [ ] Add empty states
- [ ] Improve navigation flow

#### 2. Error Handling
- [ ] Handle network errors
- [ ] Handle API errors
- [ ] Display user-friendly messages
- [ ] Implement retry mechanism

#### 3. Testing
- [ ] Test di Android device/emulator
- [ ] Test di iOS device/simulator (jika ada Mac)
- [ ] Test dengan network slow
- [ ] Test dengan network offline
- [ ] Test edge cases

---

## 🚀 Quick Start (Lakukan Sekarang!)

### Langkah 1: Install Flutter
```bash
# Download Flutter: https://flutter.dev/docs/get-started/install/windows
# Extract dan tambahkan ke PATH
flutter doctor
```

### Langkah 2: Create Project
```bash
flutter create siakad_mobile
cd siakad_mobile
```

### Langkah 3: Install Dependencies
Edit `pubspec.yaml`, tambahkan dependencies, lalu:
```bash
flutter pub get
```

### Langkah 4: Copy Files
Copy semua file dari folder `mobile_app/` ke project Flutter:
- `lib/config/api_config.dart`
- `lib/services/api_service.dart`
- `lib/services/storage_service.dart`
- `lib/screens/auth/login_screen.dart`
- Update `lib/main.dart`

### Langkah 5: Test
```bash
# Terminal 1: Start backend
php artisan serve

# Terminal 2: Run Flutter
cd siakad_mobile
flutter run
```

### Langkah 6: Test Login
1. Buka app di emulator/device
2. Login dengan: `noer@gmail.com` / `zidanlangut14`
3. Verifikasi token tersimpan
4. Check console untuk response

---

## 📝 File yang Perlu Dibuat Selanjutnya

### Priority 1 (Hari Ini)
1. **Navigation Router** (`lib/routes/app_router.dart`)
   - Setup routing dengan go_router
   - Login → Dashboard (berdasarkan role)
   - Logout → Login

2. **Dashboard Screens**
   - `lib/screens/dashboard/admin_dashboard.dart`
   - `lib/screens/dashboard/dosen_dashboard.dart`
   - `lib/screens/dashboard/mahasiswa_dashboard.dart`

### Priority 2 (Minggu Ini)
3. **Profile Screen** (`lib/screens/profile/profile_screen.dart`)
4. **KRS Screens** (untuk Mahasiswa)
5. **KHS Screen** (untuk Mahasiswa)

---

## 🔧 Configuration

### API Base URL
Edit `lib/config/api_config.dart`:

**Development (Windows):**
```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

**Android Emulator:**
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

**iOS Simulator:**
```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

**Production:**
```dart
static const String baseUrl = 'https://yourdomain.com/api';
```

---

## 🐛 Troubleshooting

### Flutter Not Found
- Pastikan Flutter sudah di PATH
- Restart terminal/command prompt
- Run `flutter doctor` untuk cek

### Connection Error
- Pastikan backend running: `php artisan serve`
- Untuk Android emulator, gunakan `10.0.2.2:8000`
- Check firewall settings

### CORS Error
- Pastikan CORS sudah dikonfigurasi di backend
- Check `bootstrap/app.php` untuk HandleCors middleware

### Token Expired
- App akan otomatis logout
- User perlu login ulang

---

## 📚 Resources

- **Flutter Docs**: https://flutter.dev/docs
- **API Documentation**: `API_DOCUMENTATION.md`
- **Mobile Setup**: `MOBILE_APP_SETUP.md`
- **Quick Start**: `MOBILE_APP_QUICK_START.md`

---

## ✅ Checklist Harian

### Hari 1: Setup
- [ ] Flutter installed
- [ ] Project created
- [ ] Dependencies installed
- [ ] Starter files copied
- [ ] Login tested

### Hari 2: Navigation & Dashboard
- [ ] Router setup
- [ ] Dashboard screens created
- [ ] Navigation working
- [ ] Dashboard data displayed

### Hari 3: Profile
- [ ] Profile screen created
- [ ] Update profile working
- [ ] Change password working

---

**Mulai dari Phase 1, langkah demi langkah! 🚀**

