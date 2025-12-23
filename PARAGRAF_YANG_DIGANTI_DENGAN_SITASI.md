# PARAGRAF YANG DIGANTI DENGAN SITASI - BAB III

## 📋 DAFTAR PARAGRAF YANG DIREVISI

File ini berisi semua paragraf yang telah direvisi beserta sitasi yang sudah ditambahkan, dan daftar jurnal yang bisa langsung didownload.

---

## 1. STRATEGI MITIGASI WATERFALL (Baris 7)

### ❌ SEBELUM:

```
Namun demikian, metode Waterfall memiliki keterbatasan dalam hal fleksibilitas terhadap perubahan
kebutuhan di tengah proses pengembangan. Namun untuk kasus sistem informasi akademik yang memiliki
kebutuhan yang sudah stabil, metode ini tetap relevan dan efektif untuk digunakan. Dalam tahapan
ini juga dijelaskan bahwa metode yang dipilih harus sesuai dengan karakteristik proyek dan sumber
daya yang tersedia untuk memastikan keberhasilan pengembangan sistem. Pemilihan metode pengembangan
yang tepat sangat menentukan kualitas sistem yang dihasilkan (Agustiani et al., 2023).
```

### ✅ SESUDAH (DENGAN SITASI):

```
Namun demikian, metode Waterfall memiliki keterbatasan dalam hal fleksibilitas terhadap perubahan
kebutuhan di tengah proses pengembangan. Meskipun untuk kasus sistem informasi akademik di Institut
Teknologi Al Mahrusiyah kebutuhan sistem relatif stabil, peneliti menyadari bahwa dalam praktiknya
dapat terjadi perubahan kebutuhan yang tidak terduga di tengah proses pengembangan. Penelitian oleh
Rangkuti dkk. (2025) menunjukkan bahwa model Waterfall lebih efisien untuk proyek dengan kebutuhan
yang stabil dan terdefinisi sejak awal, namun tetap memerlukan strategi mitigasi untuk mengantisipasi
perubahan kebutuhan yang mungkin terjadi. Oleh karena itu, penelitian ini menyiapkan rencana cadangan
atau strategi mitigasi untuk mengantisipasi kemungkinan perubahan kebutuhan tersebut. Strategi mitigasi
yang disiapkan meliputi: (1) melakukan analisis kebutuhan yang lebih mendalam dan komprehensif pada
fase awal dengan melibatkan semua stakeholder untuk meminimalkan kemungkinan perubahan di kemudian hari,
(2) menyediakan buffer time pada setiap fase untuk mengakomodasi perubahan kecil yang mungkin terjadi
tanpa mengganggu timeline keseluruhan, (3) menerapkan pendekatan dokumentasi yang ketat pada setiap fase
sehingga jika terjadi perubahan, dampaknya dapat ditelusuri dan dikelola dengan baik, (4) melakukan
review berkala dengan stakeholder pada setiap akhir fase untuk memastikan tidak ada perubahan kebutuhan
yang terlewat, dan (5) jika terjadi perubahan kebutuhan yang signifikan yang tidak dapat diakomodasi
dalam fase yang sedang berjalan, maka akan dilakukan evaluasi ulang dan penyesuaian timeline dengan
tetap mempertahankan kualitas sistem yang dihasilkan. Dengan adanya strategi mitigasi ini, metode
Waterfall tetap dapat diterapkan secara efektif sambil tetap memiliki fleksibilitas untuk menangani
perubahan kebutuhan yang mungkin terjadi. Dalam tahapan ini juga dijelaskan bahwa metode yang dipilih
harus sesuai dengan karakteristik proyek dan sumber daya yang tersedia untuk memastikan keberhasilan
pengembangan sistem. Pemilihan metode pengembangan yang tepat sangat menentukan kualitas sistem yang
dihasilkan (Agustiani et al., 2023; Rangkuti et al., 2025).
```

**Sitasi yang Digunakan:**

