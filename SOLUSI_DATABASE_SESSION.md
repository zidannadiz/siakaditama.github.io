# 🔧 Solusi: Gunakan Database Session Driver

## 🐛 Masalah
Login berhasil, tapi setiap klik master data selalu redirect ke login.

## 🔍 Analisis
Dari test yang dilakukan:
- ✅ User ditemukan dan password benar
- ✅ Auth::attempt() berhasil  
- ⚠️ **Session tidak started dengan benar** - ini masalahnya!

File session driver kadang tidak reliable, terutama di Windows dengan Laravel. **Database session driver lebih reliable**.

## ✅ Solusi: Gunakan Database Session

### Langkah 1: Pastikan Sessions Table Ada
```bash
php artisan migrate
```

Sessions table sudah ada di migration `0001_01_01_000000_create_users_table.php`.

### Langkah 2: Ubah .env
Buka file `.env` dan ubah:
```env
SESSION_DRIVER=database
```

Pastikan juga:
```env
APP_URL=http://127.0.0.1:8000
SESSION_LIFETIME=1440
SESSION_DOMAIN=
SESSION_SAME_SITE=lax
```

### Langkah 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Langkah 4: Restart Server (WAJIB!)
```bash
# Stop server (Ctrl+C)
php artisan serve
```

### Langkah 5: Clear Browser Cache & Cookies (WAJIB!)
- Tekan `Ctrl + Shift + Delete`
- Pilih:
  - ✅ "Cookies and other site data"
  - ✅ "Cached images and files"
- Time range: "All time"
- Click "Clear data"

**ATAU** gunakan **Incognito/Private mode**

### Langkah 6: Test Login & Navigation
1. Buka: `http://127.0.0.1:8000/login` (bukan localhost!)
2. Login dengan: `noer@gmail.com` / `zidanlangut14`
3. Setelah masuk dashboard, test navigasi:
   - ✅ Klik "Program Studi" → Harus masuk, tidak redirect login
   - ✅ Klik "Mahasiswa" → Harus masuk, tidak redirect login
   - ✅ Klik "Dosen" → Harus masuk, tidak redirect login
   - ✅ Klik "Mata Kuliah" → Harus masuk, tidak redirect login
   - ✅ Klik "Semester" → Harus masuk, tidak redirect login
   - ✅ Klik "Jadwal Kuliah" → Harus masuk, tidak redirect login
   - ✅ Klik "Pengumuman" → Harus masuk, tidak redirect login

## 🔍 Verifikasi Database Session

Setelah login, cek di database:
```sql
SELECT * FROM sessions ORDER BY last_activity DESC LIMIT 1;
```

Harus ada record dengan:
- `user_id` = ID user yang login
- `last_activity` = timestamp terbaru
- `payload` = berisi session data

## 📝 Keuntungan Database Session

1. **Lebih Reliable**: Tidak ada masalah dengan file permissions
2. **Lebih Mudah Debug**: Bisa lihat session langsung di database
3. **Lebih Konsisten**: Tidak ada masalah dengan file system
4. **Lebih Mudah Maintenance**: Bisa cleanup session lama dengan mudah

## ⚠️ Catatan Penting

1. **WAJIB restart server** setelah perubahan .env
2. **WAJIB clear browser cache/cookies** setelah perubahan
3. **Gunakan `http://127.0.0.1:8000`** (bukan localhost)
4. **Gunakan Incognito mode** untuk test yang lebih bersih

## 🔄 Jika Masih Bermasalah

Jika setelah menggunakan database session masih bermasalah:

1. **Check Laravel Logs:**
   ```powershell
   Get-Content storage\logs\laravel.log -Tail 50
   ```

2. **Check Database Sessions:**
   ```sql
   SELECT id, user_id, last_activity FROM sessions ORDER BY last_activity DESC;
   ```

3. **Check Cookie di Browser:**
   - Developer Tools (F12) > Application > Cookies
   - Harus ada cookie `laravel-session` dengan value

4. **Test dengan browser lain** (Chrome, Firefox, Edge)

