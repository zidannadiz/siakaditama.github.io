# 📋 Pertanyaan dan Jawaban Proposal SIAKAD
## Sistem Informasi Akademik (SIAKAD) pada Institut Teknologi Al Mahrusiyah Kota Kediri

---

## 🎯 **KATEGORI PERTANYAAN: LATAR BELAKANG & MASALAH**

### **P1: Mengapa Anda memilih topik Sistem Informasi Akademik untuk penelitian ini?**

**Jawaban:**
Saya memilih topik Sistem Informasi Akademik karena beberapa alasan:

1. **Permasalahan Nyata di Lapangan:** Institut Teknologi Al Mahrusiyah masih menggunakan sistem manual dalam pengelolaan data akademik, yang menyebabkan proses pengolahan data menjadi lambat, risiko kesalahan input data tinggi, dan kesulitan melakukan pemantauan data akademik secara real-time.

2. **Gap Penelitian:** Penelitian terdahulu tentang sistem informasi akademik sebagian besar masih fokus di level sekolah (SD, SMP, SMA, SMK) dan menggunakan teknologi konvensional (PHP MySQL biasa atau CodeIgniter). Belum ada penelitian yang secara spesifik mengimplementasikan sistem informasi akademik berbasis Laravel untuk perguruan tinggi dengan kompleksitas kebutuhan yang lebih tinggi.

3. **Relevansi dengan Bidang Ilmu:** Topik ini sangat relevan dengan bidang ilmu sistem informasi dan teknologi informasi, khususnya dalam pengembangan aplikasi web modern.

4. **Manfaat Praktis:** Sistem ini akan memberikan manfaat langsung bagi institusi, dosen, mahasiswa, dan staf administrasi dalam meningkatkan efisiensi pengelolaan data akademik.

---

### **P2: Apa yang menjadi gap atau celah penelitian yang ingin Anda isi?**

**Jawaban:**
Gap penelitian yang ingin saya isi adalah:

1. **Level Institusi:** Penelitian terdahulu fokus pada level sekolah, sedangkan penelitian ini fokus pada perguruan tinggi dengan kompleksitas kebutuhan yang lebih tinggi.

2. **Teknologi:** Penelitian terdahulu menggunakan CodeIgniter atau PHP MySQL biasa, sedangkan penelitian ini menggunakan Laravel 11 yang lebih modern dengan fitur-fitur canggih seperti Eloquent ORM, middleware, dan arsitektur MVC yang lebih terstruktur.

3. **Fitur Lengkap:** Penelitian ini mengimplementasikan fitur-fitur modern seperti presensi QR Code, tugas online dengan anti-cheat system, integrasi payment gateway Xendit, dan sistem komunikasi real-time yang belum ada di penelitian terdahulu.

4. **Implementasi Langsung:** Penelitian ini mengimplementasikan sistem secara langsung di Institut Teknologi Al Mahrusiyah dengan 70+ fitur terintegrasi, bukan hanya prototype atau konsep.

---

### **P3: Bagaimana Anda mengidentifikasi permasalahan di institusi?**

**Jawaban:**
Saya mengidentifikasi permasalahan melalui:

1. **Observasi Langsung:** Melakukan observasi terhadap proses pengelolaan data akademik yang masih manual di Institut Teknologi Al Mahrusiyah.

2. **Wawancara:** Melakukan wawancara dengan admin, dosen, dan mahasiswa untuk memahami permasalahan yang mereka hadapi dalam proses akademik sehari-hari.

3. **Dokumentasi:** Menganalisis dokumen-dokumen terkait proses akademik untuk memahami alur kerja yang ada.

4. **Analisis Sistem Existing:** Menganalisis sistem yang sudah ada (jika ada) untuk mengidentifikasi kelemahan dan kebutuhan perbaikan.

**Permasalahan yang diidentifikasi:**
- Sistem pengelolaan data akademik masih manual
- Proses pengolahan data lambat
- Risiko kesalahan input data tinggi
- Kesulitan monitoring data akademik secara real-time
- Data akademik tersebar dan tidak terintegrasi

---

## 🔬 **KATEGORI PERTANYAAN: METODOLOGI & TEORI**

### **P4: Mengapa Anda memilih metodologi SDLC Waterfall?**

**Jawaban:**
Saya memilih metodologi SDLC Waterfall karena:

1. **Kesesuaian dengan Proyek:** Waterfall cocok untuk proyek yang memiliki kebutuhan yang jelas dan terdefinisi dengan baik sejak awal, seperti pengembangan sistem informasi akademik.

2. **Tahapan yang Sistematis:** Waterfall memiliki tahapan yang jelas dan sistematis: Analisis Kebutuhan Sistem → Perancangan Sistem → Implementasi → Pengujian, yang memudahkan dalam pengembangan dan dokumentasi.