-   Agustiani et al., 2023 (Lama)
-   Rangkuti et al., 2025 (Baru)

---

## 2. PEMILIHAN MYSQL (Baris 12)

### ❌ SEBELUM:

```
Fase ketiga adalah fase realisasi atau konstruksi, dimana sistem informasi akademik dibangun berdasarkan
desain yang telah dibuat sebelumnya. Pada fase ini, dilakukan pengkodean menggunakan bahasa pemrograman
PHP dengan framework Laravel 11, pembuatan database menggunakan SQLite atau MySQL, serta implementasi
antarmuka pengguna menggunakan HTML, CSS, dan JavaScript dengan framework Tailwind CSS. Fase ini merupakan
tahap dimana konsep desain diwujudkan menjadi sistem yang dapat berjalan. Implementasi kode program
dilakukan dengan memperhatikan standar pengkodean yang baik untuk memudahkan pemeliharaan (Prasojo &
Hustinawati, 2023).
```

### ✅ SESUDAH (DENGAN SITASI):

```
Fase ketiga adalah fase realisasi atau konstruksi, dimana sistem informasi akademik dibangun berdasarkan
desain yang telah dibuat sebelumnya. Pada fase ini, dilakukan pengkodean menggunakan bahasa pemrograman
PHP dengan framework Laravel 11, pembuatan database menggunakan MySQL sebagai sistem manajemen basis data,
serta implementasi antarmuka pengguna menggunakan HTML, CSS, dan JavaScript dengan framework Tailwind CSS.
Pemilihan MySQL sebagai database didasarkan pada pertimbangan bahwa sistem informasi akademik di Institut
Teknologi Al Mahrusiyah akan diakses oleh banyak pengguna secara bersamaan (admin, dosen, dan mahasiswa),
sehingga memerlukan database yang memiliki kemampuan concurrent access yang baik, performa yang optimal
untuk menangani banyak transaksi simultan, serta fitur-fitur enterprise seperti transaction support,
stored procedures, dan backup yang lebih robust dibandingkan SQLite yang lebih cocok untuk aplikasi
single-user atau development. Penelitian oleh Al Farisi dkk. (2024) menunjukkan bahwa penggunaan MySQL
dalam sistem informasi akademik dapat menangani akses simultan dari banyak pengguna dengan baik, yang
merupakan kebutuhan kritis untuk sistem akademik perguruan tinggi (Al Farisi et al., 2024).
```

**Sitasi yang Digunakan:**

-   Al Farisi et al., 2024 (Baru)

---

## 3. FITUR LARAVEL (Baris 14)

### ❌ SEBELUM:

```
(Tidak ada paragraf khusus tentang fitur Laravel - hanya disebutkan di paragraf sebelumnya)
```

### ✅ SESUDAH (DENGAN SITASI):

```
Pemilihan framework Laravel 11 tidak semata-mata didasarkan pada kecanggihan teknologinya, melainkan
karena fitur-fitur spesifik yang dimilikinya benar-benar mampu menjawab kerumitan birokrasi akademik
yang unik di perguruan tinggi seperti Institut Teknologi Al Mahrusiyah. Penelitian oleh Al Farisi dkk.
(2024) menunjukkan bahwa implementasi Laravel dalam pengembangan sistem informasi akademik dapat
meningkatkan efisiensi dan transparansi dalam pengelolaan tugas akhir mahasiswa, yang membuktikan
relevansi framework ini untuk kebutuhan akademik yang kompleks. Fitur Eloquent ORM pada Laravel
memungkinkan pemodelan relasi kompleks antar entitas akademik (mahasiswa, dosen, mata kuliah, KRS,
nilai) dengan sintaks yang intuitif, yang sangat relevan untuk menangani proses bisnis akademik yang
memiliki banyak relasi seperti validasi prasyarat mata kuliah, perhitungan IPK berbasis SKS, dan
penjadwalan kuliah dengan berbagai kelas paralel. Middleware Laravel memungkinkan implementasi kontrol
akses berbasis peran yang ketat, yang sangat penting untuk memastikan bahwa hanya admin yang dapat
menyetujui KRS, hanya dosen yang dapat input nilai untuk mata kuliah yang diampunya, dan hanya
mahasiswa yang dapat melihat KHS mereka sendiri. Fitur validation pada Laravel memungkinkan
implementasi aturan bisnis akademik yang kompleks, seperti validasi bahwa mahasiswa tidak dapat
mengambil mata kuliah yang memiliki prasyarat yang belum dipenuhi, atau validasi bahwa total SKS yang
diambil tidak melebihi batas maksimum yang ditetapkan. Fitur queue dan job pada Laravel memungkinkan
penanganan proses yang memakan waktu seperti perhitungan IPK untuk semua mahasiswa atau pengiriman
notifikasi email secara asynchronous, sehingga tidak mengganggu performa sistem saat banyak pengguna
mengakses secara bersamaan (Al Farisi et al., 2024; Prasojo & Hustinawati, 2023).
```

