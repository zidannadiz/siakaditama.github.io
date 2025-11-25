# Laporan Audit Keamanan SIAKAD

**Tanggal Audit:** 26 November 2025  
**Versi Aplikasi:** SIAKAD - Sistem Informasi Akademik

## Ringkasan Eksekutif

Audit keamanan ini mengidentifikasi beberapa area yang perlu diperbaiki untuk meningkatkan keamanan aplikasi SIAKAD. Secara keseluruhan, aplikasi sudah menggunakan beberapa praktik keamanan dasar Laravel, namun masih ada beberapa celah keamanan yang perlu ditangani.

---

## 1. AUTHENTICATION & AUTHORIZATION

### ✅ **Yang Sudah Baik:**
- Password di-hash menggunakan `hashed` cast di model User
- Session regeneration setelah login (`$request->session()->regenerate()`)
- Role-based access control menggunakan middleware `role:admin`, `role:dosen`, `role:mahasiswa`
- Middleware `auth` diterapkan pada route yang memerlukan autentikasi

### ⚠️ **Masalah yang Ditemukan:**

#### 1.1. Public Route untuk QR Scan
**Lokasi:** `routes/web.php:47`
```php
Route::get('/qr-scan/{token}', ...)->name('qr-presensi.public-scan');
```
**Masalah:** Route ini dapat diakses tanpa autentikasi, berpotensi untuk abuse jika token tidak divalidasi dengan benar.

**Rekomendasi:**
- Tambahkan rate limiting pada route ini
- Validasi token dengan expiry time
- Log semua akses ke route ini

#### 1.2. Role Middleware - Tidak Ada Rate Limiting
**Lokasi:** `app/Http/Middleware/RoleMiddleware.php`
**Masalah:** Tidak ada proteksi terhadap brute force attack pada login.

**Rekomendasi:**
- Tambahkan rate limiting pada route login
- Implementasi account lockout setelah beberapa percobaan gagal

---

## 2. CSRF PROTECTION

### ✅ **Yang Sudah Baik:**
- CSRF protection aktif secara default
- Custom `VerifyCsrfToken` middleware dengan logging
- Webhook route dikecualikan dengan benar (`payment/xendit/webhook`)

### ⚠️ **Masalah yang Ditemukan:**

#### 2.1. Webhook Route Tanpa Verifikasi Signature
**Lokasi:** `routes/web.php:43`
```php
Route::post('/payment/xendit/webhook', ...)
```
**Masalah:** Webhook dapat diakses tanpa verifikasi signature dari Xendit, berpotensi untuk request spoofing.

**Rekomendasi:**
- Verifikasi signature Xendit di controller webhook
- Validasi IP address Xendit (jika memungkinkan)

---

## 3. SQL INJECTION

### ✅ **Yang Sudah Baik:**
- Menggunakan Eloquent ORM yang sudah aman
- Query builder dengan parameter binding
- `selectRaw` dan `DB::raw` digunakan dengan hati-hati (hanya untuk agregasi, bukan input user)

### ⚠️ **Masalah yang Ditemukan:**

#### 3.1. Penggunaan `selectRaw` dengan String Literal
**Lokasi:** Beberapa controller menggunakan `selectRaw` dengan string literal yang aman, namun perlu dipastikan tidak ada input user yang langsung dimasukkan.

**Status:** ✅ **AMAN** - Semua `selectRaw` yang ditemukan hanya menggunakan string literal untuk agregasi (COUNT, SUM, CASE WHEN), bukan input user.

---

## 4. XSS (CROSS-SITE SCRIPTING)

### ✅ **Yang Sudah Baik:**
- Blade template engine secara default escape output (`{{ }}`)
- Penggunaan `{!! !!}` hanya ditemukan di beberapa tempat yang perlu ditinjau

### ⚠️ **Masalah yang Ditemukan:**

#### 4.1. Penggunaan `{!! !!}` di Blade Templates
**Lokasi:** Ditemukan di beberapa file view
**Masalah:** Penggunaan `{!! !!}` dapat memungkinkan XSS jika data tidak di-sanitize.