3. **Dokumentasi Lengkap:** Setiap tahap menghasilkan dokumentasi yang lengkap, yang penting untuk penelitian akademik dan maintenance sistem di masa depan.

4. **Kontrol yang Baik:** Setiap tahap harus diselesaikan sebelum melanjutkan ke tahap berikutnya, memastikan kualitas setiap komponen sistem.

5. **Standar Industri:** Waterfall adalah metodologi yang sudah terbukti dan banyak digunakan dalam pengembangan sistem informasi.

**Tahapan yang dilakukan:**
1. **Analisis Kebutuhan Sistem:** Identifikasi kebutuhan fungsional dan non-fungsional
2. **Perancangan Sistem:** Perancangan basis data, arsitektur, dan antarmuka
3. **Implementasi:** Pengembangan aplikasi menggunakan Laravel 11, MySQL, dan Tailwind CSS
4. **Pengujian:** Unit testing, integration testing, dan user acceptance testing

---

### **P5: Teori apa saja yang menjadi landasan penelitian Anda?**

**Jawaban:**
Teori yang menjadi landasan penelitian ini adalah:

1. **Teori Sistem Informasi Akademik (Jogiyanto, 2017):**
   - Konsep sistem informasi akademik
   - Fungsi-fungsi utama sistem informasi akademik
   - Kriteria sistem informasi akademik yang baik

2. **Teori Aplikasi Web (Sebesta, 2018):**
   - Konsep aplikasi web dan kelebihannya
   - Arsitektur three-tier dalam aplikasi web
   - Framework Laravel dan keunggulannya

3. **Metodologi Pengembangan Sistem:**
   - System Development Life Cycle (SDLC) model Waterfall
   - Tahapan pengembangan sistem
   - Best practices dalam pengembangan sistem informasi berbasis web

4. **Teori Keamanan Sistem Informasi:**
   - Autentikasi dan otorisasi
   - Role-based access control (RBAC)
   - Proteksi terhadap serangan web (SQL Injection, XSS, CSRF)

---

### **P6: Mengapa Anda memilih framework Laravel?**

**Jawaban:**
Saya memilih Laravel karena:

1. **Framework Modern:** Laravel adalah framework PHP modern yang mengikuti best practices dan standar industri.

2. **Fitur Lengkap:** Laravel menyediakan banyak fitur built-in seperti:
   - Eloquent ORM untuk interaksi database yang mudah
   - Middleware untuk autentikasi dan otorisasi
   - Blade templating engine untuk view
   - Artisan CLI untuk produktivitas
   - Migration untuk version control database

3. **Keamanan:** Laravel memiliki proteksi built-in terhadap SQL Injection, XSS, CSRF, dan serangan web lainnya.

4. **Komunitas Besar:** Laravel memiliki komunitas yang besar dan dokumentasi yang lengkap, memudahkan dalam pengembangan dan troubleshooting.

5. **Skalabilitas:** Laravel mendukung arsitektur yang scalable dan mudah di-maintain untuk sistem yang kompleks.

6. **API Support:** Laravel Sanctum memudahkan pengembangan RESTful API untuk integrasi dengan aplikasi mobile.

---

## 💻 **KATEGORI PERTANYAAN: TEKNOLOGI & IMPLEMENTASI**

### **P7: Teknologi apa saja yang digunakan dalam pengembangan sistem?**

**Jawaban:**
Teknologi yang digunakan adalah:

**Backend:**
- **Laravel 11:** Framework PHP untuk pengembangan aplikasi web
- **PHP 8.1+:** Bahasa pemrograman server-side
- **MySQL/SQLite:** Database management system

**Frontend:**
- **Blade Templates:** Templating engine Laravel
- **Tailwind CSS 4:** Framework CSS untuk styling
- **JavaScript:** Untuk interaktivitas dan AJAX

**Authentication:**
- **Laravel Session:** Untuk autentikasi web application
- **Laravel Sanctum:** Untuk autentikasi API berbasis token

**Build Tool:**
- **Vite:** Untuk bundling dan optimasi assets

**Server:**
- **Apache/Nginx:** Web server
- **PHP-FPM:** PHP FastCGI Process Manager

**Payment Gateway:**
- **Xendit:** Integrasi payment gateway untuk pembayaran online

---

### **P8: Bagaimana arsitektur sistem yang Anda gunakan?**

**Jawaban:**
Sistem menggunakan arsitektur **three-tier architecture** yang terdiri dari:

1. **Presentation Layer (Layer Presentasi):**
   - Menangani antarmuka pengguna berbasis web
   - Menggunakan HTML, CSS (Tailwind CSS), dan JavaScript
   - Blade templating engine untuk rendering view