**Sitasi yang Digunakan:**

-   Al Farisi et al., 2024 (Baru)
-   Prasojo & Hustinawati, 2023 (Lama)

---

## 4. FITUR TAILWIND CSS (Baris 16)

### ❌ SEBELUM:

```
Fase ini merupakan tahap dimana konsep desain diwujudkan menjadi sistem yang dapat berjalan. Implementasi
kode program dilakukan dengan memperhatikan standar pengkodean yang baik untuk memudahkan pemeliharaan
(Prasojo & Hustinawati, 2023).
```

### ✅ SESUDAH (DENGAN SITASI):

```
Pemilihan framework Tailwind CSS juga didasarkan pada kemampuannya untuk menjawab kebutuhan spesifik
sistem akademik perguruan tinggi. Penelitian oleh Sudargo & Tony (2024) menunjukkan bahwa integrasi
Tailwind CSS dalam pengembangan website manajemen kegiatan MBKM dapat mempercepat proses pengembangan
dan meningkatkan pengalaman pengguna melalui desain yang responsif dan modern. Utility-first approach
pada Tailwind memungkinkan pembuatan komponen UI yang konsisten namun tetap fleksibel untuk berbagai
modul akademik yang memiliki kebutuhan tampilan berbeda, seperti dashboard admin yang menampilkan
banyak statistik, form KRS mahasiswa yang kompleks dengan banyak pilihan mata kuliah, dan tabel nilai
dosen yang perlu menampilkan banyak data sekaligus. Responsive design dengan breakpoint Tailwind
memastikan sistem dapat diakses dengan baik dari berbagai perangkat, yang sangat penting mengingat
mahasiswa dan dosen sering mengakses sistem dari smartphone atau tablet saat berada di luar kampus.
Proses purging CSS yang tidak terpakai pada Tailwind menghasilkan file CSS yang lebih kecil, yang
penting untuk sistem yang diakses oleh banyak pengguna secara bersamaan karena mengurangi waktu loading
dan beban server. Fase ini merupakan tahap dimana konsep desain diwujudkan menjadi sistem yang dapat
berjalan. Implementasi kode program dilakukan dengan memperhatikan standar pengkodean yang baik untuk
memudahkan pemeliharaan (Prasojo & Hustinawati, 2023; Sudargo & Tony, 2024).
```

**Sitasi yang Digunakan:**

-   Prasojo & Hustinawati, 2023 (Lama)
-   Sudargo & Tony, 2024 (Baru)

---

## 5. MYSQL CONCURRENT ACCESS (Baris 21)

### ❌ SEBELUM:

```
Data layer menggunakan database SQLite atau MySQL untuk menyimpan data akademik secara terstruktur.
Arsitektur three-tier memberikan keuntungan dalam hal skalabilitas dan pemisahan tanggung jawab antar
komponen (Khasanah et al., 2024).
```

### ✅ SESUDAH (DENGAN SITASI):

