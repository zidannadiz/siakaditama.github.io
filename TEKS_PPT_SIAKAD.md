# TEKS PPT SIAKAD
## Implementasi Sistem Informasi Akademik (SIAKAD) pada Institut Teknologi Al Mahrusiyah Kota Kediri

---

## SLIDE 1: LATAR BELAKANG PENELITIAN

### Highlight (Sorotan) yang Menjadi Pokok Bahasan:
**Sistem pengelolaan data akademik di Institut Teknologi Al Mahrusiyah masih menggunakan cara manual, menyebabkan proses pengolahan data menjadi lambat, risiko kesalahan input data tinggi, dan kesulitan melakukan pemantauan data akademik secara real-time.**

### Gap (Celah) Penelitian:
**Penelitian terdahulu tentang sistem informasi akademik sebagian besar masih fokus di level sekolah (SD, SMP, SMA, SMK) dan menggunakan teknologi konvensional (PHP MySQL biasa atau CodeIgniter). Belum ada penelitian yang secara spesifik mengimplementasikan sistem informasi akademik berbasis Laravel untuk perguruan tinggi dengan kompleksitas kebutuhan yang lebih tinggi.**

### Ide Peneliti untuk Mengisi Celah Penelitian:
**Perlu dilakukan pengembangan dan implementasi Sistem Informasi Akademik (SIAKAD) berbasis web menggunakan framework Laravel yang terintegrasi untuk mengelola seluruh proses akademik di Institut Teknologi Al Mahrusiyah, meliputi pengelolaan KRS, data mahasiswa, input nilai, dan informasi akademik lainnya.**

---

## SLIDE 2: PENELITIAN TERDAHULU

### Cantumkan Cukup 1 Hasil Penelitian Terdahulu yang Paling Relevan

**Penelitian Terdahulu**

Ambara, M. P., & Antarajaya, I. N. S. (2020). "Pengembangan Sistem Informasi Akademik untuk Mendukung Proses Perkuliahan di Lembaga Pendidikan," Jurnal Teknologi Informasi dan Komputer, 6(2), 112-125. 

Penelitian ini melakukan pengembangan sistem informasi akademik berbasis web menggunakan framework CodeIgniter di Lembaga Pendidikan Bali Asia Denpasar. Penelitian terdahulu berhasil mempermudah proses administrasi akademik, namun masih menggunakan framework yang kurang modern dan belum fokus pada kompleksitas kebutuhan perguruan tinggi.

**Penelitian terdahulu tidak menggunakan Laravel dan belum mengimplementasikan sistem secara langsung di perguruan tinggi dengan kompleksitas kebutuhan yang lebih tinggi.**

### Kritik Terhadap Hasil Penelitian Terdahulu

**Kesamaan dengan Penelitian Sekarang:**
- Sama-sama mengembangkan sistem informasi akademik berbasis web
- Sama-sama bertujuan mempermudah proses administrasi akademik
- Sama-sama menggunakan framework PHP

**Perbedaan dengan Penelitian Sekarang:**
- Penelitian terdahulu menggunakan CodeIgniter, penelitian sekarang menggunakan Laravel (framework yang lebih modern)
- Penelitian terdahulu fokus pada lembaga pendidikan umum, penelitian sekarang fokus pada perguruan tinggi dengan kompleksitas lebih tinggi
- Penelitian sekarang mengimplementasikan sistem secara langsung di Institut Teknologi Al Mahrusiyah dengan fitur yang lebih lengkap (KRS, KHS, presensi, tugas online, dll)

---

## SLIDE 3: KERANGKA BERPIKIR

**Alur logis secara garis besar berjalannya penelitian. Alur logis harus kuat dan sistematis. Kerangka berpikir dapat berupa jawaban sementara atas pertanyaan penelitian.**

### Kerangka Berpikir

**1. Teks Permasalahan**
- Sistem manual dalam pengelolaan data akademik
- Data akademik tersebar dan tidak terintegrasi
- Proses pengolahan data yang lambat
- Tingginya risiko kesalahan input data
- Kesulitan monitoring data akademik secara real-time

**2. Analisis dan Perancangan Sistem**
- Identifikasi kebutuhan fungsional dan non-fungsional
- Perancangan basis data dan arsitektur sistem
- Perancangan antarmuka pengguna (user interface)
- Pemilihan teknologi: Laravel 11, MySQL, Tailwind CSS

**3. Implementasi dan Pengujian**
- Pengembangan aplikasi berbasis web
- Integrasi modul-modul sistem (KRS, KHS, Nilai, Jadwal, dll)
- Pengujian unit, integrasi, dan user acceptance testing