2. **Application Layer (Layer Aplikasi):**
   - Server aplikasi yang menangani logika bisnis
   - Menggunakan Laravel 11 dengan pola MVC (Model-View-Controller)
   - Komponen:
     - Routing Layer (web.php, api.php)
     - Middleware Layer (autentikasi, otorisasi, CSRF protection)
     - Controller Layer (business logic)
     - Model Layer (data access dengan Eloquent ORM)

3. **Data Layer (Layer Data):**
   - Database untuk menyimpan semua data akademik
   - Menggunakan MySQL untuk production atau SQLite untuk development
   - Relasi antar tabel untuk menjaga integritas data

**Keunggulan arsitektur ini:**
- Pemisahan concerns yang jelas
- Mudah di-maintain dan di-scale
- Keamanan yang lebih baik dengan middleware layer
- Fleksibilitas untuk pengembangan lebih lanjut

---

### **P9: Bagaimana sistem mengelola keamanan data?**

**Jawaban:**
Sistem mengimplementasikan keamanan data melalui beberapa lapisan:

1. **Autentikasi:**
   - Login dengan email dan password
   - Password di-hash menggunakan bcrypt
   - Session regeneration setelah login untuk mencegah session hijacking

2. **Otorisasi (Role-Based Access Control):**
   - Middleware untuk mengecek role pengguna (Admin, Dosen, Mahasiswa)
   - Setiap role memiliki akses yang berbeda sesuai kebutuhan
   - Proteksi route berdasarkan role

3. **Proteksi terhadap Serangan:**
   - **CSRF Protection:** Token CSRF untuk setiap form
   - **SQL Injection:** Eloquent ORM menggunakan parameterized queries
   - **XSS (Cross-Site Scripting):** Automatic escaping dalam Blade templating
   - **Input Validation:** Validasi input di client dan server side

4. **Keamanan Database:**
   - Foreign key constraints untuk integritas referensial
   - Database transactions untuk operasi kompleks
   - Password disimpan dalam bentuk hash

5. **Keamanan Session:**
   - Session expiry time
   - Session regeneration
   - HTTPS untuk enkripsi data yang ditransmisikan

---

### **P10: Bagaimana sistem menghitung IPK secara otomatis?**

**Jawaban:**
Sistem menghitung IPK secara otomatis dengan langkah-langkah berikut:

1. **Perhitungan Nilai Akhir:**
   - Nilai akhir = (Nilai Tugas × Bobot Tugas) + (Nilai UTS × Bobot UTS) + (Nilai UAS × Bobot UAS)
   - Bobot dapat dikonfigurasi di System Settings (default: Tugas 30%, UTS 30%, UAS 40%)

2. **Konversi ke Huruf Mutu:**
   - Nilai akhir dikonversi ke huruf mutu (A, A-, B+, B, B-, C+, C, C-, D, E) berdasarkan range yang dikonfigurasi

3. **Perhitungan IPK Semester:**
   - IPK Semester = Σ (Huruf Mutu × Bobot SKS) / Σ (Bobot SKS)
   - Dihitung untuk semua mata kuliah yang diambil di semester tersebut

4. **Perhitungan IPK Kumulatif:**
   - IPK Kumulatif = Σ (Huruf Mutu × Bobot SKS) / Σ (Bobot SKS)
   - Dihitung untuk semua mata kuliah yang sudah diambil dari semester 1 sampai semester aktif

5. **Otomatisasi:**
   - Perhitungan dilakukan otomatis setiap kali dosen menginput nilai
   - IPK ter-update real-time di KHS dan Transkrip Akademik

---

## 📊 **KATEGORI PERTANYAAN: FITUR & FUNGSIONALITAS**

### **P11: Fitur apa saja yang diimplementasikan dalam sistem?**

**Jawaban:**
Sistem mengimplementasikan 70+ fitur yang terintegrasi, meliputi:

**1. Sistem Multi-Role & Autentikasi:**
- Login/Logout untuk Admin, Dosen, dan Mahasiswa
- Dashboard berbeda untuk setiap role
- Role-based access control

**2. Master Data (Admin):**
- CRUD Program Studi
- CRUD Mahasiswa (dengan Import/Export)
- CRUD Dosen
- CRUD Mata Kuliah
- CRUD Semester
- CRUD Jadwal Kuliah

**3. Sistem Akademik:**
- KRS (Kartu Rencana Studi) dengan sistem approval admin
- KHS (Kartu Hasil Studi) per semester
- Transkrip Akademik dengan PDF
- Input Nilai oleh Dosen (Tugas, UTS, UAS)
- Perhitungan IPK otomatis