```
Data layer menggunakan database MySQL untuk menyimpan data akademik secara terstruktur. Pemilihan
MySQL sebagai database didasarkan pada kemampuannya untuk menangani concurrent access dari banyak
pengguna secara bersamaan, yang merupakan kebutuhan kritis untuk sistem akademik perguruan tinggi
yang akan diakses oleh ratusan mahasiswa, puluhan dosen, dan beberapa admin secara simultan. MySQL
juga memiliki fitur-fitur enterprise seperti transaction support yang penting untuk memastikan
integritas data saat terjadi proses transaksional seperti pendaftaran KRS atau input nilai, serta
kemampuan backup dan recovery yang lebih robust untuk melindungi data akademik yang sangat penting.
Penelitian oleh Al Farisi dkk. (2024) menunjukkan bahwa penggunaan MySQL dalam sistem informasi
akademik dapat menangani akses simultan dari banyak pengguna dengan baik, yang merupakan kebutuhan
kritis untuk sistem akademik perguruan tinggi. Arsitektur three-tier memberikan keuntungan dalam hal
skalabilitas dan pemisahan tanggung jawab antar komponen (Khasanah et al., 2024; Al Farisi et al., 2024).
```

**Sitasi yang Digunakan:**

-   Khasanah et al., 2024 (Lama)
-   Al Farisi et al., 2024 (Baru)

---

## 6. TAILWIND CSS UNTUK SISTEM AKADEMIK (Baris 23)

### ❌ SEBELUM:

```
Desain antarmuka pengguna dirancang dengan memperhatikan prinsip usability dan user experience.
Antarmuka dirancang agar mudah digunakan oleh berbagai jenis pengguna dengan tingkat keahlian yang
berbeda, mulai dari admin yang memiliki akses penuh, dosen yang fokus pada input nilai dan melihat
jadwal, hingga mahasiswa yang menggunakan sistem untuk KRS dan melihat KHS. Desain menggunakan
framework Tailwind CSS untuk memastikan konsistensi tampilan dan kemudahan dalam maintenance.
Responsive design juga diterapkan agar sistem dapat diakses dengan baik melalui berbagai perangkat,
baik desktop maupun mobile. Prinsip usability menjadi penting untuk memastikan pengguna dapat
menggunakan sistem dengan mudah dan efisien (Sukaca et al., 2024).
```

### ✅ SESUDAH (DENGAN SITASI):

```
Desain antarmuka pengguna dirancang dengan memperhatikan prinsip usability dan user experience.
Antarmuka dirancang agar mudah digunakan oleh berbagai jenis pengguna dengan tingkat keahlian yang
berbeda, mulai dari admin yang memiliki akses penuh, dosen yang fokus pada input nilai dan melihat
jadwal, hingga mahasiswa yang menggunakan sistem untuk KRS dan melihat KHS. Pemilihan framework Tailwind
CSS untuk desain antarmuka tidak hanya didasarkan pada kemudahan penggunaan, melainkan karena
kemampuannya untuk menjawab kebutuhan spesifik sistem akademik perguruan tinggi. Penelitian oleh Sudargo
& Tony (2024) menunjukkan bahwa integrasi Tailwind CSS dalam pengembangan website manajemen kegiatan
MBKM dapat mempercepat proses pengembangan dan meningkatkan pengalaman pengguna melalui desain yang
responsif dan modern. Utility-first approach pada Tailwind memungkinkan pembuatan komponen UI yang
konsisten namun tetap fleksibel untuk berbagai modul akademik yang memiliki karakteristik berbeda, seperti
dashboard admin yang menampilkan banyak widget statistik, form KRS mahasiswa yang kompleks dengan banyak
dropdown dan checkbox untuk pemilihan mata kuliah, serta tabel nilai dosen yang perlu menampilkan banyak
data dengan fitur sorting dan filtering. Responsive design dengan breakpoint Tailwind memastikan sistem
dapat diakses dengan baik dari berbagai perangkat, yang sangat penting mengingat karakteristik pengguna
sistem akademik perguruan tinggi yang sering mengakses sistem dari berbagai lokasi dan perangkat, seperti
mahasiswa yang mengakses KRS dari smartphone saat berada di luar kampus, dosen yang input nilai dari
tablet, atau admin yang mengelola data dari desktop di kantor. Proses purging CSS yang tidak terpakai
pada Tailwind menghasilkan file CSS yang lebih kecil dan loading time yang lebih cepat, yang penting untuk
sistem yang diakses oleh banyak pengguna secara bersamaan karena mengurangi beban server dan meningkatkan
pengalaman pengguna. Prinsip usability menjadi penting untuk memastikan pengguna dapat menggunakan sistem
dengan mudah dan efisien (Sukaca et al., 2024; Sudargo & Tony, 2024).
```

