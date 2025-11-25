# 🚀 Mobile App Starter - SIAKAD

## Platform: Flutter (Dart)

Flutter dipilih karena:
- ✅ Cross-platform (iOS + Android dengan 1 codebase)
- ✅ Performance bagus
- ✅ UI modern dan mudah dikustomisasi
- ✅ Banyak library yang tersedia

## 📋 Langkah Setup

### 1. Install Flutter

**Windows:**
1. Download Flutter SDK: https://flutter.dev/docs/get-started/install/windows
2. Extract ke folder (misal: `C:\flutter`)
3. Tambahkan ke PATH environment variable
4. Install Android Studio untuk Android development
5. Install Xcode (hanya untuk iOS, perlu Mac)

**Verifikasi:**
```bash
flutter doctor
```

### 2. Create Flutter Project

```bash
flutter create siakad_mobile
cd siakad_mobile
```

### 3. Install Dependencies

Edit `pubspec.yaml`:
```yaml
dependencies:
  flutter:
    sdk: flutter
  
  # HTTP Client
  http: ^1.1.0
  
  # Local Storage
  shared_preferences: ^2.2.0
  
  # State Management
  provider: ^6.0.0
  
  # Navigation
  go_router: ^12.0.0
  
  # UI Components
  flutter_svg: ^2.0.0
  cached_network_image: ^3.3.0
  
  # Utils
  intl: ^0.18.0  # Date formatting
```

Install:
```bash
flutter pub get
```

### 4. Project Structure

```
lib/
├── main.dart
├── config/
│   └── api_config.dart
├── services/
│   ├── api_service.dart
│   ├── auth_service.dart
│   └── storage_service.dart
├── models/
│   ├── user.dart
│   ├── krs.dart
│   └── ...
├── providers/
│   └── auth_provider.dart
├── screens/
│   ├── auth/
│   │   └── login_screen.dart
│   ├── dashboard/
│   │   ├── admin_dashboard.dart
│   │   ├── dosen_dashboard.dart
│   │   └── mahasiswa_dashboard.dart
│   └── ...
└── widgets/
    └── ...
```

## 🔧 Implementation Files

File-file berikut akan dibuat di folder terpisah untuk referensi implementasi.