**4. Sistem Presensi:**
- QR Code Presensi real-time
- Presensi Manual oleh Dosen
- Statistik Presensi
- Laporan Presensi

**5. Tugas & Ujian Online:**
- Dosen membuat tugas dan ujian
- Mahasiswa submit tugas dan take exam
- Anti-cheat system (fullscreen, prevent copy-paste, tab detection)
- Auto-grading (pilihan ganda) dan manual grading (essay)

**6. Sistem Pembayaran:**
- Integrasi Xendit Payment Gateway
- Manajemen tagihan dan tracking pembayaran
- Webhook untuk update status otomatis

**7. Sistem Komunikasi:**
- Chat real-time
- Forum diskusi
- Q&A (Question & Answer)

**8. Pengumuman & Notifikasi:**
- Buat pengumuman dengan kategori
- Notifikasi in-app dan email
- Target pengumuman (Semua, Mahasiswa, Dosen, Admin)

**9. System Settings:**
- Konfigurasi Bobot Penilaian
- Konfigurasi Huruf Mutu
- Pengaturan Semester Aktif
- Informasi Aplikasi

**10. Laporan & Statistik:**
- Laporan pembayaran
- Laporan akademik
- Statistik presensi
- Export ke Excel dan PDF

---

### **P12: Bagaimana sistem presensi QR Code bekerja?**

**Jawaban:**
Sistem presensi QR Code bekerja dengan alur berikut:

1. **Generate QR Code (Dosen):**
   - Dosen membuka menu QR Presensi
   - Memilih jadwal kuliah dan pertemuan
   - Mengatur durasi QR Code aktif (misalnya 15 menit)
   - Sistem generate token unik dan QR Code
   - QR Code ditampilkan di layar dosen

2. **Scan QR Code (Mahasiswa):**
   - Mahasiswa membuka menu Scan QR Presensi di aplikasi mobile/web
   - Mengaktifkan kamera untuk scan QR Code
   - Sistem membaca token dari QR Code

3. **Validasi:**
   - Sistem memvalidasi token (apakah masih aktif, belum expired)
   - Mengecek apakah mahasiswa sudah scan sebelumnya (prevent duplicate)
   - Mengecek apakah jadwal kuliah sesuai dengan jadwal yang diampu dosen
   - Mengecek apakah mahasiswa terdaftar di jadwal kuliah tersebut

4. **Simpan Presensi:**
   - Jika semua validasi berhasil, sistem menyimpan presensi dengan status "Hadir"
   - Membuat notifikasi untuk dosen bahwa mahasiswa sudah presensi
   - QR Code otomatis expired setelah durasi yang ditentukan

**Keamanan:**
- Token memiliki expiry time untuk mencegah abuse
- Satu token hanya bisa digunakan sekali per mahasiswa
- Validasi jadwal untuk memastikan presensi di waktu yang tepat

---

### **P13: Bagaimana sistem anti-cheat pada ujian online bekerja?**

**Jawaban:**
Sistem anti-cheat pada ujian online mengimplementasikan beberapa mekanisme:

1. **Fullscreen Mode:**
   - Aplikasi memaksa fullscreen saat ujian dimulai
   - User tidak bisa keluar dari fullscreen tanpa mengakhiri ujian

2. **Prevent Copy-Paste:**
   - Disable copy-paste saat ujian berlangsung
   - Mencegah mahasiswa copy soal atau jawaban dari sumber lain

3. **Tab Detection:**
   - Sistem mendeteksi jika user membuka tab baru atau switch tab
   - Jika terdeteksi, sistem memberikan peringatan atau mengakhiri ujian

4. **Time Limit:**
   - Setiap ujian memiliki batas waktu
   - Timer countdown yang tidak bisa di-reset
   - Ujian otomatis submit saat waktu habis

5. **Randomize Soal:**
   - Soal di-randomize untuk setiap mahasiswa
   - Mencegah mahasiswa saling contek

6. **Logging:**
   - Sistem mencatat aktivitas mencurigakan (tab switch, fullscreen exit, dll)
   - Data logging digunakan untuk evaluasi dan penilaian

**Catatan:** Sistem anti-cheat ini bekerja di aplikasi web. Untuk aplikasi mobile, bisa ditambahkan fitur tambahan seperti disable screenshot atau lock screen orientation.

---

## 🔄 **KATEGORI PERTANYAAN: INTEGRASI & API**

### **P14: Bagaimana sistem terintegrasi dengan payment gateway Xendit?**

**Jawaban:**
Sistem terintegrasi dengan Xendit melalui:

1. **Registrasi Payment:**
   - Mahasiswa membuat tagihan pembayaran (SPP, UKT, dll)
   - Sistem mengirim request ke Xendit API untuk membuat virtual account atau payment link
   - Xendit mengembalikan virtual account number atau payment link