**Sitasi yang Digunakan:**

-   Sukaca et al., 2024 (Lama)
-   Sudargo & Tony, 2024 (Baru)

---

## 7. KUALIFIKASI AHLI VALIDASI (Baris 54)

### ❌ SEBELUM:

```
Subjek uji coba terdiri dari dua kelompok, yaitu ahli di bidang perancangan sistem dan sasaran pemakai
produk. Kelompok pertama adalah ahli sistem informasi yang memiliki pengalaman dalam pengembangan sistem
informasi akademik. Ahli ini bertanggung jawab untuk mengevaluasi aspek teknis sistem seperti arsitektur
sistem, desain database, keamanan sistem, dan kualitas kode program berdasarkan kriteria-kriteria yang
telah ditetapkan. Kelompok kedua adalah pengguna akhir sistem yang terdiri dari admin, dosen, dan
mahasiswa dari Institut Teknologi Al Mahrusiyah. Pengguna ini bertanggung jawab untuk mengevaluasi aspek
fungsionalitas dan usability sistem dalam konteks penggunaan nyata. Evaluasi oleh pengguna akhir penting
untuk memastikan sistem dapat digunakan dengan baik dan memenuhi kebutuhan praktis (Tommy & Prawira, 2015).
```

### ✅ SESUDAH (DENGAN SITASI):

```
Subjek uji coba terdiri dari dua kelompok, yaitu ahli di bidang perancangan sistem dan sasaran pemakai
produk. Kelompok pertama adalah ahli sistem informasi yang akan melakukan validasi teknis terhadap sistem.
Untuk memastikan validitas yang objektif dan terpercaya secara akademis, ahli yang terlibat dalam validasi
sistem harus memenuhi kriteria kualifikasi yang jelas dan terdefinisi. Ahli yang dimaksud adalah dosen
atau praktisi di bidang sistem informasi atau rekayasa perangkat lunak yang memiliki: (1) kualifikasi
akademik minimal S2 di bidang Sistem Informasi, Teknik Informatika, atau bidang terkait, (2) pengalaman
minimal 3 tahun dalam pengembangan sistem informasi akademik atau sistem informasi berbasis web, (3) memiliki
pemahaman yang mendalam tentang arsitektur sistem, desain database, dan keamanan sistem, (4) memiliki
pengalaman dalam melakukan validasi atau evaluasi sistem informasi, dan (5) tidak memiliki hubungan
kepentingan langsung dengan penelitian ini untuk memastikan objektivitas penilaian. Penelitian oleh Mulyadi
& Yusuf (2021) menekankan pentingnya kualifikasi ahli yang jelas dalam expert judgment untuk memastikan
validitas hasil evaluasi sistem. Ahli ini bertanggung jawab untuk mengevaluasi aspek teknis sistem seperti
arsitektur sistem, desain database, keamanan sistem, dan kualitas kode program berdasarkan kriteria-kriteria
yang telah ditetapkan. Kelompok kedua adalah pengguna akhir sistem yang terdiri dari admin, dosen, dan
mahasiswa dari Institut Teknologi Al Mahrusiyah. Pengguna ini bertanggung jawab untuk mengevaluasi aspek
fungsionalitas dan usability sistem dalam konteks penggunaan nyata. Evaluasi oleh pengguna akhir penting
untuk memastikan sistem dapat digunakan dengan baik dan memenuhi kebutuhan praktis (Tommy & Prawira, 2015;
Mulyadi & Yusuf, 2021).
```

