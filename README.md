# SIAKAD - Sistem Informasi Akademik

Sistem Informasi Akademik modern yang dibangun dengan Laravel 11, Blade, dan Tailwind CSS.

## Fitur Utama

### 1. Sistem Multi-Role
- **Admin**: Mengelola semua data dan sistem
- **Dosen**: Input nilai dan melihat jadwal mengajar
- **Mahasiswa**: KRS, KHS, dan melihat jadwal

### 2. Master Data (Admin)
- ✅ CRUD Program Studi
- ✅ CRUD Mahasiswa
- ✅ CRUD Dosen
- ✅ CRUD Mata Kuliah
- ✅ CRUD Semester
- ✅ CRUD Jadwal Kuliah

### 3. Sistem Akademik
- ✅ **KRS (Kartu Rencana Studi)**: Mahasiswa memilih mata kuliah
- ✅ **KHS (Kartu Hasil Studi)**: Rekap nilai mahasiswa per semester
- ✅ **Input Nilai**: Dosen dapat input nilai (Tugas, UTS, UAS)
- ✅ **Perhitungan IPK**: Otomatis menghitung IPK berdasarkan bobot nilai

### 4. Pengumuman & Notifikasi
- ✅ Sistem pengumuman dengan kategori
- ✅ Target pengumuman (Semua, Mahasiswa, Dosen, Admin)
- ✅ Pin pengumuman penting

## Instalasi

### 1. Clone atau Copy Project
```bash
cd C:\laragon\www\SIAKAD-BARU
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
# Copy file .env.example ke .env (jika belum ada)
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Setup Database
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=sqlite
# atau
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migrations
```bash
php artisan migrate
```

### 6. Build Assets
```bash
npm run build
# atau untuk development
npm run dev
```

### 7. Jalankan Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## Setup Awal

### Membuat User Admin
Jalankan tinker:
```bash
php artisan tinker
```

Kemudian buat user admin:
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@siakad.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);
```

### Membuat Data Awal
1. Buat Program Studi melalui menu Admin > Program Studi
2. Buat Semester Aktif melalui menu Admin > Semester
3. Buat Mata Kuliah melalui menu Admin > Mata Kuliah
4. Buat Jadwal Kuliah melalui menu Admin > Jadwal Kuliah
5. Buat Dosen melalui menu Admin > Dosen
6. Buat Mahasiswa melalui menu Admin > Mahasiswa

## Struktur Database

### Tabel Utama
- `users` - User dengan role (admin, dosen, mahasiswa)
- `prodis` - Program Studi
- `mahasiswas` - Data Mahasiswa
- `dosens` - Data Dosen
- `semesters` - Semester Akademik
- `mata_kuliahs` - Mata Kuliah
- `jadwal_kuliahs` - Jadwal Kuliah
- `krs` - Kartu Rencana Studi
- `nilais` - Nilai Mahasiswa
- `pengumumans` - Pengumuman
- `notifikasis` - Notifikasi

## Cara Penggunaan

### Untuk Admin
1. Login dengan akun admin
2. Kelola master data (Prodi, Mahasiswa, Dosen, Mata Kuliah, Semester, Jadwal)
3. Setujui/tolak KRS mahasiswa
4. Buat pengumuman

### Untuk Dosen
1. Login dengan akun dosen
2. Lihat jadwal mengajar di dashboard
3. Input nilai mahasiswa melalui menu "Input Nilai"
4. Pilih kelas yang diampu, kemudian input nilai (Tugas, UTS, UAS)
5. Sistem otomatis menghitung nilai akhir, huruf mutu, dan bobot

### Untuk Mahasiswa
1. Login dengan akun mahasiswa
2. Lihat dashboard dengan jadwal hari ini
3. Ambil KRS melalui menu "KRS"
4. Pilih mata kuliah yang ingin diambil
5. KRS akan menunggu persetujuan admin
6. Lihat KHS melalui menu "KHS" untuk melihat nilai per semester

## Sistem Penilaian

### Perhitungan Nilai Akhir
- Tugas: 30%
- UTS: 30%
- UAS: 40%

### Konversi Huruf Mutu
- A (85-100): 4.00
- A- (80-84): 3.75
- B+ (75-79): 3.50
- B (70-74): 3.00
- B- (65-69): 2.75
- C+ (60-64): 2.50
- C (55-59): 2.00
- C- (50-54): 1.75
- D (40-49): 1.00
- E (<40): 0.00

### Perhitungan IPK
IPK = Total (SKS × Bobot) / Total SKS

## Teknologi yang Digunakan

- **Backend**: Laravel 11
- **Frontend**: Blade Templates + Tailwind CSS 4
- **Database**: SQLite (default) / MySQL
- **Build Tool**: Vite

## Struktur Folder

```
SIAKAD-BARU/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/        # Controller untuk Admin
│   │   │   ├── Dosen/        # Controller untuk Dosen
│   │   │   ├── Mahasiswa/    # Controller untuk Mahasiswa
│   │   │   └── Dashboard/     # Controller Dashboard
│   │   └── Middleware/       # Middleware (RoleMiddleware)
│   └── Models/               # Eloquent Models
├── database/
│   └── migrations/           # Database Migrations
├── resources/
│   ├── views/
│   │   ├── admin/           # Views untuk Admin
│   │   ├── dosen/           # Views untuk Dosen
│   │   ├── mahasiswa/       # Views untuk Mahasiswa
│   │   ├── dashboard/       # Views Dashboard
│   │   ├── auth/            # Views Authentication
│   │   └── layouts/         # Layout Templates
│   ├── css/
│   └── js/
└── routes/
    └── web.php              # Web Routes
```

## Catatan Penting

1. **Semester Aktif**: Pastikan ada semester yang statusnya "aktif" agar KRS dan sistem lainnya berfungsi
2. **Kuota Kelas**: Sistem akan mengecek kuota saat mahasiswa mengambil KRS
3. **Persetujuan KRS**: Admin harus menyetujui KRS sebelum mahasiswa bisa melihat nilai
4. **Input Nilai**: Dosen hanya bisa input nilai untuk kelas yang diampunya

## Development

### Menjalankan Development Server
```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Vite Dev Server
npm run dev
```

### Menjalankan Migrations
```bash
php artisan migrate
php artisan migrate:fresh  # Reset database
```

## License

MIT License

## Support

Untuk pertanyaan atau masalah, silakan buat issue di repository ini.