2. **Payment Tracking:**
   - Sistem menyimpan payment ID dari Xendit
   - Status pembayaran di-update melalui webhook dari Xendit
   - Webhook dipanggil otomatis oleh Xendit saat pembayaran berhasil

3. **Webhook Handler:**
   - Sistem memiliki endpoint khusus untuk menerima webhook dari Xendit
   - Webhook memverifikasi signature untuk memastikan request berasal dari Xendit
   - Sistem update status pembayaran di database (pending → paid)

4. **Notifikasi:**
   - Setelah pembayaran berhasil, sistem mengirim notifikasi ke mahasiswa dan admin
   - Notifikasi via in-app dan email

**Keuntungan Integrasi:**
- Pembayaran otomatis terverifikasi
- Tidak perlu manual check bukti transfer
- Real-time update status pembayaran
- Support multiple payment method (virtual account, e-wallet, dll)

---

### **P15: Bagaimana API untuk aplikasi mobile bekerja?**

**Jawaban:**
Sistem menyediakan RESTful API menggunakan Laravel Sanctum:

1. **Autentikasi API:**
   - Mobile app mengirim request ke `POST /api/login` dengan email dan password
   - Server mengembalikan access token (Bearer Token)
   - Token disimpan di mobile app dan digunakan untuk setiap request selanjutnya

2. **Endpoint API:**
   - **Dashboard:** `GET /api/dashboard` - Data dashboard sesuai role
   - **KRS:** `GET /api/mahasiswa/krs`, `POST /api/mahasiswa/krs`
   - **KHS:** `GET /api/mahasiswa/khs`
   - **Jadwal:** `GET /api/mahasiswa/jadwal`
   - **Nilai:** `GET /api/dosen/nilai`, `POST /api/dosen/nilai`
   - **Presensi:** `GET /api/mahasiswa/presensi`, `POST /api/mahasiswa/qr-presensi/scan`
   - Dan endpoint lainnya sesuai kebutuhan

3. **Response Format:**
   - Semua response dalam format JSON
   - Struktur konsisten: `{success: true/false, message: "...", data: {...}}`
   - HTTP status code sesuai standar REST (200, 201, 400, 401, 403, 404, 500)

4. **Keamanan:**
   - Token-based authentication dengan Laravel Sanctum
   - Middleware untuk autentikasi dan otorisasi
   - Rate limiting untuk mencegah abuse

5. **Shared Business Logic:**
   - API menggunakan controller dan model yang sama dengan web application
   - Memastikan konsistensi logika bisnis antara web dan mobile
   - Database yang sama untuk konsistensi data

---

## 📈 **KATEGORI PERTANYAAN: HASIL & EVALUASI**

### **P16: Bagaimana hasil pengujian sistem?**

**Jawaban:**
Hasil pengujian sistem menunjukkan:

1. **Fungsionalitas:**
   - ✅ Semua modul berhasil diimplementasikan dan berfungsi dengan baik
   - ✅ Semua fitur dapat diakses sesuai dengan role pengguna
   - ✅ Sistem dapat mengolah data akademik secara terintegrasi

2. **Pengujian API:**
   - ✅ Endpoint authentication berhasil (login, logout)
   - ✅ Endpoint dashboard berhasil untuk semua role
   - ✅ Endpoint CRUD berfungsi dengan baik
   - ✅ Token authentication bekerja dengan sempurna

3. **Keamanan:**
   - ✅ Role-based access control bekerja dengan baik
   - ✅ Proteksi CSRF, SQL Injection, XSS berfungsi
   - ✅ Autentikasi dan otorisasi berjalan sesuai desain

4. **User Acceptance Testing:**
   - ✅ Admin dapat mengelola semua master data dengan mudah
   - ✅ Dosen dapat input nilai dan presensi dengan lancar
   - ✅ Mahasiswa dapat mengambil KRS dan mengakses informasi akademik dengan baik

**Kesimpulan:** Sistem berhasil diimplementasikan dan siap digunakan untuk produksi.

---

### **P17: Apa keunggulan sistem yang Anda kembangkan dibandingkan sistem yang sudah ada?**

**Jawaban:**
Keunggulan sistem yang dikembangkan:

1. **Teknologi Modern:**
   - Menggunakan Laravel 11 (framework modern) vs CodeIgniter atau PHP biasa
   - Arsitektur three-tier yang scalable dan mudah di-maintain
   - Support untuk aplikasi mobile melalui RESTful API

2. **Fitur Lengkap & Modern:**
   - Presensi QR Code real-time
   - Tugas dan ujian online dengan anti-cheat system
   - Integrasi payment gateway Xendit
   - Sistem komunikasi real-time (chat, forum, Q&A)
   - 70+ fitur terintegrasi dalam satu platform