**4. Temuan**
- Sistem informasi akademik berbasis web yang terintegrasi
- Peningkatan efisiensi pengolahan data akademik
- Pengurangan kesalahan input data
- Kemudahan monitoring data akademik secara real-time
- Peningkatan kualitas layanan akademik

**Simpulan atau temuan yang bersifat prediktif bagi penelitian kualitatif. Ujung dari kerangka berpikir adalah hipotesis bagi penelitian kuantitatif.**

---

## SLIDE 4: FORMULA PENELITIAN

**Pertanyaan penelitian, yaitu problem yang belum dijawab oleh penelitian terdahulu**

**Tujuan penelitian: Mengapa penelitian Anda ada?**

### Rumusan Masalah, Tujuan, dan Manfaat Penelitian

#### 1. RUMUSAN MASALAH
**Bagaimana mengembangkan dan mengimplementasikan Sistem Informasi Akademik (SIAKAD) berbasis web menggunakan framework Laravel untuk meningkatkan efisiensi pengelolaan data akademik di Institut Teknologi Al Mahrusiyah Kota Kediri?**

#### 2. TUJUAN PENELITIAN
**Tujuan penelitian ini untuk mengembangkan dan mengimplementasikan Sistem Informasi Akademik (SIAKAD) berbasis web menggunakan framework Laravel yang dapat mengintegrasikan seluruh proses akademik, meliputi pengelolaan KRS, data mahasiswa, input nilai, jadwal kuliah, dan laporan akademik di Institut Teknologi Al Mahrusiyah Kota Kediri.**

#### 3. MANFAAT PENELITIAN
**Manfaat Teoritis:**
- Penguatan metodologi SDLC Waterfall dalam pengembangan sistem informasi akademik
- Kontribusi pada pengembangan ilmu pengetahuan di bidang sistem informasi berbasis web
- Referensi untuk penelitian selanjutnya dalam pengembangan sistem akademik menggunakan Laravel

**Manfaat Praktis:**
- Bagi Institut: Meningkatkan efisiensi operasional, kualitas layanan, dan manajemen data akademik
- Bagi Dosen: Memudahkan input nilai, akses jadwal mengajar, dan manajemen kelas
- Bagi Mahasiswa: Memudahkan pengambilan KRS, akses informasi akademik real-time, dan self-service akademik
- Bagi Staf Administrasi: Mengurangi beban administratif, meningkatkan akurasi data, dan efisiensi kerja

**Bukan hanya manfaat, tetapi urgensi. Juga implikasi general bagi bidang ilmu lain dan manfaat praktis untuk peminat bidang ilmu program studi.**

---

## SLIDE 5: TINJAUAN PUSTAKA DAN METODOLOGI PENELITIAN

**Tinjauan Pustaka (literature review) adalah argumen mencakup analisis terhadap teori keilmuan yang berkembang disertai bukti-bukti yang valid sebagai landasan bagi pokok bahasan penelitian sekarang**

### Tinjauan Pustaka dan Metodologi Penelitian

#### A. TINJAUAN PUSTAKA

**1. Teori Sistem Informasi Akademik**
- Konsep sistem informasi akademik menurut Jogiyanto (2017)
- Fungsi-fungsi utama sistem informasi akademik (Turban, Pollard, & Wood, 2018)
- Kriteria sistem informasi akademik yang baik (Sommerville, 2016)

**2. Teori Aplikasi Web**
- Konsep aplikasi web dan kelebihannya (Sebesta, 2018)
- Arsitektur three-tier dalam aplikasi web
- Framework Laravel dan keunggulannya dalam pengembangan aplikasi web

**3. Metodologi Pengembangan Sistem**
- System Development Life Cycle (SDLC) model Waterfall
- Tahapan pengembangan sistem: Analisis, Perancangan, Implementasi, Pengujian
- Best practices dalam pengembangan sistem informasi berbasis web

#### B. METODE PENELITIAN

**Penelitian ini menggunakan pendekatan kualitatif melalui metode System Development Life Cycle (SDLC) model Waterfall dengan tahapan: (1) Analisis Kebutuhan Sistem, (2) Perancangan Sistem (basis data, arsitektur, antarmuka), (3) Implementasi Sistem menggunakan Laravel 11, MySQL, dan Tailwind CSS, (4) Pengujian Sistem (unit testing, integration testing, user acceptance testing).**

**Metodologi penelitian dilihat dari kesesuaian antara pertanyaan penelitian dan tinjauan pustaka yang akan digunakan untuk mengelola data penelitian**