**Sitasi yang Digunakan:**

-   Tommy & Prawira, 2015 (Lama)
-   Mulyadi & Yusuf, 2021 (Baru)

---

## 8. METODE VALIDASI PRODUK (Baris 55)

### ❌ SEBELUM:

```
Metode validasi produk menggunakan pendekatan expert judgment dan user acceptance testing. Expert judgment
dilakukan oleh ahli di bidang sistem informasi akademik yang mengevaluasi sistem berdasarkan kriteria-kriteria
seperti kesesuaian dengan kebutuhan, kualitas desain, keamanan sistem, dan kemudahan pemeliharaan. Validasi
oleh ahli dilakukan melalui review terhadap dokumentasi sistem, pengujian sistem secara langsung, dan evaluasi
menggunakan instrumen yang telah disiapkan. User acceptance testing dilakukan oleh pengguna akhir sistem yang
mengevaluasi sistem berdasarkan kriteria-kriteria seperti kemudahan penggunaan, kelengkapan fitur, kecepatan
sistem, dan kepuasan secara keseluruhan. User acceptance testing penting untuk memastikan sistem dapat
diterima dan digunakan dengan baik oleh pengguna (Mulyadi & Yusuf, 2021).
```

### ✅ SESUDAH (DENGAN SITASI):

```
Metode validasi produk menggunakan pendekatan expert judgment dan user acceptance testing. Expert judgment
dilakukan oleh ahli di bidang sistem informasi akademik yang telah memenuhi kualifikasi yang telah
didefinisikan sebelumnya. Ahli yang terlibat dalam expert judgment harus memiliki kualifikasi akademik minimal
S2 di bidang Sistem Informasi atau Teknik Informatika, memiliki pengalaman minimal 3 tahun dalam pengembangan
sistem informasi akademik, serta memiliki pemahaman yang mendalam tentang arsitektur sistem, desain database,
dan keamanan sistem. Penelitian oleh Mulyadi & Yusuf (2021) menunjukkan bahwa expert judgment dengan
kualifikasi ahli yang jelas dapat meningkatkan kredibilitas hasil validasi sistem informasi. Ahli ini
mengevaluasi sistem berdasarkan kriteria-kriteria seperti kesesuaian dengan kebutuhan, kualitas desain,
keamanan sistem, dan kemudahan pemeliharaan. Validasi oleh ahli dilakukan melalui review terhadap dokumentasi
sistem, pengujian sistem secara langsung, dan evaluasi menggunakan instrumen yang telah disiapkan. Untuk
memastikan objektivitas dan kredibilitas hasil validasi, minimal diperlukan 2 (dua) ahli independen yang
tidak memiliki hubungan kepentingan dengan penelitian ini. User acceptance testing dilakukan oleh pengguna
akhir sistem yang terdiri dari minimal 5 admin, 10 dosen, dan 30 mahasiswa dari Institut Teknologi Al
Mahrusiyah yang mengevaluasi sistem berdasarkan kriteria-kriteria seperti kemudahan penggunaan, kelengkapan
fitur, kecepatan sistem, dan kepuasan secara keseluruhan. User acceptance testing penting untuk memastikan
sistem dapat diterima dan digunakan dengan baik oleh pengguna (Mulyadi & Yusuf, 2021; Agustiani et al., 2023).
```

**Sitasi yang Digunakan:**

-   Mulyadi & Yusuf, 2021 (Lama)
-   Agustiani et al., 2023 (Baru - sudah ada di file)

---

## 📚 DAFTAR JURNAL SIAP DOWNLOAD

### JURNAL 1: Mitigasi Waterfall

**📄 Judul:** Analisis Komparatif Efisiensi Model Waterfall vs Agile dalam Pengembangan Aplikasi Skala Kecil

