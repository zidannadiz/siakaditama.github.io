# Verifikasi Konfigurasi Email

Panduan ini membantu Anda memverifikasi dan menguji konfigurasi email di file `.env`.

## 📋 Lokasi Konfigurasi Email

Konfigurasi email biasanya berada di file `.env` sekitar **baris 50-57** atau setelah konfigurasi database.

## ✅ Checklist Konfigurasi Email

Pastikan semua variabel berikut ada dan benar di file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@siakad.ac.id
MAIL_FROM_NAME="${APP_NAME}"
```

## 🔍 Cara Verifikasi

### 1. Menggunakan Command (Paling Mudah)

Jalankan command berikut untuk mengecek konfigurasi:

```bash
php artisan email:test --check
```

Command ini akan menampilkan:
- ✅ Konfigurasi email saat ini
- ✅ Status validasi setiap variabel
- ✅ Peringatan jika ada yang kurang
- ✅ Tips untuk perbaikan

### 2. Verifikasi Manual

Buka file `.env` dan pastikan:

#### ✅ Variabel Wajib untuk SMTP:
- [ ] `MAIL_MAILER=smtp` (atau `log` untuk development)
- [ ] `MAIL_HOST` sudah diisi (contoh: `smtp.gmail.com`)
- [ ] `MAIL_PORT` sudah diisi (contoh: `587` atau `465`)
- [ ] `MAIL_USERNAME` sudah diisi (email Anda)
- [ ] `MAIL_PASSWORD` sudah diisi (password/app password)
- [ ] `MAIL_ENCRYPTION` sudah diisi (`tls` atau `ssl`)
- [ ] `MAIL_FROM_ADDRESS` sudah diisi
- [ ] `MAIL_FROM_NAME` sudah diisi

#### ⚠️ Tips Port & Encryption:
- **Port 587** → Gunakan `MAIL_ENCRYPTION=tls`
- **Port 465** → Gunakan `MAIL_ENCRYPTION=ssl`

### 3. Setelah Mengubah .env

Setelah mengubah file `.env`, selalu jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

Ini akan memastikan Laravel membaca konfigurasi terbaru.

## 📧 Contoh Konfigurasi untuk Berbagai Provider

### Gmail

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

**Catatan untuk Gmail:**
1. Aktifkan 2-Step Verification di Google Account
2. Buat App Password di: https://myaccount.google.com/apppasswords
3. Gunakan App Password (16 karakter dengan spasi) sebagai `MAIL_PASSWORD`

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

Email akan tersimpan di `storage/logs/laravel.log`

## 🧪 Test Email Configuration

### Test Cepat

```bash
# Cek konfigurasi saja
php artisan email:test --check

# Kirim test email
php artisan email:test your-email@example.com
```

### Test dari Aplikasi

1. Login sebagai Admin
2. Approve atau Reject KRS mahasiswa
3. Mahasiswa seharusnya menerima email

## ❌ Troubleshooting

### Email tidak terkirim?

1. **Cek konfigurasi dengan command:**
   ```bash
   php artisan email:test --check
   ```

2. **Cek log:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Cek queue worker:**
   - Pastikan queue worker berjalan: `php artisan queue:work`
   - Cek failed jobs: `php artisan queue:failed`

4. **Cek error di database:**
   - Lihat tabel `failed_jobs` untuk detail error

### Error: "Connection refused"

- Pastikan `MAIL_HOST` benar
- Pastikan `MAIL_PORT` sesuai dengan encryption
- Cek firewall/network

### Error: "Authentication failed"

- Pastikan `MAIL_USERNAME` benar
- Pastikan `MAIL_PASSWORD` benar
- Untuk Gmail, gunakan App Password, bukan password biasa

### Email masuk ke spam?

1. Gunakan domain sendiri untuk `MAIL_FROM_ADDRESS`
2. Setup SPF record di DNS
3. Setup DKIM record

## 📝 Catatan Penting

1. ✅ Setelah mengubah `.env`, selalu jalankan `php artisan config:clear`
2. ✅ Untuk Gmail, **WAJIB** menggunakan App Password, bukan password biasa
3. ✅ Pastikan queue worker berjalan jika menggunakan queue: `php artisan queue:work`
4. ✅ Untuk development, gunakan `MAIL_MAILER=log` agar tidak perlu setup SMTP
5. ✅ Test email terlebih dahulu sebelum production

## 🎯 Quick Start

1. Buka file `.env`
2. Tambahkan/edit konfigurasi email (sekitar baris 50-57)
3. Jalankan: `php artisan config:clear`
4. Test dengan: `php artisan email:test --check`
5. Jika OK, kirim test: `php artisan email:test your-email@example.com`
6. Mulai queue worker: `php artisan queue:work`

---

**Jika masih ada masalah, jalankan command `php artisan email:test --check` untuk melihat detail error!**

