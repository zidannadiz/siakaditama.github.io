# Dokumentasi API SIAKAD Mobile

## Base URL
```
http://localhost:8000/api
```

## Autentikasi

API menggunakan Laravel Sanctum untuk autentikasi berbasis token.

### Login
**POST** `/api/login`

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "mahasiswa"
        },
        "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    }
}
```

### Logout
**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Logout berhasil"
}
```

### Get Current User
**GET** `/api/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "mahasiswa",
        "mahasiswa": {
            "id": 1,
            "nim": "1234567890",
            "nama": "John Doe",
            "prodi": "Teknik Informatika"
        }
    }
}
```

## Dashboard

### Get Dashboard Data
**GET** `/api/dashboard`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (Mahasiswa):**
```json
{
    "success": true,
    "data": {
        "role": "mahasiswa",
        "mahasiswa": {
            "id": 1,
            "nim": "1234567890",
            "nama": "John Doe",
            "prodi": "Teknik Informatika"
        },
        "semester_aktif": {
            "id": 1,
            "nama": "Semester Ganjil 2024/2025",
            "tahun_ajaran": "2024/2025"
        },
        "krs_semester_ini": [...],
        "jadwal_hari_ini": [...],
        "total_sks": 18,
        "pengumuman_terbaru": [...]
    }
}
```

## Mahasiswa Endpoints

### KRS

#### Get KRS List
**GET** `/api/mahasiswa/krs`

**Headers:**
```
Authorization: Bearer {token}
```

#### Get Available Courses
**GET** `/api/mahasiswa/krs/create`

**Headers:**
```
Authorization: Bearer {token}
```

#### Add KRS
**POST** `/api/mahasiswa/krs`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "jadwal_kuliah_id": 1
}
```

#### Delete KRS
**DELETE** `/api/mahasiswa/krs/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

### KHS

#### Get Semester List
**GET** `/api/mahasiswa/khs`

**Headers:**
```
Authorization: Bearer {token}
```

#### Get KHS by Semester
**GET** `/api/mahasiswa/khs/{semester_id?}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "semester": {
            "id": 1,
            "nama": "Semester Ganjil 2024/2025",
            "tahun_ajaran": "2024/2025"
        },
        "nilais": [
            {
                "id": 1,
                "mata_kuliah": "Pemrograman Web",
                "kode_mk": "IF123",
                "sks": 3,
                "dosen": "Dr. Ahmad",
                "nilai_tugas": 85,
                "nilai_uts": 80,
                "nilai_uas": 90,
                "nilai_akhir": 85.5,
                "huruf_mutu": "A",
                "bobot": 4.0,
                "status": "selesai"
            }
        ],
        "total_sks": 18,
        "ipk": 3.75
    }
}
```

### Presensi

#### Get Presensi List
**GET** `/api/mahasiswa/presensi?jadwal_id={id}`

**Headers:**
```
Authorization: Bearer {token}
```

#### Get Presensi Detail
**GET** `/api/mahasiswa/presensi/{jadwal_id}`

**Headers:**
```
Authorization: Bearer {token}
```

## Dosen Endpoints

### Nilai

#### Get Nilai List
**GET** `/api/dosen/nilai?jadwal_id={id}`

**Headers:**
```
Authorization: Bearer {token}
```

#### Create Nilai Form
**GET** `/api/dosen/nilai/create/{jadwal_id}`

**Headers:**
```
Authorization: Bearer {token}
```

#### Store Nilai
**POST** `/api/dosen/nilai/{jadwal_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "krs_id": [1, 2, 3],
    "nilai_tugas": [85, 90, 80],
    "nilai_uts": [80, 85, 75],
    "nilai_uas": [90, 95, 85]
}
```

#### Update Nilai
**PUT** `/api/dosen/nilai/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "nilai_tugas": 85,
    "nilai_uts": 80,
    "nilai_uas": 90,
    "catatan": "Bagus"
}
```

### Presensi

#### Get Presensi List
**GET** `/api/dosen/presensi?jadwal_id={id}&pertemuan={number}`

**Headers:**
```
Authorization: Bearer {token}
```

#### Create Presensi Form
**GET** `/api/dosen/presensi/create/{jadwal_id}`

**Headers:**
```
Authorization: Bearer {token}
```

#### Store Presensi
**POST** `/api/dosen/presensi/{jadwal_id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "pertemuan": 1,
    "tanggal": "2024-01-15",
    "presensi": [
        {
            "mahasiswa_id": 1,
            "status": "hadir",
            "catatan": null
        },
        {
            "mahasiswa_id": 2,
            "status": "izin",
            "catatan": "Izin sakit"
        }
    ]
}
```

#### Get Presensi Detail
**GET** `/api/dosen/presensi/{jadwal_id}`

**Headers:**
```
Authorization: Bearer {token}
```

## Notifikasi

### Get Notifikasi List
**GET** `/api/notifikasi`

**Headers:**
```
Authorization: Bearer {token}
```

### Mark as Read
**POST** `/api/notifikasi/{id}/read`

**Headers:**
```
Authorization: Bearer {token}
```

### Mark All as Read
**POST** `/api/notifikasi/read-all`

**Headers:**
```
Authorization: Bearer {token}
```

### Get Unread Count
**GET** `/api/notifikasi/unread-count`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "count": 5
    }
}
```

### Get Recent Notifications
**GET** `/api/notifikasi/recent`

**Headers:**
```
Authorization: Bearer {token}
```

## Profile

### Get Profile
**GET** `/api/profile`

**Headers:**
```
Authorization: Bearer {token}
```

### Update Profile
**PUT** `/api/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com"
}
```

### Update Password
**PUT** `/api/profile/password`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

## Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Data tidak ditemukan"
}
```

### 422 Validation Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

## Catatan Penting

1. **Token Authentication**: Semua endpoint (kecuali login) memerlukan header `Authorization: Bearer {token}`
2. **Role-based Access**: Endpoint tertentu hanya bisa diakses oleh role tertentu (mahasiswa, dosen, admin)
3. **Base URL**: Ganti `http://localhost:8000` dengan URL server production saat deploy
4. **CORS**: Pastikan konfigurasi CORS sudah benar di `config/cors.php` untuk mobile app

## Contoh Penggunaan di Mobile App

### Flutter/Dart
```dart
final response = await http.post(
  Uri.parse('http://localhost:8000/api/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'user@example.com',
    'password': 'password123',
  }),
);

final data = jsonDecode(response.body);
final token = data['data']['token'];

// Simpan token untuk request selanjutnya
await storage.write(key: 'token', value: token);
```

### React Native
```javascript
const login = async (email, password) => {
  const response = await fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, password }),
  });
  
  const data = await response.json();
  const token = data.data.token;
  
  // Simpan token
  await AsyncStorage.setItem('token', token);
};

// Request dengan token
const getDashboard = async () => {
  const token = await AsyncStorage.getItem('token');
  const response = await fetch('http://localhost:8000/api/dashboard', {
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  });
  
  return await response.json();
};
```

