# Tugas Hari Ini (1-2 Jam) - Step by Step

## ⏰ Timeline: 1-2 Jam

### 🎯 Tujuan
1. Test semua API endpoints
2. Pastikan tidak ada error
3. Buat test users jika belum ada
4. Siap untuk develop mobile app

---

## 📝 Step 1: Pastikan Server Running (5 menit)

### A. Start Laravel Server
```bash
php artisan serve
```

**Expected Output:**
```
INFO  Server running on [http://127.0.0.1:8000]
```

### B. Test Server
Buka browser: `http://localhost:8000`
- Harus muncul halaman login atau redirect ke login

✅ **Checkpoint:** Server running dan bisa diakses

---

## 📝 Step 2: Buat Test Users (15 menit)

### A. Buka Tinker
```bash
php artisan tinker
```

### B. Buat User Admin
```php
$admin = \App\Models\User::create([
    'name' => 'Admin Test',
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
echo "Admin created: " . $admin->email . "\n";
```

### C. Buat User Dosen
```php
$dosen = \App\Models\User::create([
    'name' => 'Dosen Test',
    'email' => 'dosen@test.com',
    'password' => bcrypt('password'),
    'role' => 'dosen'
]);

// Buat data dosen
$dosenData = \App\Models\Dosen::create([
    'user_id' => $dosen->id,
    'nidn' => '1234567890',
    'nama' => 'Dosen Test',
    'email' => 'dosen@test.com',
    'status' => 'aktif'
]);
echo "Dosen created: " . $dosen->email . "\n";
```

### D. Buat User Mahasiswa
```php
// Buat prodi dulu jika belum ada
$prodi = \App\Models\Prodi::firstOrCreate([
    'kode' => 'TI',
    'nama' => 'Teknik Informatika'
]);

$mahasiswa = \App\Models\User::create([
    'name' => 'Mahasiswa Test',
    'email' => 'mahasiswa@test.com',
    'password' => bcrypt('password'),
    'role' => 'mahasiswa'
]);

// Buat data mahasiswa
$mahasiswaData = \App\Models\Mahasiswa::create([
    'user_id' => $mahasiswa->id,
    'nim' => '1234567890',
    'nama' => 'Mahasiswa Test',
    'prodi_id' => $prodi->id,
    'jenis_kelamin' => 'L',
    'semester' => 3,
    'status' => 'aktif'
]);
echo "Mahasiswa created: " . $mahasiswa->email . "\n";
```

### E. Exit Tinker
```php
exit
```

✅ **Checkpoint:** 3 users berhasil dibuat (admin, dosen, mahasiswa)

---

## 📝 Step 3: Test API dengan Postman/cURL (30 menit)

### A. Install Postman (jika belum ada)
- Download: https://www.postman.com/downloads/
- Atau gunakan cURL di terminal

### B. Test Login - Admin

**Postman:**
- Method: `POST`
- URL: `http://localhost:8000/api/login`
- Headers: `Content-Type: application/json`
- Body (raw JSON):
```json
{
  "email": "admin@test.com",
  "password": "password"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@test.com\",\"password\":\"password\"}"
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin Test",
      "email": "admin@test.com",
      "role": "admin"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

**⚠️ IMPORTANT:** Copy token dari response!

### C. Test Dashboard - Admin

**Postman:**
- Method: `GET`
- URL: `http://localhost:8000/api/dashboard`
- Headers: 
  - `Authorization: Bearer YOUR_TOKEN_HERE`
  - `Content-Type: application/json`

**cURL:**
```bash
curl -X GET http://localhost:8000/api/dashboard ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE" ^
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "role": "admin",
    "statistics": {
      "total_mahasiswa": 1,
      "total_dosen": 1,
      "total_prodi": 1,
      "total_mata_kuliah": 0,
      "krs_pending": 0
    }
  }
}
```

### D. Test Login - Mahasiswa

**Body:**
```json
{
  "email": "mahasiswa@test.com",
  "password": "password"
}
```

**Expected:** Response dengan token