---

## SLIDE 6: HASIL PENELITIAN DAN PEMBAHASAN

**Hasil pengolahan data apa adanya secara clear (bersih) tanpa interpretasi penulis**

### Hasil Penelitian dan Pembahasan

#### A. HASIL PENELITIAN

**1. Sistem yang Dikembangkan:**
- Sistem Informasi Akademik (SIAKAD) berbasis web menggunakan Laravel 11
- Arsitektur three-tier: Presentation Layer (Blade + Tailwind CSS), Application Layer (Laravel), Data Layer (MySQL)
- Sistem multi-role: Admin, Dosen, dan Mahasiswa dengan role-based access control

**2. Modul yang Diimplementasikan:**
- Modul Master Data (Program Studi, Mahasiswa, Dosen, Mata Kuliah, Semester, Jadwal)
- Modul KRS (Kartu Rencana Studi) dengan sistem approval
- Modul KHS (Kartu Hasil Studi) dan Transkrip Akademik
- Modul Input Nilai oleh Dosen dengan perhitungan IPK otomatis
- Modul Presensi QR Code dan Manual
- Modul Tugas dan Ujian Online dengan anti-cheat system
- Modul Pembayaran terintegrasi Xendit
- Modul Komunikasi (Chat, Forum, Q&A)
- Modul Pengumuman dan Notifikasi

**3. Hasil Pengujian:**
- Sistem berhasil diimplementasikan dan berjalan dengan baik
- Semua modul fungsional dapat diakses sesuai dengan role pengguna
- Sistem dapat mengolah data akademik secara terintegrasi

#### B. HASIL PEMBAHASAN

**1. Sistem Berhasil Mengintegrasikan Proses Akademik:**
- Semua proses akademik yang sebelumnya manual kini terintegrasi dalam satu platform
- Data akademik tersimpan terpusat dalam database, menghindari duplikasi dan inkonsistensi
- Proses pengolahan data menjadi lebih cepat dan efisien

**2. Peningkatan Efisiensi Operasional:**
- Admin dapat mengelola data akademik secara terpusat tanpa harus mencari data dari berbagai tempat
- Dosen dapat input nilai secara online dengan validasi otomatis
- Mahasiswa dapat mengambil KRS dan mengakses informasi akademik kapan saja tanpa harus datang ke kampus

**3. Secara Teknis, Sistem Menggunakan Teknologi Modern:**
- Framework Laravel 11 memberikan struktur kode yang rapi dan keamanan yang lebih baik
- Arsitektur three-tier memungkinkan sistem scalable dan mudah di-maintain
- Integrasi dengan payment gateway Xendit memungkinkan pembayaran online

### Pembahasan (Discussion):

- **Hasil utama penelitian:** Sistem SIAKAD berhasil dikembangkan dan diimplementasikan dengan 70+ fitur yang terintegrasi
- **Analisis terhadap hasil utama dengan menggunakan teori yang tercantum di tinjauan pustaka:** Sistem memenuhi kriteria sistem informasi akademik yang baik menurut Sommerville (2016): user-friendly, scalable, secure, integrated, dan reliable
- **Dialog dengan hasil-hasil penelitian terdahulu:** Berbeda dengan penelitian terdahulu yang fokus di level sekolah, penelitian ini berhasil mengimplementasikan sistem di perguruan tinggi dengan kompleksitas lebih tinggi dan menggunakan teknologi modern (Laravel)
- **Penawaran gagasan:** Sistem dapat dikembangkan lebih lanjut dengan menambahkan modul-modul baru seperti sistem keuangan, perpustakaan, atau integrasi dengan sistem eksternal
- **State of the art: Kebaruan dan orisinalitas:** Penelitian ini memberikan kontribusi baru dalam implementasi sistem informasi akademik menggunakan Laravel 11 di perguruan tinggi dengan fitur-fitur modern seperti presensi QR Code, tugas online dengan anti-cheat, dan integrasi payment gateway

---

## SLIDE 7: SIMPULAN

**Simpulan: Hasil akhir sebagai jawaban atas pertanyaan penelitian**

### Kesimpulan

**1. Natijah (Hasil Akhir):**
Meskipun sistem informasi akademik berbasis web telah banyak dikembangkan, implementasi Sistem Informasi Akademik (SIAKAD) menggunakan framework Laravel 11 di Institut Teknologi Al Mahrusiyah berhasil mengintegrasikan seluruh proses akademik dalam satu platform terpusat, meningkatkan efisiensi operasional, dan memberikan layanan akademik yang lebih baik bagi admin, dosen, dan mahasiswa.