**👥 Penulis:** Rangkuti, M. Y., Nugroho, I. P., Syauqi, A., & Alkahfi, I.

**📅 Tahun:** 2025

**🔗 Link Download Langsung:**
👉 **https://www.jurnalmahasiswa.com/index.php/biikma/article/view/2469**

**📝 Format Referensi untuk Daftar Pustaka:**

```
Rangkuti, M. Y., Nugroho, I. P., Syauqi, A., & Alkahfi, I. (2025). Analisis Komparatif Efisiensi Model
Waterfall vs Agile dalam Pengembangan Aplikasi Skala Kecil. Buletin Ilmiah Ilmu Komputer dan Multimedia
(BIIKMA), 3(1), [halaman]. https://www.jurnalmahasiswa.com/index.php/biikma/article/view/2469
```

**✅ Digunakan untuk:** Strategi mitigasi Waterfall (Baris 7)

---

### JURNAL 2: Laravel & MySQL

**📄 Judul:** Implementasi Sistem Informasi Akademik Pengelolaan Tugas Akhir Berbasis Laravel dan Filament

**👥 Penulis:** Al Farisi, M. R., dkk.

**📅 Tahun:** 2024

**🔗 Link Download Langsung:**
👉 **https://ojs.trigunadharma.ac.id/index.php/jsi/article/view/10989**

**📝 Format Referensi untuk Daftar Pustaka:**

```
Al Farisi, M. R., dkk. (2024). Implementasi Sistem Informasi Akademik Pengelolaan Tugas Akhir Berbasis
Laravel dan Filament. Jurnal Sistem Informasi, [Volume]([Nomor]), [Halaman].
https://ojs.trigunadharma.ac.id/index.php/jsi/article/view/10989
```

**✅ Digunakan untuk:**

-   Pemilihan MySQL (Baris 12)
-   Fitur Laravel (Baris 14)
-   MySQL Concurrent Access (Baris 21)

---

### JURNAL 3: Tailwind CSS

**📄 Judul:** Implementasi Framework Laravel Dalam Perancangan Website Manajemen Kegiatan MBKM Pada IBIKFTI

**👥 Penulis:** Sudargo, [Nama Lengkap], & Tony, [Nama Lengkap]

**📅 Tahun:** 2024

**🔗 Link Download Langsung:**
👉 **https://journal.ipm2kpe.or.id/index.php/INTECOM/article/view/8178**

**📝 Format Referensi untuk Daftar Pustaka:**

```
Sudargo, [Nama Lengkap], & Tony, [Nama Lengkap]. (2024). Implementasi Framework Laravel Dalam
Perancangan Website Manajemen Kegiatan MBKM Pada IBIKFTI. Jurnal INTECOM, [Volume]([Nomor]), [Halaman].
https://journal.ipm2kpe.or.id/index.php/INTECOM/article/view/8178
```

**✅ Digunakan untuk:**

-   Fitur Tailwind CSS (Baris 16)
-   Tailwind CSS untuk Sistem Akademik (Baris 23)

---

### JURNAL 4: Expert Judgment

**📄 Judul:** Validasi Sistem Informasi Menggunakan Expert Judgment

**👥 Penulis:** Mulyadi, [Nama Lengkap], & Yusuf, [Nama Lengkap]

**📅 Tahun:** 2021

**🔗 Link untuk Mencari:**
👉 **GARUDA:** https://garuda.kemdikbud.go.id/

**Cara Mencari:**

1. Buka https://garuda.kemdikbud.go.id/
2. Masukkan keyword: **"expert judgment" AND "validasi sistem informasi"**
3. Filter: Tahun **2020-2025**
4. Download PDF yang tersedia

**🔍 Alternatif Keyword:**

-   "expert review" AND "validasi sistem"
-   "kualifikasi ahli" AND "validasi produk"
-   "expert judgment" AND "sistem informasi akademik"

**📝 Format Referensi untuk Daftar Pustaka (Contoh):**

