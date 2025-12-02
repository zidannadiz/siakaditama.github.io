# Konfigurasi Email di File .env

## 📍 Lokasi Konfigurasi

Konfigurasi email biasanya berada di file `.env` sekitar **baris 50-57**, setelah konfigurasi database.

## ✅ Konfigurasi yang Benar

Tambahkan atau edit konfigurasi berikut di file `.env`:

```env
# ============================================
# KONFIGURASI EMAIL (Baris 50-57)
# ============================================

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@siakad.ac.id
MAIL_FROM_NAME="${APP_NAME}"
```

## 📋 Penjelasan Setiap Variabel

### 1. `MAIL_MAILER`
- **Nilai:** `smtp` (untuk production) atau `log` (untuk development)
- **Fungsi:** Menentukan driver email yang digunakan
- **Contoh:**
  - `smtp` → Menggunakan SMTP server
  - `log` → Email disimpan di `storage/logs/laravel.log` (untuk testing)

### 2. `MAIL_HOST`
- **Nilai:** Host SMTP server
- **Contoh:**
  - `smtp.gmail.com` (Gmail)
  - `smtp.mailtrap.io` (Mailtrap untuk testing)
  - `mail.yourdomain.com` (Custom SMTP)

### 3. `MAIL_PORT`
- **Nilai:** Port SMTP server
- **Contoh:**
  - `587` → Untuk TLS encryption
  - `465` → Untuk SSL encryption
  - `2525` → Mailtrap

### 4. `MAIL_USERNAME`
- **Nilai:** Email atau username untuk autentikasi SMTP
- **Contoh:** `your-email@gmail.com`

### 5. `MAIL_PASSWORD`
- **Nilai:** Password atau App Password untuk autentikasi SMTP
- **Untuk Gmail:** Wajib menggunakan App Password (bukan password biasa)
- **Cara membuat App Password:** https://myaccount.google.com/apppasswords

### 6. `MAIL_ENCRYPTION`
- **Nilai:** `tls` atau `ssl`
- **Aturan:**
  - Port `587` → Gunakan `tls`
  - Port `465` → Gunakan `ssl`

### 7. `MAIL_FROM_ADDRESS`
- **Nilai:** Alamat email pengirim
- **Contoh:** `noreply@siakad.ac.id` atau `your-email@gmail.com`

### 8. `MAIL_FROM_NAME`
- **Nilai:** Nama pengirim yang akan muncul di email
- **Contoh:** `"SIAKAD"` atau `"${APP_NAME}"`

## 🔧 Contoh Konfigurasi Lengkap

### Gmail (Production)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="SIAKAD"
```

**Catatan Gmail:**
1. Aktifkan 2-Step Verification di Google Account
2. Buat App Password: https://myaccount.google.com/apppasswords
3. Gunakan App Password (format: `xxxx xxxx xxxx xxxx`) sebagai `MAIL_PASSWORD`

### Mailtrap (Testing)

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=test@mailtrap.io
MAIL_FROM_NAME="SIAKAD Test"
```

### Development (Log Email)

```env
MAIL_MAILER=log
```

Email akan tersimpan di `storage/logs/laravel.log` - tidak perlu setup SMTP.

## 🚀 Setelah Mengubah .env

Setelah mengubah file `.env`, **WAJIB** menjalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

Ini memastikan Laravel membaca konfigurasi terbaru.

## ✅ Verifikasi Konfigurasi

Gunakan command untuk mengecek konfigurasi:

```bash
# Cek konfigurasi tanpa mengirim email
php artisan email:test --check

# Atau kirim test email
php artisan email:test your-email@example.com
```

## 🔍 Checklist

Sebelum menggunakan email notification, pastikan:

- [ ] `MAIL_MAILER` sudah di-set (`smtp` atau `log`)
- [ ] Jika `smtp`, semua variabel berikut sudah diisi:
  - [ ] `MAIL_HOST`
  - [ ] `MAIL_PORT`
  - [ ] `MAIL_USERNAME`
  - [ ] `MAIL_PASSWORD`
  - [ ] `MAIL_ENCRYPTION`
  - [ ] `MAIL_FROM_ADDRESS`
  - [ ] `MAIL_FROM_NAME`
- [ ] Port dan encryption sudah sesuai (587+tls atau 465+ssl)
- [ ] Sudah menjalankan `php artisan config:clear`
- [ ] Sudah test dengan `php artisan email:test --check`
- [ ] Queue worker berjalan: `php artisan queue:work`

## ❓ Troubleshooting

### Email tidak terkirim?

1. **Cek konfigurasi:**
   ```bash
   php artisan email:test --check
   ```

2. **Cek log:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Cek queue:**
   - Pastikan queue worker berjalan: `php artisan queue:work`
   - Cek failed jobs: `php artisan queue:failed`

### Error "Authentication failed"

- Pastikan `MAIL_USERNAME` benar
- Untuk Gmail, **WAJIB** menggunakan App Password, bukan password biasa
- Pastikan App Password sudah dibuat di: https://myaccount.google.com/apppasswords

### Error "Connection refused"

- Pastikan `MAIL_HOST` benar
- Pastikan `MAIL_PORT` sesuai
- Cek firewall/network connection

## 📝 Catatan Penting

1. ✅ **Jangan commit file `.env` ke Git** - file ini berisi informasi sensitif
2. ✅ **Gunakan `.env.example`** sebagai template untuk dokumentasi
3. ✅ **Untuk production**, gunakan SMTP dengan credentials yang valid
4. ✅ **Untuk development**, gunakan `MAIL_MAILER=log` agar tidak perlu setup SMTP
5. ✅ **Setelah mengubah `.env`**, selalu jalankan `php artisan config:clear`

---

**Butuh bantuan?** Jalankan `php artisan email:test --check` untuk melihat detail konfigurasi dan error!

