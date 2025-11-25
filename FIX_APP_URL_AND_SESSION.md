# 🔧 Perbaikan APP_URL dan Session Issue

## 🐛 Masalah Ditemukan
`APP_URL` masih `http://localhost:8000` padahal seharusnya `http://127.0.0.1:8000`

Ini bisa menyebabkan masalah dengan:
1. Cookie domain
2. Session cookie tidak ter-set dengan benar
3. CSRF token validation

## ✅ Solusi

### 1. Pastikan .env File Benar
Buka file `.env` dan pastikan:
```env
APP_URL=http://127.0.0.1:8000
SESSION_DRIVER=file
SESSION_LIFETIME=1440
SESSION_DOMAIN=
SESSION_SAME_SITE=lax
```

### 2. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 3. Restart Server (WAJIB!)
```bash
# Stop server (Ctrl+C)
php artisan serve
```

### 4. Clear Browser Cache & Cookies (WAJIB!)
- Tekan `Ctrl + Shift + Delete`
- Pilih:
  - ✅ "Cookies and other site data"
  - ✅ "Cached images and files"
- Time range: "All time"
- Click "Clear data"

**ATAU** gunakan **Incognito/Private mode**

### 5. Test Login & Navigation
1. Buka: `http://127.0.0.1:8000/login` (bukan localhost!)
2. Login dengan kredensial admin
3. Setelah masuk dashboard, test navigasi:
   - ✅ Klik "Program Studi" → Harus masuk, tidak redirect login
   - ✅ Klik "Mahasiswa" → Harus masuk, tidak redirect login
   - ✅ Klik "Dosen" → Harus masuk, tidak redirect login
   - ✅ Klik "Mata Kuliah" → Harus masuk, tidak redirect login
   - ✅ Klik "Semester" → Harus masuk, tidak redirect login
   - ✅ Klik "Jadwal Kuliah" → Harus masuk, tidak redirect login
   - ✅ Klik "Pengumuman" → Harus masuk, tidak redirect login

## 🔍 Verifikasi

### Check 1: Verify APP_URL
```bash
php artisan tinker --execute="echo config('app.url');"
```
Harus output: `http://127.0.0.1:8000`

### Check 2: Check Cookie in Browser
1. Buka Developer Tools (F12)
2. Tab **Application** > **Cookies** > `http://127.0.0.1:8000`
3. Harus ada cookie `laravel-session`
4. Cookie harus ada **value** (tidak kosong)
5. Cookie **Path** harus `/`
6. Cookie **SameSite** harus `Lax`

### Check 3: Check Session Files
```powershell
Get-ChildItem storage\framework\sessions | Sort-Object LastWriteTime -Descending | Select-Object -First 5
```
Setelah login, harus ada file session baru dengan timestamp terbaru.

## ⚠️ Catatan Penting

1. **JANGAN gunakan `localhost`** - gunakan `127.0.0.1:8000`
2. **WAJIB restart server** setelah perubahan .env
3. **WAJIB clear browser cache/cookies** setelah perubahan
4. **Gunakan Incognito mode** untuk test yang lebih bersih

## 🔄 Jika Masih Bermasalah

Jika setelah semua langkah di atas masih bermasalah:

1. **Coba gunakan database session driver:**
   ```env
   SESSION_DRIVER=database
   ```
   Lalu:
   ```bash
   php artisan migrate
   php artisan config:clear
   php artisan serve
   ```

2. **Check Laravel Logs:**
   ```powershell
   Get-Content storage\logs\laravel.log -Tail 50
   ```

3. **Test dengan browser lain** (Chrome, Firefox, Edge)

4. **Test dengan Incognito mode**

