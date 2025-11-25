# Panduan Setup Aplikasi Mobile SIAKAD

## Overview

Aplikasi web SIAKAD sekarang sudah memiliki API yang siap digunakan untuk aplikasi mobile. API menggunakan Laravel Sanctum untuk autentikasi berbasis token.

## Prerequisites

1. **Backend sudah running**
   ```bash
   php artisan serve
   ```
   API akan tersedia di: `http://localhost:8000/api`

2. **Database sudah di-migrate**
   ```bash
   php artisan migrate
   ```

## Konfigurasi untuk Mobile App

### 1. Base URL API

Ganti base URL sesuai environment:

**Development:**
```
http://localhost:8000/api
```

**Production:**
```
https://yourdomain.com/api
```

### 2. Autentikasi

Semua request (kecuali login) memerlukan header:
```
Authorization: Bearer {token}
```

Token didapat dari endpoint `/api/login` dan harus disimpan di mobile app (AsyncStorage, SharedPreferences, dll).

### 3. Format Response

Semua response API menggunakan format:
```json
{
    "success": true/false,
    "message": "Pesan (optional)",
    "data": { ... }
}
```

## Contoh Implementasi

### Flutter (Dart)

**1. Install dependencies:**
```yaml
dependencies:
  http: ^1.1.0
  shared_preferences: ^2.2.0
```

**2. Buat API Service:**
```dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api';
  
  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }
  
  static Future<void> saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', token);
  }
  
  static Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
      }),
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        await saveToken(data['data']['token']);
        return data['data'];
      }
    }
    throw Exception('Login failed');
  }
  
  static Future<Map<String, dynamic>> getDashboard() async {
    final token = await getToken();
    if (token == null) throw Exception('Not authenticated');
    
    final response = await http.get(
      Uri.parse('$baseUrl/dashboard'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return data['data'];
    }
    throw Exception('Failed to load dashboard');
  }
}
```

### React Native (JavaScript)

**1. Install dependencies:**
```bash
npm install axios @react-native-async-storage/async-storage
```

**2. Buat API Service:**
```javascript
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Interceptor untuk menambahkan token
api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const authService = {
  async login(email, password) {
    const response = await api.post('/login', { email, password });
    if (response.data.success) {
      await AsyncStorage.setItem('token', response.data.data.token);
      return response.data.data;
    }
    throw new Error('Login failed');
  },
  
  async logout() {
    await api.post('/logout');
    await AsyncStorage.removeItem('token');
  },
  
  async getDashboard() {
    const response = await api.get('/dashboard');
    return response.data.data;
  },
};
```

## Testing API

### Menggunakan Postman

1. **Login:**
   - Method: POST
   - URL: `http://localhost:8000/api/login`
   - Body (JSON):
     ```json
     {
       "email": "user@example.com",
       "password": "password"
     }
     ```
   - Response akan berisi `token`

2. **Get Dashboard:**
   - Method: GET
   - URL: `http://localhost:8000/api/dashboard`
   - Headers:
     ```
     Authorization: Bearer {token_dari_login}
     ```

### Menggunakan cURL

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Get Dashboard (ganti TOKEN dengan token dari login)
curl -X GET http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer TOKEN"
```

## Endpoint Utama

### Authentication
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/user` - Get current user

### Dashboard
- `GET /api/dashboard` - Get dashboard data (berbeda per role)

### Mahasiswa
- `GET /api/mahasiswa/krs` - List KRS
- `POST /api/mahasiswa/krs` - Tambah KRS
- `GET /api/mahasiswa/khs` - List semester untuk KHS
- `GET /api/mahasiswa/khs/{semester_id}` - KHS per semester
- `GET /api/mahasiswa/presensi` - List presensi

### Dosen
- `GET /api/dosen/nilai` - List nilai
- `POST /api/dosen/nilai/{jadwal_id}` - Input nilai
- `GET /api/dosen/presensi` - List presensi
- `POST /api/dosen/presensi/{jadwal_id}` - Input presensi

### Notifikasi
- `GET /api/notifikasi` - List notifikasi
- `POST /api/notifikasi/{id}/read` - Mark as read
- `GET /api/notifikasi/unread-count` - Count unread

### Profile
- `GET /api/profile` - Get profile
- `PUT /api/profile` - Update profile
- `PUT /api/profile/password` - Update password

Lihat `API_DOCUMENTATION.md` untuk dokumentasi lengkap.

## Error Handling

API mengembalikan error dengan format:
```json
{
    "success": false,
    "message": "Error message"
}
```

Status code:
- `200` - Success
- `201` - Created
- `401` - Unauthorized (token invalid/expired)
- `403` - Forbidden (tidak punya akses)
- `404` - Not Found
- `422` - Validation Error

## Tips

1. **Token Expiration**: Token tidak memiliki expiration default, tapi bisa dikonfigurasi di `config/sanctum.php`
2. **Error Handling**: Selalu cek `success` field di response
3. **Loading State**: Tampilkan loading indicator saat request API
4. **Offline Support**: Pertimbangkan untuk cache data penting
5. **Security**: Jangan hardcode token di code, selalu simpan di secure storage

## Troubleshooting

### CORS Error
Jika mendapat CORS error, pastikan:
1. CORS middleware sudah diaktifkan di `bootstrap/app.php`
2. Untuk development, bisa tambahkan di `.env`:
   ```
   SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
   ```

### 401 Unauthorized
- Pastikan token masih valid
- Cek apakah token dikirim dengan format: `Bearer {token}`
- Coba login ulang untuk mendapatkan token baru

### 403 Forbidden
- Pastikan user memiliki role yang sesuai
- Cek middleware `role:` di routes

## Support

Untuk pertanyaan atau masalah, silakan buat issue di repository atau hubungi developer.

