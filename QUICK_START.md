# Quick Start Guide - API SIAKAD

## 🚀 5 Menit Setup

### 1. Pastikan Server Running
```bash
php artisan serve
```
API akan tersedia di: `http://localhost:8000/api`

### 2. Test Login (Gunakan Postman atau cURL)

**Request:**
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com",
      "role": "admin"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

### 3. Simpan Token
Copy token dari response, gunakan untuk request selanjutnya.

### 4. Test Dashboard

**Request:**
```bash
GET http://localhost:8000/api/dashboard
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response akan berbeda sesuai role:**
- Admin: Statistics (total mahasiswa, dosen, dll)
- Dosen: Jadwal mengajar, jadwal hari ini
- Mahasiswa: KRS semester ini, jadwal hari ini, pengumuman

## 📱 Untuk Mobile Developer

### Flutter - Quick Start

1. **Install dependencies:**
```yaml
# pubspec.yaml
dependencies:
  http: ^1.1.0
  shared_preferences: ^2.2.0
```

2. **Buat API Service:**
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
  
  static Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success']) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', data['data']['token']);
        return data['data'];
      }
    }
    throw Exception('Login failed');
  }
  
  static Future<Map<String, dynamic>> getDashboard() async {
    final token = await getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/dashboard'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );
    
    if (response.statusCode == 200) {
      return jsonDecode(response.body)['data'];
    }
    throw Exception('Failed to load dashboard');
  }
}
```

3. **Gunakan di UI:**
```dart
// Login
final userData = await ApiService.login('user@example.com', 'password');

// Get Dashboard
final dashboard = await ApiService.getDashboard();
```

### React Native - Quick Start

1. **Install dependencies:**
```bash
npm install axios @react-native-async-storage/async-storage
```

2. **Buat API Service:**
```javascript
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {'Content-Type': 'application/json'},
});

api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const login = async (email, password) => {
  const response = await api.post('/login', {email, password});
  if (response.data.success) {
    await AsyncStorage.setItem('token', response.data.data.token);
    return response.data.data;
  }
  throw new Error('Login failed');
};

export const getDashboard = async () => {
  const response = await api.get('/dashboard');
  return response.data.data;
};
```

3. **Gunakan di Component:**
```javascript
import {login, getDashboard} from './services/api';

// Login
const userData = await login('user@example.com', 'password');

// Get Dashboard
const dashboard = await getDashboard();
```

## 🧪 Test dengan cURL

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Get Dashboard
```bash
curl -X GET http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Get KRS (Mahasiswa)
```bash
curl -X GET http://localhost:8000/api/mahasiswa/krs \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

## 📚 Endpoint Penting

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/api/login` | POST | Login, dapatkan token |
| `/api/logout` | POST | Logout |
| `/api/user` | GET | Get current user |
| `/api/dashboard` | GET | Dashboard data |
| `/api/mahasiswa/krs` | GET | List KRS |
| `/api/mahasiswa/krs` | POST | Tambah KRS |
| `/api/mahasiswa/khs` | GET | List semester |
| `/api/mahasiswa/khs/{id}` | GET | KHS per semester |
| `/api/dosen/nilai` | GET | List nilai |
| `/api/dosen/nilai/{id}` | POST | Input nilai |
| `/api/notifikasi` | GET | List notifikasi |
| `/api/profile` | GET | Get profile |
| `/api/profile` | PUT | Update profile |

## ⚠️ Troubleshooting

### CORS Error
Pastikan CORS middleware aktif di `bootstrap/app.php`

### 401 Unauthorized
- Pastikan token valid
- Format header: `Authorization: Bearer {token}`
- Coba login ulang

### 404 Not Found
- Pastikan server running: `php artisan serve`
- Cek URL: `http://localhost:8000/api/...`

### 500 Internal Server Error
- Cek `storage/logs/laravel.log`
- Pastikan database sudah migrate
- Cek `.env` configuration

## 📖 Dokumentasi Lengkap

- **API Documentation**: `API_DOCUMENTATION.md`
- **Mobile Setup**: `MOBILE_APP_SETUP.md`
- **Testing Guide**: `API_TESTING.md`
- **Next Steps**: `NEXT_STEPS.md`

## 🎯 Next Steps

1. ✅ Test API dengan Postman
2. ✅ Setup mobile project
3. ✅ Implementasi authentication
4. ✅ Develop core features
5. ✅ Deploy ke production

**Selamat coding! 🚀**