3. **User Experience:**
   - Interface modern dengan Tailwind CSS
   - Responsive design untuk berbagai perangkat
   - Notifikasi real-time
   - Dashboard yang informatif untuk setiap role

4. **Keamanan:**
   - Multi-layer security (autentikasi, otorisasi, proteksi serangan)
   - Role-based access control yang ketat
   - Audit logging untuk tracking aktivitas

5. **Integrasi:**
   - Terintegrasi dengan payment gateway
   - Support untuk aplikasi mobile
   - API yang lengkap untuk integrasi dengan sistem lain

6. **Implementasi Langsung:**
   - Sistem diimplementasikan langsung di Institut Teknologi Al Mahrusiyah
   - Bukan hanya prototype, tetapi sistem yang siap produksi

---

### **P18: Bagaimana sistem meningkatkan efisiensi pengelolaan data akademik?**

**Jawaban:**
Sistem meningkatkan efisiensi melalui:

1. **Otomatisasi Proses:**
   - Perhitungan IPK otomatis saat input nilai
   - Validasi otomatis saat pengambilan KRS
   - Notifikasi otomatis untuk berbagai event (KRS approved, nilai baru, dll)

2. **Integrasi Data:**
   - Semua data akademik terpusat dalam satu database
   - Menghindari duplikasi dan inkonsistensi data
   - Data dapat diakses real-time oleh semua pengguna yang berwenang

3. **Self-Service:**
   - Mahasiswa dapat mengambil KRS sendiri tanpa harus datang ke kampus
   - Mahasiswa dapat melihat KHS dan transkrip kapan saja
   - Dosen dapat input nilai secara online

4. **Pengurangan Beban Administratif:**
   - Admin tidak perlu mencari data dari berbagai tempat
   - Laporan dapat di-generate otomatis
   - Statistik dapat dilihat real-time di dashboard

5. **Kecepatan Proses:**
   - Proses yang sebelumnya memakan waktu lama (misalnya approval KRS) sekarang bisa dilakukan dalam hitungan menit
   - Presensi QR Code lebih cepat daripada presensi manual
   - Input nilai lebih efisien dengan form yang terstruktur

**Hasil:** Peningkatan efisiensi operasional secara signifikan dan pengurangan waktu yang dibutuhkan untuk proses akademik.

---

## 🎓 **KATEGORI PERTANYAAN: KETERBATASAN & REKOMENDASI**

### **P19: Apa keterbatasan penelitian ini?**

**Jawaban:**
Keterbatasan penelitian ini meliputi:

1. **Ruang Lingkup Institusi:**
   - Penelitian dibatasi hanya pada Institut Teknologi Al Mahrusiyah
   - Hasil tidak dapat digeneralisasi tanpa kajian ulang untuk institusi lain
   - Setiap institusi mungkin memiliki kebutuhan spesifik yang berbeda

2. **Ruang Lingkup Fungsional:**
   - Fokus pada aspek akademik (KRS, nilai, jadwal, presensi)
   - Tidak mencakup sistem keuangan lengkap atau perpustakaan digital
   - Beberapa fitur masih bisa dikembangkan lebih lanjut (misalnya: forgot password, academic warning)

3. **Ruang Lingkup Waktu:**
   - Penelitian mencakup periode pengembangan dan implementasi awal
   - Evaluasi jangka panjang terhadap efektivitas sistem berada di luar lingkup
   - Perlu monitoring lebih lanjut untuk melihat dampak jangka panjang

4. **Teknologi:**
   - Sistem menggunakan teknologi web-based, belum fully optimized untuk mobile native
   - Beberapa fitur mobile masih menggunakan web view

5. **Pengujian:**
   - Pengujian dilakukan dengan data terbatas (test users)
   - Perlu pengujian dengan data real dan volume besar untuk melihat performa sistem

---

### **P20: Apa rekomendasi untuk pengembangan sistem lebih lanjut?**

**Jawaban:**
Rekomendasi untuk pengembangan lebih lanjut:

1. **Fitur Prioritas Tinggi:**
   - **Forgot Password / Reset Password:** Sangat penting untuk user experience
   - **Peringatan Akademik:** Sistem peringatan otomatis untuk mahasiswa dengan IPK rendah
   - **Absensi Dosen:** Tracking kehadiran dosen saat mengajar

2. **Fitur Menambah Value:**
   - **Konsultasi Akademik:** Sistem konsultasi antara mahasiswa dengan dosen PA
   - **Kurikulum & Rencana Studi Otomatis:** Master data kurikulum dengan validasi prasyarat otomatis
   - **Dashboard Analytics Lanjutan:** Grafik dan chart interaktif untuk statistik
   - **Batch Import/Export:** Import/Export data via Excel untuk efisiensi