**2. Manfaat Penelitian:**
- **Manfaat teoritis:** Penguatan metodologi SDLC Waterfall dan kontribusi pada pengembangan ilmu pengetahuan di bidang sistem informasi berbasis web
- **Manfaat praktis:** Peningkatan efisiensi pengelolaan data akademik, kemudahan akses informasi akademik, dan peningkatan kualitas layanan bagi semua pengguna sistem

**3. Keterbatasan Penelitian:**
- **a) Ruang lingkup institusi:** Penelitian dibatasi hanya pada Institut Teknologi Al Mahrusiyah, hasil tidak dapat digeneralisasi tanpa kajian ulang
- **b) Ruang lingkup fungsional:** Fokus pada aspek akademik (KRS, nilai, jadwal), tidak mencakup sistem keuangan atau perpustakaan
- **c) Ruang lingkup waktu:** Penelitian mencakup periode pengembangan dan implementasi awal, evaluasi jangka panjang berada di luar lingkup

**4. Rekomendasi:**
- **Untuk pengembangan sistem:** Menambahkan modul-modul baru seperti sistem keuangan, perpustakaan digital, atau sistem manajemen skripsi
- **Untuk penelitian lanjutan:** Melakukan evaluasi jangka panjang terhadap efektivitas sistem, studi komparatif dengan sistem lain, atau pengembangan aplikasi mobile native
- **Untuk institusi lain:** Sistem dapat diadaptasi dengan melakukan analisis kebutuhan spesifik masing-masing institusi

### Simpulan Secara Umum:

- **Hasil akhir (natijah):** Sistem SIAKAD berhasil dikembangkan dan diimplementasikan dengan 70+ fitur terintegrasi
- **Hasil penelitian dan pembahasan singkat:** Sistem meningkatkan efisiensi operasional dan kualitas layanan akademik di Institut Teknologi Al Mahrusiyah
- **Implikasi penelitian:** 
  - **General:** Kontribusi pada pengembangan metodologi pengembangan sistem informasi berbasis web
  - **Spesifik:** Solusi konkret untuk meningkatkan efisiensi pengelolaan akademik di perguruan tinggi
- **Keterbatasan penelitian:** Ruang lingkup institusi, fungsional, dan waktu penelitian
- **Studi lanjut di masa depan:** Evaluasi jangka panjang, pengembangan modul tambahan, dan adaptasi untuk institusi lain
- **Rekomendasi:** Pengembangan sistem lebih lanjut dan penelitian lanjutan untuk meningkatkan efektivitas sistem

---

## SLIDE 8: STRUKTUR PENELITIAN (OPTIONAL - Overview)

### RISET SISTEM INFORMASI

**BAB I PENDAHULUAN**
- Topik Bahasan: Implementasi SIAKAD di ITAMA
- Rumusan Masalah: Bagaimana mengembangkan dan mengimplementasikan SIAKAD?
- Kerangka Berpikir: Alur dari permasalahan → solusi → implementasi → temuan
- Hasil Penelitian Terdahulu: Kajian sistem informasi akademik sebelumnya

**BAB II TINJAUAN PUSTAKA**
- A. Atas/Kepala: Teori Sistem Informasi Akademik
- B. Tengah/Body: Teori Aplikasi Web dan Framework Laravel
- C. Bawah/Kaki: Metodologi Pengembangan Sistem (SDLC Waterfall)

**BAB III METODOLOGI PENELITIAN**
- A. Pendekatan dan Metode: SDLC Waterfall
- B. Jenis Data dan Sumber Data: Data akademik ITAMA
- C. Teknik Pengumpulan Data: Observasi, wawancara, dokumentasi
- D. Teknik Analisis Data: Analisis kebutuhan, perancangan, implementasi, pengujian

**BAB IV HASIL DAN PEMBAHASAN**
- A. Hasil Teori: Sistem yang dikembangkan sesuai dengan teori
- B. Hasil Metode: Implementasi menggunakan SDLC Waterfall
- C. Analisis: Pembahasan hasil dan perbandingan dengan penelitian terdahulu

**BAB V PENUTUP**
- 1. Jawaban Pertanyaan Satu: Sistem berhasil dikembangkan
- 2. Jawaban Pertanyaan Dua: Sistem berhasil diimplementasikan
- 3. Jawaban Pertanyaan Tiga: Sistem meningkatkan efisiensi dan kualitas layanan

---

**SELESAI**

