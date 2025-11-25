# 🚀 Quick Start - Mobile App Development

## ✅ Yang Sudah Dibuat

1. ✅ **API Backend** - Lengkap dengan 68 endpoints
2. ✅ **Starter Code Flutter** - File-file dasar sudah dibuat
3. ✅ **API Service** - Class untuk komunikasi dengan API
4. ✅ **Login Screen** - UI dasar untuk login

## 📋 Langkah Selanjutnya

### 1. Setup Flutter Project

```bash
# Install Flutter (jika belum)
# Download dari: https://flutter.dev/docs/get-started/install

# Create project
flutter create siakad_mobile
cd siakad_mobile

# Copy file-file dari folder mobile_app/ ke project
# Atau buat manual sesuai struktur di mobile_app/
```

### 2. Install Dependencies

Edit `pubspec.yaml` dan tambahkan:
```yaml
dependencies:
  http: ^1.1.0
  shared_preferences: ^2.2.0
  provider: ^6.0.0
  go_router: ^12.0.0
```

Lalu:
```bash
flutter pub get
```

### 3. Copy Files

Copy file-file dari folder `mobile_app/` ke project Flutter Anda:
- `lib/config/api_config.dart`
- `lib/services/api_service.dart`
- `lib/services/storage_service.dart`
- `lib/screens/auth/login_screen.dart`
- Update `lib/main.dart`

### 4. Test Login

```bash
# Pastikan backend running
php artisan serve

# Run Flutter app
flutter run
```

### 5. Implementasi Dashboard

Buat dashboard screens untuk setiap role:
- `lib/screens/dashboard/admin_dashboard.dart`
- `lib/screens/dashboard/dosen_dashboard.dart`
- `lib/screens/dashboard/mahasiswa_dashboard.dart`

### 6. Implementasi Navigation

Setup routing dengan `go_router`:
- Login → Dashboard (berdasarkan role)
- Dashboard → Feature screens
- Logout → Login

## 📱 Features yang Perlu Diimplementasi

### Priority 1 (Minggu 1-2)
- [ ] Dashboard screens (Admin, Dosen, Mahasiswa)
- [ ] Navigation system
- [ ] Profile screen
- [ ] Logout functionality

### Priority 2 (Minggu 3-4)
- [ ] KRS Management (Mahasiswa)
- [ ] KHS View (Mahasiswa)
- [ ] Input Nilai (Dosen)
- [ ] Input Presensi (Dosen)

### Priority 3 (Minggu 5-6)
- [ ] Notifikasi screen
- [ ] Pengumuman screen
- [ ] Error handling improvements
- [ ] Loading states
- [ ] Pull to refresh

## 🔧 Configuration

### API Base URL

Edit `lib/config/api_config.dart`:
```dart
// Development
static const String baseUrl = 'http://127.0.0.1:8000/api';

// Production
// static const String baseUrl = 'https://yourdomain.com/api';
```

**Note untuk Android Emulator:**
Gunakan `10.0.2.2` instead of `127.0.0.1`:
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

## 📚 Resources

- **Flutter Docs**: https://flutter.dev/docs
- **API Documentation**: `API_DOCUMENTATION.md`
- **Mobile Setup Guide**: `MOBILE_APP_SETUP.md`

## 🎯 Quick Test

1. **Test API dari Flutter:**
   ```dart
   // Di login_screen.dart, test dengan:
   final result = await ApiService.login('noer@gmail.com', 'zidanlangut14');
   print(result);
   ```

2. **Test Dashboard:**
   ```dart
   final dashboard = await ApiService.getDashboard();
   print(dashboard);
   ```

## 🐛 Common Issues

### CORS Error
- Pastikan CORS sudah dikonfigurasi di backend
- Check `bootstrap/app.php` untuk HandleCors middleware

### Connection Refused
- Pastikan server running: `php artisan serve`
- Untuk Android emulator, gunakan `10.0.2.2:8000`
- Untuk iOS simulator, gunakan `127.0.0.1:8000`

### Token Expired
- App akan otomatis logout
- User perlu login ulang

## 📝 Next Steps

1. ✅ Setup Flutter project
2. ✅ Copy starter files
3. ✅ Test login
4. ⏭️ Implementasi dashboard
5. ⏭️ Implementasi navigation
6. ⏭️ Implementasi core features

---

**Selamat! Starter code sudah siap. Mulai develop sekarang! 🚀**