3. **Pengembangan Mobile:**
   - **Aplikasi Mobile Native:** Pengembangan aplikasi mobile native (Flutter/React Native)
   - **Push Notifications:** Notifikasi real-time untuk aplikasi mobile
   - **Offline Mode:** Fitur offline untuk akses data tanpa internet

4. **Integrasi & Ekstensi:**
   - **Sistem Perpustakaan Digital:** Integrasi dengan sistem perpustakaan
   - **Sistem Keuangan Lengkap:** Modul keuangan yang lebih komprehensif
   - **Integrasi dengan Sistem Eksternal:** Integrasi dengan sistem lain (misalnya: sistem absensi fingerprint)

5. **Optimasi & Perbaikan:**
   - **Performance Optimization:** Optimasi query dan caching untuk performa yang lebih baik
   - **Security Enhancement:** Penambahan fitur keamanan seperti 2FA (Two-Factor Authentication)
   - **Backup & Restore Otomatis:** Sistem backup otomatis untuk keamanan data

6. **Evaluasi & Monitoring:**
   - **Evaluasi Jangka Panjang:** Monitoring efektivitas sistem dalam jangka panjang
   - **User Feedback System:** Sistem untuk mengumpulkan feedback dari pengguna
   - **Analytics & Reporting:** Laporan analitik yang lebih detail

---

## 📚 **KATEGORI PERTANYAAN: TEORI & REFERENSI**

### **P21: Referensi apa saja yang Anda gunakan dalam penelitian ini?**

**Jawaban:**
Referensi yang digunakan meliputi:

1. **Buku Teks:**
   - Jogiyanto, H. (2017). *Sistem Informasi: Konsep Dasar dan Aplikasi*. Yogyakarta: Andi Offset.
   - Turban, E., Pollard, C., & Wood, G. (2018). *Information Technology for Management: On-Demand Strategies for Performance, Growth and Sustainability*. Wiley.
   - Sommerville, I. (2016). *Software Engineering*. Pearson.
   - Sebesta, R. W. (2018). *Programming the World Wide Web*. Pearson.

2. **Jurnal Ilmiah:**
   - Ambara, M. P., & Antarajaya, I. N. S. (2020). "Pengembangan Sistem Informasi Akademik untuk Mendukung Proses Perkuliahan di Lembaga Pendidikan," *Jurnal Teknologi Informasi dan Komputer*, 6(2), 112-125.

