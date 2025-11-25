# SIAKAD Mobile App

Aplikasi mobile untuk Sistem Informasi Akademik (SIAKAD) menggunakan Flutter.

## 🚀 Quick Start

### 1. Install Flutter

Pastikan Flutter sudah terinstall:
```bash
flutter doctor
```

### 2. Install Dependencies

```bash
flutter pub get
```

### 3. Run App

**Android:**
```bash
flutter run
```

**iOS (hanya di Mac):**
```bash
flutter run -d ios
```

## 📱 Features

- ✅ Authentication (Login/Logout)
- ✅ Dashboard per role (Admin, Dosen, Mahasiswa)
- ✅ KRS Management (Mahasiswa)
- ✅ KHS View (Mahasiswa)
- ✅ Input Nilai (Dosen)
- ✅ Input Presensi (Dosen)
- ✅ Notifikasi
- ✅ Profile Management

## 🔧 Configuration

Edit `lib/config/api_config.dart` untuk mengubah base URL API:

```dart
static const String baseUrl = 'http://127.0.0.1:8000/api';
```

Untuk production:
```dart
static const String baseUrl = 'https://yourdomain.com/api';
```

## 📁 Project Structure

```
lib/
├── main.dart                 # Entry point
├── config/
│   └── api_config.dart      # API configuration
├── services/
│   ├── api_service.dart     # API service
│   └── storage_service.dart # Local storage
├── screens/
│   ├── auth/
│   │   └── login_screen.dart
│   └── dashboard/
│       └── ...
└── widgets/
    └── ...
```

## 🔐 Authentication

App menggunakan token-based authentication dengan Laravel Sanctum. Token disimpan di local storage dan otomatis dikirim di setiap request.

## 📚 API Documentation

Lihat `API_DOCUMENTATION.md` di root project untuk dokumentasi lengkap API.

## 🐛 Troubleshooting

### CORS Error
Pastikan backend sudah dikonfigurasi untuk allow CORS dari mobile app.

### Connection Error
- Pastikan server backend running
- Cek base URL di `api_config.dart`
- Untuk Android emulator, gunakan `10.0.2.2` instead of `127.0.0.1`

### Token Expired
App akan otomatis logout jika token expired. User perlu login ulang.

## 📝 Next Steps

1. Implementasi dashboard screens
2. Implementasi KRS/KHS screens
3. Implementasi input nilai/presensi
4. Add error handling & loading states
5. Add offline support (optional)