### E. Test Dashboard - Mahasiswa

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "role": "mahasiswa",
    "mahasiswa": {
      "id": 1,
      "nim": "1234567890",
      "nama": "Mahasiswa Test",
      "prodi": "Teknik Informatika"
    },
    "krs_semester_ini": [],
    "jadwal_hari_ini": [],
    "total_sks": 0,
    "pengumuman_terbaru": []
  }
}
```

### F. Test Login - Dosen

**Body:**
```json
{
  "email": "dosen@test.com",
  "password": "password"
}
```

### G. Test Endpoint Lainnya

**Get User:**
```bash
GET http://localhost:8000/api/user
Headers: Authorization: Bearer {token}
```

**Get Notifikasi:**
```bash
GET http://localhost:8000/api/notifikasi
Headers: Authorization: Bearer {token}
```

**Get Profile:**
```bash
GET http://localhost:8000/api/profile
Headers: Authorization: Bearer {token}
```

✅ **Checkpoint:** Semua endpoint utama berhasil di-test

---

## 📝 Step 4: Test Error Handling (15 menit)

### A. Test Login dengan Password Salah
```json
{
  "email": "admin@test.com",
  "password": "wrongpassword"
}
```
**Expected:** 422 atau 401 error

### B. Test Dashboard tanpa Token
```bash
GET http://localhost:8000/api/dashboard
(No Authorization header)
```
**Expected:** 401 Unauthorized

### C. Test dengan Token Invalid
```bash
GET http://localhost:8000/api/dashboard
Headers: Authorization: Bearer invalid_token_12345
```
**Expected:** 401 Unauthorized

✅ **Checkpoint:** Error handling bekerja dengan baik

---

## 📝 Step 5: Buat Postman Collection (20 menit)

### A. Buat Collection Baru di Postman
1. Klik "New" > "Collection"
2. Nama: "SIAKAD API"

### B. Setup Variables
1. Klik collection > "Variables" tab
2. Tambahkan:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (kosongkan, akan diisi otomatis)

### C. Buat Folder "Auth"
1. **Login Request:**
   - Method: POST
   - URL: `{{base_url}}/login`
   - Body: 
   ```json
   {
     "email": "admin@test.com",
     "password": "password"
   }
   ```
   - Tests tab (untuk auto-save token):
   ```javascript
   if (pm.response.code === 200) {
       var jsonData = pm.response.json();
       pm.collectionVariables.set("token", jsonData.data.token);
   }
   ```

2. **Get User:**
   - Method: GET
   - URL: `{{base_url}}/user`
   - Headers: `Authorization: Bearer {{token}}`

3. **Logout:**
   - Method: POST
   - URL: `{{base_url}}/logout`
   - Headers: `Authorization: Bearer {{token}}`

### D. Buat Folder "Dashboard"
1. **Get Dashboard:**
   - Method: GET
   - URL: `{{base_url}}/dashboard`
   - Headers: `Authorization: Bearer {{token}}`

### E. Buat Folder "Mahasiswa"
1. **Get KRS:**
   - Method: GET
   - URL: `{{base_url}}/mahasiswa/krs`
   - Headers: `Authorization: Bearer {{token}}`

2. **Get KHS:**
   - Method: GET
   - URL: `{{base_url}}/mahasiswa/khs`
   - Headers: `Authorization: Bearer {{token}}`

### F. Export Collection
1. Klik collection > "..." > "Export"
2. Simpan sebagai `SIAKAD_API.postman_collection.json`

✅ **Checkpoint:** Postman collection siap digunakan

---

## 📝 Step 6: Dokumentasi Hasil Testing (10 menit)

### A. Catat Hasil Testing

Buat file `TEST_RESULTS.md`:

```markdown
# Hasil Testing API - [Tanggal]

## ✅ Endpoint yang Berhasil
- [x] POST /api/login (Admin)
- [x] POST /api/login (Mahasiswa)
- [x] POST /api/login (Dosen)
- [x] GET /api/dashboard (Admin)
- [x] GET /api/dashboard (Mahasiswa)
- [x] GET /api/dashboard (Dosen)
- [x] GET /api/user
- [x] GET /api/notifikasi
- [x] GET /api/profile

## ⚠️ Endpoint yang Error
- [ ] (Jika ada, tulis di sini)

## 📝 Notes
- Token berhasil di-generate
- Error handling bekerja dengan baik
- CORS sudah dikonfigurasi
```

### B. Screenshot (Opsional)
- Screenshot response dari Postman
- Simpan di folder `docs/screenshots/`

✅ **Checkpoint:** Dokumentasi testing selesai

---

## 📝 Step 7: Checklist Final (5 menit)

### Verifikasi
- [ ] Server running tanpa error
- [ ] 3 test users berhasil dibuat
- [ ] Login berhasil untuk semua role
- [ ] Token berhasil di-generate
- [ ] Dashboard bisa diakses dengan token
- [ ] Error handling bekerja
- [ ] Postman collection dibuat
- [ ] Dokumentasi testing dibuat

### Jika Ada Error
1. Cek `storage/logs/laravel.log`
2. Cek response error di Postman
3. Fix error atau catat untuk ditangani nanti

---

## 🎯 Hasil Akhir (Setelah 1-2 Jam)

### Yang Harus Sudah Selesai:
1. ✅ Server running
2. ✅ Test users dibuat
3. ✅ API endpoints di-test
4. ✅ Postman collection dibuat
5. ✅ Dokumentasi testing

### Siap untuk:
- ✅ Develop mobile app
- ✅ Setup mobile project
- ✅ Implementasi authentication

---

## 🚀 Next Steps (Setelah Selesai)

1. **Baca `QUICK_START.md`** untuk panduan mobile app
2. **Setup mobile project** (Flutter/React Native)
3. **Implementasi authentication** di mobile app

---

## ⚠️ Troubleshooting

### Error: "Class 'App\Models\User' not found"
**Solution:** Pastikan sudah run `composer install`

### Error: "SQLSTATE[HY000] [14] unable to open database file"
**Solution:** Pastikan `database/database.sqlite` ada, atau setup MySQL di `.env`

### Error: "419 CSRF Token Mismatch"
**Solution:** Normal untuk API, pastikan menggunakan `POST /api/login` bukan `/login`

### Error: "Route [api.login] not defined"
**Solution:** Pastikan sudah run `php artisan route:cache` atau clear cache

---

**Selamat! Setelah selesai, API Anda sudah siap untuk digunakan! 🎉**