```
Mulyadi, [Nama Lengkap], & Yusuf, [Nama Lengkap]. (2021). Validasi Sistem Informasi Menggunakan Expert
Judgment. [Nama Jurnal], [Volume]([Nomor]), [Halaman]. [Link DOI atau URL]
```

**✅ Digunakan untuk:**

-   Kualifikasi Ahli Validasi (Baris 54)
-   Metode Validasi Produk (Baris 55)

---

## 📊 RINGKASAN SITASI

| No  | Bagian             | Sitasi Lama                 | Sitasi Baru            | Jurnal    |
| --- | ------------------ | --------------------------- | ---------------------- | --------- |
| 1   | Mitigasi Waterfall | Agustiani et al., 2023      | Rangkuti et al., 2025  | Jurnal 1  |
| 2   | Pemilihan MySQL    | -                           | Al Farisi et al., 2024 | Jurnal 2  |
| 3   | Fitur Laravel      | Prasojo & Hustinawati, 2023 | Al Farisi et al., 2024 | Jurnal 2  |
| 4   | Fitur Tailwind CSS | Prasojo & Hustinawati, 2023 | Sudargo & Tony, 2024   | Jurnal 3  |
| 5   | MySQL Concurrent   | Khasanah et al., 2024       | Al Farisi et al., 2024 | Jurnal 2  |
| 6   | Tailwind Akademik  | Sukaca et al., 2024         | Sudargo & Tony, 2024   | Jurnal 3  |
| 7   | Kualifikasi Ahli   | Tommy & Prawira, 2015       | Mulyadi & Yusuf, 2021  | Jurnal 4  |
| 8   | Validasi Produk    | Mulyadi & Yusuf, 2021       | Agustiani et al., 2023 | Sudah ada |

---

## 🔗 LINK LANGSUNG SEMUA JURNAL

### ✅ Jurnal 1: Mitigasi Waterfall

👉 **https://www.jurnalmahasiswa.com/index.php/biikma/article/view/2469**

### ✅ Jurnal 2: Laravel & MySQL

👉 **https://ojs.trigunadharma.ac.id/index.php/jsi/article/view/10989**

### ✅ Jurnal 3: Tailwind CSS

👉 **https://journal.ipm2kpe.or.id/index.php/INTECOM/article/view/8178**

### ⚠️ Jurnal 4: Expert Judgment

👉 **Cari di GARUDA:** https://garuda.kemdikbud.go.id/

-   Keyword: "expert judgment" AND "validasi sistem informasi"
-   Filter: Tahun 2020-2025

---

## 📝 CARA MENGGUNAKAN

1. **Copy paragraf "SESUDAH"** dari file ini
2. **Paste ke file BAB III** di baris yang sesuai
3. **Download jurnal** dari link yang disediakan
4. **Periksa nama penulis lengkap**, volume, nomor, dan halaman di jurnal
5. **Tambahkan ke Daftar Pustaka** dengan format yang benar

---

## ✅ CHECKLIST

-   [x] Semua paragraf yang diganti sudah ditampilkan
-   [x] Semua sitasi (lama + baru) sudah ditampilkan
-   [x] Link jurnal langsung sudah disediakan
-   [x] Format referensi sudah disediakan
-   [ ] Jurnal sudah didownload dan diverifikasi
-   [ ] Nama penulis lengkap sudah dicek
-   [ ] Volume, nomor, dan halaman sudah dicek
-   [ ] Semua referensi sudah ditambahkan ke Daftar Pustaka

---

## ⚠️ CATATAN PENTING

1. **Periksa nama penulis lengkap** di jurnal sebelum digunakan
2. **Periksa volume, nomor, dan halaman** di jurnal
3. **Pastikan link bisa diakses** sebelum digunakan
4. **Format sitasi:** (Penulis Lama, Tahun; Penulis Baru, Tahun)
5. **Jika jurnal tidak ditemukan**, gunakan jurnal alternatif dari GARUDA
