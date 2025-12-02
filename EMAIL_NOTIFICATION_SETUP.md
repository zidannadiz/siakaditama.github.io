# Setup Email Notification

## Konfigurasi Email

### 1. Setup SMTP di `.env`

Tambahkan atau edit konfigurasi email di file `.env`:

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

### 2. Untuk Gmail

Jika menggunakan Gmail:
1. Aktifkan "2-Step Verification" di Google Account
2. Buat "App Password" di: https://myaccount.google.com/apppasswords
3. Gunakan App Password sebagai `MAIL_PASSWORD`

### 3. Untuk Mailtrap (Testing)

Untuk testing, gunakan Mailtrap:

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

### 4. Untuk Development (Log Email)

Untuk development, email akan tersimpan di log:

```env
MAIL_MAILER=log
```

Email akan tersimpan di `storage/logs/laravel.log`

## Queue Configuration

Email dikirim secara async menggunakan queue. Pastikan queue worker berjalan:

### Development
```bash
php artisan queue:work
```

### Production
Setup supervisor atau systemd untuk menjalankan queue worker secara otomatis.

## Jenis Email Notification

### 1. KRS Approved
Dikirim saat admin menyetujui KRS mahasiswa.

### 2. KRS Rejected
Dikirim saat admin menolak KRS mahasiswa.

### 3. Nilai Inputted
Dikirim saat dosen menginput nilai baru (hanya untuk nilai baru, bukan update).

### 4. General Notification
Notifikasi umum yang bisa digunakan untuk berbagai keperluan.

## Testing Email

### 1. Test dengan Command (Recommended)

Gunakan command `email:test` untuk mengecek konfigurasi dan mengirim test email:

```bash
# Cek konfigurasi email tanpa mengirim email
php artisan email:test --check

# Kirim test email ke alamat tertentu
php artisan email:test test@example.com

# Atau tanpa parameter, akan ditanyakan email tujuan
php artisan email:test
```

Command ini akan:
- ✅ Menampilkan konfigurasi email saat ini
- ✅ Memvalidasi konfigurasi email
- ✅ Mengirim test email ke alamat yang ditentukan
- ✅ Memberikan tips jika ada error

### 2. Test Manual

1. Setup email di `.env`
2. Jalankan queue worker: `php artisan queue:work`
3. Lakukan aksi yang memicu email (contoh: approve KRS)
4. Cek email di inbox atau log

### 3. Test via Tinker

```php
php artisan tinker

use App\Models\User;
use App\Services\EmailNotificationService;

// Test General Notification
$user = User::first(); // Atau user yang ingin ditest
EmailNotificationService::sendGeneralNotification(
    $user,
    'Test Email',
    'Halo!',
    'Ini adalah email test dari sistem SIAKAD.',
    'Buka Dashboard',
    route('admin.dashboard')
);
```

### 4. Verifikasi Konfigurasi Email di .env

Pastikan konfigurasi di file `.env` (sekitar baris 50-57) sudah benar:

```env
# Pastikan semua variabel berikut ada dan benar:
MAIL_MAILER=smtp                    # atau "log" untuk development
MAIL_HOST=smtp.gmail.com            # host SMTP
MAIL_PORT=587                       # port SMTP (587 untuk TLS, 465 untuk SSL)
MAIL_USERNAME=your-email@gmail.com  # username/email SMTP
MAIL_PASSWORD=your-app-password     # password/app password
MAIL_ENCRYPTION=tls                 # tls atau ssl
MAIL_FROM_ADDRESS=noreply@siakad.ac.id
MAIL_FROM_NAME="${APP_NAME}"
```

**Catatan:**
- Setelah mengubah `.env`, jalankan: `php artisan config:clear`
- Untuk Gmail, gunakan App Password, bukan password biasa
- Untuk development/testing, gunakan `MAIL_MAILER=log` agar email tersimpan di log

## Troubleshooting

### Email tidak terkirim

1. **Cek konfigurasi `.env`** - Pastikan semua variabel email sudah benar
2. **Cek queue worker** - Pastikan queue worker sedang berjalan
3. **Cek log** - Lihat `storage/logs/laravel.log` untuk error
4. **Cek failed jobs** - Lihat tabel `failed_jobs` di database
5. **Test SMTP** - Gunakan Mailtrap atau Gmail SMTP untuk testing

### Email masuk spam

1. Setup SPF record di DNS
2. Setup DKIM record
3. Gunakan domain sendiri untuk `MAIL_FROM_ADDRESS`
4. Hindari kata-kata yang memicu spam

### Queue tidak jalan

1. Pastikan queue driver di `.env`: `QUEUE_CONNECTION=database`
2. Pastikan tabel `jobs` sudah dibuat: `php artisan migrate`
3. Jalankan queue worker: `php artisan queue:work`
4. Cek failed jobs: `php artisan queue:failed`

## Fitur

- ✅ Email otomatis untuk KRS approve/reject
- ✅ Email otomatis untuk nilai baru
- ✅ Queue support (async sending)
- ✅ Error handling dan logging
- ✅ Email templates yang responsif
- ✅ Integration dengan notifikasi in-app

## Catatan Penting

1. Email dikirim secara async menggunakan queue untuk performa yang lebih baik
2. Pastikan queue worker selalu berjalan di production
3. Monitor failed jobs secara berkala
4. Email hanya dikirim untuk nilai baru, bukan update nilai
5. Email tidak akan dikirim jika user tidak memiliki email yang valid