**Rekomendasi:**
- Audit semua penggunaan `{!! !!}` 
- Pastikan data yang di-output sudah di-sanitize atau berasal dari sumber terpercaya
- Pertimbangkan menggunakan HTML Purifier untuk konten user-generated

---

## 5. MASS ASSIGNMENT

### ✅ **Yang Sudah Baik:**
- Semua model menggunakan `$fillable` untuk whitelist atribut yang dapat di-assign
- Tidak ditemukan penggunaan `$guarded = []` yang berbahaya

### ⚠️ **Masalah yang Ditemukan:**

#### 5.1. User Model - Role dapat di-assign
**Lokasi:** `app/Models/User.php:21-26`
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',  // ⚠️ Berpotensi berbahaya
];
```
**Masalah:** Field `role` dapat di-assign melalui mass assignment, berpotensi untuk privilege escalation.

**Rekomendasi:**
- Hapus `role` dari `$fillable`
- Assign `role` secara eksplisit di controller dengan validasi yang ketat
- Atau gunakan `$guarded = []` dan `$fillable` untuk field yang aman saja

---

## 6. FILE UPLOAD SECURITY

### ✅ **Yang Sudah Baik:**
- Validasi file type (`mimes:doc,docx`)
- Validasi file size (`max:5120` = 5MB)
- File disimpan di `storage/app/private` (tidak langsung accessible via web)

### ⚠️ **Masalah yang Ditemukan:**

#### 6.1. File Upload - Tidak Ada Validasi Nama File
**Lokasi:** `app/Http/Controllers/Admin/TemplateKrsKhsController.php:57`
```php
$filePath = $file->store('templates/krs-khs', 'local');
```
**Masalah:** Nama file asli tidak divalidasi, berpotensi untuk path traversal atau karakter berbahaya.

**Rekomendasi:**
- Sanitize nama file sebelum disimpan
- Generate nama file unik (UUID atau hash)
- Validasi ekstensi file secara eksplisit

#### 6.2. File Upload - Tidak Ada Scanning Malware
**Masalah:** Tidak ada scanning untuk malware atau virus pada file yang di-upload.

**Rekomendasi:**
- Pertimbangkan menggunakan antivirus scanning untuk file yang di-upload
- Atau setidaknya validasi struktur file (untuk doc/docx)

---

## 7. SESSION SECURITY

### ✅ **Yang Sudah Baik:**
- Session regeneration setelah login
- Session lifetime dikonfigurasi
- Session disimpan di database (jika menggunakan database driver)

### ⚠️ **Masalah yang Ditemukan:**

#### 7.1. Session Lifetime - Tidak Ada Idle Timeout
**Masalah:** Session tidak expired berdasarkan idle time, hanya berdasarkan lifetime.

**Rekomendasi:**
- Implementasi idle timeout untuk session
- Atau setidaknya peringatan sebelum session expired

---

## 8. API SECURITY

### ✅ **Yang Sudah Baik:**
- Menggunakan Laravel Sanctum untuk API authentication
- Token-based authentication untuk mobile app
- Password di-hash dengan benar

### ⚠️ **Masalah yang Ditemukan:**

#### 8.1. API Token - Tidak Ada Rate Limiting
**Lokasi:** `routes/api.php`
**Masalah:** Tidak ada rate limiting pada API endpoints, berpotensi untuk abuse.

**Rekomendasi:**
- Tambahkan rate limiting pada API routes
- Gunakan `throttle:60,1` untuk membatasi request per menit

#### 8.2. API Token - Tidak Ada Expiry
**Masalah:** Token tidak memiliki expiry time, token yang tercuri dapat digunakan selamanya.

**Rekomendasi:**
- Implementasi token expiry
- Atau setidaknya refresh token mechanism

---

## 9. INPUT VALIDATION

### ✅ **Yang Sudah Baik:**
- Validasi input menggunakan Laravel validation
- Validasi email, numeric, dll sudah diterapkan

### ⚠️ **Masalah yang Ditemukan:**

#### 9.1. Validasi - Tidak Konsisten
**Masalah:** Beberapa controller mungkin tidak melakukan validasi dengan lengkap.

**Rekomendasi:**
- Audit semua controller untuk memastikan validasi lengkap
- Gunakan Form Request classes untuk validasi yang lebih terstruktur

---

## 10. ERROR HANDLING & INFORMATION DISCLOSURE

### ⚠️ **Masalah yang Ditemukan:**

#### 10.1. Error Messages - Potensi Information Disclosure
**Lokasi:** Beberapa controller
**Masalah:** Error messages mungkin mengungkapkan informasi sensitif tentang struktur database atau aplikasi.

**Rekomendasi:**
- Pastikan `APP_DEBUG=false` di production
- Custom error pages yang tidak mengungkapkan informasi sensitif
- Log error details ke file log, bukan ke user

---

## 11. PASSWORD SECURITY

### ✅ **Yang Sudah Baik:**
- Password di-hash menggunakan bcrypt (default Laravel)
- Password tidak ditampilkan di response

### ⚠️ **Masalah yang Ditemukan:**

#### 11.1. Password Policy - Tidak Ada Enforced
**Masalah:** Tidak ada enforced password policy (min length, complexity, dll).

**Rekomendasi:**
- Implementasi password policy:
  - Minimum 8 karakter
  - Harus mengandung huruf besar, huruf kecil, dan angka
  - Tidak boleh sama dengan email atau nama
- Validasi saat user membuat/update password

---

## 12. LOGGING & MONITORING

### ✅ **Yang Sudah Baik:**
- Logging untuk CSRF token mismatch
- Logging untuk error di beberapa tempat

### ⚠️ **Masalah yang Ditemukan:**

#### 12.1. Security Event Logging - Tidak Lengkap
**Masalah:** Tidak ada logging untuk:
- Failed login attempts
- Role changes
- Sensitive data access
- File uploads

**Rekomendasi:**
- Implementasi security event logging
- Monitor untuk suspicious activities
- Alert untuk multiple failed login attempts

---

## PRIORITAS PERBAIKAN

### 🔴 **KRITIS (Harus Segera Diperbaiki):**
1. Mass Assignment - Role field di User model
2. File Upload - Validasi nama file
3. API Security - Rate limiting dan token expiry
4. Password Policy - Enforced password requirements

### 🟡 **PENTING (Perlu Diperbaiki dalam Waktu Dekat):**
1. Public Route - Rate limiting untuk QR scan
2. Webhook - Verifikasi signature Xendit
3. XSS - Audit penggunaan `{!! !!}`
4. Session - Idle timeout
5. Security Logging - Implementasi security event logging

### 🟢 **SEDANG (Dapat Diperbaiki Secara Bertahap):**
1. Error Handling - Custom error pages
2. Input Validation - Form Request classes
3. File Upload - Malware scanning

---

## KESIMPULAN

Aplikasi SIAKAD secara keseluruhan sudah menggunakan beberapa praktik keamanan dasar Laravel dengan baik. Namun, masih ada beberapa celah keamanan yang perlu ditangani, terutama terkait:

1. **Mass Assignment** - Field `role` di User model
2. **File Upload Security** - Validasi dan sanitization
3. **API Security** - Rate limiting dan token management
4. **Password Policy** - Enforced requirements

Disarankan untuk memperbaiki masalah-masalah kritis terlebih dahulu, kemudian dilanjutkan dengan masalah penting dan sedang.

---

## REKOMENDASI TAMBAHAN

1. **Security Headers:**
   - Implementasi security headers (CSP, X-Frame-Options, dll)
   - Gunakan middleware seperti `spatie/laravel-security-headers`

2. **Regular Security Updates:**
   - Update Laravel dan dependencies secara rutin
   - Monitor security advisories

3. **Penetration Testing:**
   - Lakukan penetration testing secara berkala
   - Gunakan tools seperti OWASP ZAP atau Burp Suite

4. **Backup & Recovery:**
   - Pastikan backup database dilakukan secara rutin
   - Test recovery procedure

5. **Environment Variables:**
   - Pastikan `.env` tidak di-commit ke repository
   - Gunakan secrets management untuk production

---

**Dibuat oleh:** AI Security Auditor  
**Tanggal:** 26 November 2025