3. **Dokumentasi Teknis:**
   - Laravel Documentation (https://laravel.com/docs)
   - MySQL Documentation
   - Xendit API Documentation

4. **Sumber Online:**
   - Best practices pengembangan sistem informasi akademik
   - Artikel tentang arsitektur web application
   - Tutorial dan dokumentasi framework Laravel

---

### **P22: Bagaimana penelitian ini berkontribusi pada pengembangan ilmu pengetahuan?**

**Jawaban:**
Penelitian ini berkontribusi pada pengembangan ilmu pengetahuan melalui:

1. **Kontribusi Teoritis:**
   - Penguatan metodologi SDLC Waterfall dalam pengembangan sistem informasi akademik
   - Kontribusi pada pengembangan ilmu pengetahuan di bidang sistem informasi berbasis web
   - Referensi untuk penelitian selanjutnya dalam pengembangan sistem akademik menggunakan Laravel

2. **Kontribusi Praktis:**
   - Implementasi sistem informasi akademik modern untuk perguruan tinggi
   - Solusi konkret untuk meningkatkan efisiensi pengelolaan akademik
   - Best practices dalam pengembangan sistem informasi akademik berbasis Laravel

3. **Kontribusi Metodologis:**
   - Pendekatan pengembangan sistem yang sistematis dan terstruktur
   - Dokumentasi lengkap yang dapat dijadikan referensi
   - Metodologi yang dapat diadaptasi untuk institusi lain

4. **Kontribusi Teknis:**
   - Implementasi teknologi modern (Laravel 11) dalam sistem informasi akademik
   - Integrasi payment gateway dan API untuk aplikasi mobile
   - Arsitektur sistem yang scalable dan mudah di-maintain

---

## 🎯 **KATEGORI PERTANYAAN: IMPLEMENTASI & DEPLOYMENT**

### **P23: Bagaimana sistem di-deploy dan di-maintain?**

**Jawaban:**
Sistem dapat di-deploy dan di-maintain dengan:

1. **Deployment:**
   - **Server Requirements:** PHP 8.1+, MySQL 5.7+, Apache/Nginx
   - **Setup Environment:** Konfigurasi file .env untuk production
   - **Database Migration:** Jalankan `php artisan migrate` untuk setup database
   - **Asset Compilation:** Build assets dengan `npm run build`
   - **Optimization:** Jalankan `php artisan optimize` untuk optimasi performa

2. **Maintenance:**
   - **Backup Database:** Sistem memiliki fitur backup & restore untuk database
   - **Update System:** Update Laravel dan dependencies secara berkala
   - **Monitoring:** Monitor log aplikasi dan error untuk troubleshooting
   - **Security Updates:** Update security patches secara berkala

3. **Scalability:**
   - Arsitektur three-tier memungkinkan scaling horizontal
   - Database dapat di-optimize dengan indexing dan query optimization
   - Caching dapat diimplementasikan untuk meningkatkan performa

4. **Support:**
   - Dokumentasi lengkap untuk admin dan developer
   - Training untuk admin dan user
   - Support system untuk troubleshooting

---

### **P24: Bagaimana user (admin, dosen, mahasiswa) menggunakan sistem?**

**Jawaban:**
Cara penggunaan sistem untuk setiap role:

**Admin:**
1. Login dengan email dan password
2. Dashboard menampilkan statistik lengkap sistem
3. Menu Master Data untuk mengelola Program Studi, Mahasiswa, Dosen, Mata Kuliah, Semester, Jadwal
4. Menu KRS Approval untuk menyetujui/menolak KRS mahasiswa
5. Menu Payment Management untuk verifikasi pembayaran
6. Menu System Settings untuk konfigurasi sistem
7. Menu Laporan untuk melihat laporan pembayaran dan akademik

**Dosen:**
1. Login dengan email dan password
2. Dashboard menampilkan jadwal mengajar hari ini
3. Menu Input Nilai untuk menginput nilai mahasiswa (Tugas, UTS, UAS)
4. Menu Presensi untuk input presensi manual atau generate QR Code
5. Menu Tugas & Ujian untuk membuat tugas dan ujian online
6. Menu Jadwal untuk melihat jadwal mengajar

**Mahasiswa:**
1. Login dengan email dan password
2. Dashboard menampilkan jadwal hari ini, KRS status, dan pengumuman
3. Menu KRS untuk mengambil KRS semester aktif
4. Menu KHS untuk melihat Kartu Hasil Studi per semester
5. Menu Transkrip untuk melihat transkrip akademik lengkap
6. Menu Presensi untuk scan QR Code presensi
7. Menu Tugas untuk submit tugas dan take exam
8. Menu Payment untuk membuat tagihan pembayaran
9. Menu Chat untuk komunikasi dengan dosen/admin

**Catatan:** Setiap role memiliki akses yang berbeda sesuai dengan kebutuhan dan otoritas mereka.

---

## 📝 **CATATAN PENTING UNTUK PRESENTASI**

### **Tips Menjawab Pertanyaan:**

1. **Jawab dengan Jelas dan Terstruktur:**
   - Gunakan poin-poin untuk memudahkan pemahaman
   - Berikan contoh konkret jika memungkinkan

2. **Kaitkan dengan Penelitian:**
   - Selalu kaitkan jawaban dengan penelitian yang dilakukan
   - Gunakan data dan hasil yang sudah diperoleh

3. **Jujur tentang Keterbatasan:**
   - Akui keterbatasan penelitian dengan jujur
   - Jelaskan bagaimana keterbatasan tersebut dapat diatasi di masa depan

4. **Siapkan Demo:**
   - Siapkan demo sistem untuk menunjukkan fitur-fitur yang sudah diimplementasikan
   - Siapkan backup plan jika demo tidak bisa dilakukan

5. **Pahami Dokumentasi:**
   - Pastikan memahami semua aspek sistem yang dikembangkan
   - Siapkan jawaban untuk pertanyaan teknis yang detail

6. **Tetap Tenang:**
   - Dengarkan pertanyaan dengan baik
   - Ambil waktu sejenak untuk berpikir sebelum menjawab
   - Jika tidak tahu, akui dengan jujur dan tawarkan untuk mencari tahu lebih lanjut

---

## 🎓 **KESIMPULAN**

Dokumen ini berisi pertanyaan-pertanyaan umum yang biasanya diajukan oleh dosen penguji saat proposal dan jawabannya berdasarkan proyek SIAKAD. Jawaban disusun berdasarkan:

- Dokumentasi proyek yang ada
- Fitur-fitur yang sudah diimplementasikan
- Teknologi yang digunakan
- Metodologi penelitian
- Hasil pengujian sistem

**Selamat presentasi proposal! Semoga lancar dan sukses! 🎉**

---

**Catatan:** Jawaban dapat disesuaikan dengan kondisi aktual proyek dan kebutuhan presentasi. Pastikan untuk memahami semua aspek sistem sebelum presentasi.

