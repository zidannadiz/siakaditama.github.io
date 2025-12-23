# DAFTAR BAGIAN YANG DIGANTI DALAM BAB II

## Ringkasan Perubahan
File: `BAB II Implementasi Sistem Informasi Akademik.MD`

**Total bagian yang diganti: 6 bagian utama**

---

## 1. Framework Laravel (Baris 19-20)

### Sebelum:
```
Laravel adalah kerangka pengembangan aplikasi web yang ditandai dengan sintaksisnya yang elegan dan ekspresif. Kerangka kerja online menawarkan struktur dan dasar untuk pengembangan aplikasi. (Laravel, n.d.). Dalam konteks sistem informasi akademik, Laravel memberikan keunggulan strategis untuk mengelola kompleksitas data relasional perguruan tinggi. Arsitektur MVC Laravel memungkinkan pemisahan logika bisnis akademik (seperti validasi KRS, perhitungan IPK, dan penjadwalan kuliah) dari layer presentasi, sehingga memudahkan maintenance dan pengembangan fitur baru. Sistem routing Laravel yang fleksibel memungkinkan pembuatan endpoint yang terstruktur untuk berbagai modul akademik seperti manajemen mahasiswa, pengelolaan nilai, dan laporan transkrip. Middleware Laravel sangat relevan untuk implementasi kontrol akses berbasis peran (admin, dosen, mahasiswa) yang merupakan kebutuhan kritis dalam sistem akademik perguruan tinggi.
```

### Sesudah:
```
Laravel merupakan framework pengembangan aplikasi web berbasis PHP yang memiliki sintaks yang elegan dan mudah dipahami. Framework ini menyediakan struktur dan fondasi yang solid untuk membangun aplikasi web modern. (Laravel, n.d.). Dalam penerapannya pada sistem informasi akademik, Laravel menawarkan berbagai keunggulan untuk menangani kompleksitas data relasional yang ada di lingkungan perguruan tinggi. Penerapan arsitektur Model-View-Controller (MVC) pada Laravel memungkinkan pemisahan yang jelas antara logika bisnis akademik—seperti proses validasi Kartu Rencana Studi (KRS), kalkulasi Indeks Prestasi Kumulatif (IPK), dan pengaturan jadwal perkuliahan—dengan layer presentasi atau tampilan. Pemisahan ini memberikan kemudahan dalam melakukan perawatan sistem dan pengembangan fitur-fitur tambahan di masa mendatang. Sistem routing yang dimiliki Laravel bersifat fleksibel dan memungkinkan pengembang untuk membuat endpoint yang terorganisir untuk berbagai modul akademik, mulai dari pengelolaan data mahasiswa, penginputan nilai, hingga pembuatan laporan transkrip akademik. Fitur middleware pada Laravel memiliki peran penting dalam implementasi sistem kontrol akses berdasarkan peran pengguna (role-based access control), seperti admin, dosen, dan mahasiswa, yang merupakan kebutuhan fundamental dalam sistem informasi akademik perguruan tinggi. Penelitian oleh Al Farisi dkk. (2025) menunjukkan bahwa implementasi Laravel dalam pengembangan sistem informasi akademik dapat meningkatkan efisiensi dan transparansi dalam pengelolaan tugas akhir mahasiswa, yang membuktikan relevansi framework ini untuk kebutuhan akademik yang kompleks.
```

**Sitasi ditambahkan:** Al Farisi dkk. (2025)

---

## 2. Eloquent ORM (Baris 42-44)

### Sebelum:
```
Eloquent adalah komponen Object-Relational Mapping (ORM) yang terintegrasi dalam kerangka Laravel untuk memfasilitasi interaksi database melalui pendekatan berorientasi objek. Pendekatan ORM memungkinkan pengembang aplikasi online untuk melakukan operasi basis data melalui model PHP tanpa perlu menulis kueri SQL secara langsung. Hal ini meningkatkan efisiensi proses pengembangan dan menjadikan kode lebih mudah dipahami dan dipelihara. (Stauffer, 2020). 
Dalam sistem informasi akademik, Eloquent ORM memainkan peran krusial dalam memodelkan hubungan kompleks antar entitas akademik. Sebagai contoh, model Mahasiswa dapat memiliki relasi hasMany dengan model KRS (satu mahasiswa memiliki banyak KRS), relasi belongsToMany dengan model MataKuliah melalui tabel pivot (mahasiswa mengambil banyak mata kuliah), dan relasi hasMany dengan model Nilai. Model Dosen dapat memiliki relasi hasMany dengan model JadwalKuliah dan relasi hasMany dengan model Nilai untuk mata kuliah yang diampu. Model MataKuliah dapat memiliki relasi belongsTo dengan model ProgramStudi dan relasi hasMany dengan model JadwalKuliah. Eloquent memungkinkan pengambilan data terkait secara efisien menggunakan eager loading, misalnya saat menampilkan transkrip mahasiswa, sistem dapat mengambil data mahasiswa beserta semua KRS, nilai, dan mata kuliah dalam satu query yang optimal, menghindari masalah N+1 query yang dapat memperlambat sistem. Fitur mutator dan accessor Eloquent sangat berguna untuk memformat data akademik, seperti mengubah format tanggal lahir mahasiswa atau menghitung IPK secara otomatis saat data diakses. Mass assignment protection dalam Eloquent memastikan bahwa hanya field yang diizinkan yang dapat diisi saat proses pendaftaran KRS atau input nilai, mencegah manipulasi data yang tidak sah.
```

### Sesudah:
```
Eloquent merupakan komponen Object-Relational Mapping (ORM) yang menjadi bagian integral dari framework Laravel. Komponen ini memfasilitasi interaksi dengan database menggunakan pendekatan pemrograman berorientasi objek. Melalui pendekatan ORM ini, pengembang dapat melakukan berbagai operasi database menggunakan model PHP tanpa harus menuliskan query SQL secara manual. Pendekatan ini tidak hanya mempercepat proses pengembangan, tetapi juga membuat kode menjadi lebih mudah dipahami dan dirawat. (Stauffer, 2020). 
Dalam konteks sistem informasi akademik, Eloquent ORM memiliki peran yang sangat penting dalam memodelkan relasi kompleks yang terjadi antar berbagai entitas akademik. Sebagai ilustrasi, model Mahasiswa dapat memiliki relasi hasMany dengan model KRS, yang berarti satu mahasiswa dapat memiliki banyak Kartu Rencana Studi sepanjang masa studinya. Selain itu, model Mahasiswa juga dapat memiliki relasi belongsToMany dengan model MataKuliah melalui tabel pivot, yang menggambarkan bahwa seorang mahasiswa dapat mengambil banyak mata kuliah, dan sebaliknya satu mata kuliah dapat diambil oleh banyak mahasiswa. Model Mahasiswa juga memiliki relasi hasMany dengan model Nilai untuk menyimpan berbagai nilai yang diperoleh mahasiswa tersebut. Di sisi lain, model Dosen dapat memiliki relasi hasMany dengan model JadwalKuliah untuk mencatat berbagai jadwal mengajar yang dimiliki dosen, serta relasi hasMany dengan model Nilai untuk mata kuliah yang diampu oleh dosen tersebut. Model MataKuliah dapat memiliki relasi belongsTo dengan model ProgramStudi, menunjukkan bahwa setiap mata kuliah berada di bawah naungan program studi tertentu, dan relasi hasMany dengan model JadwalKuliah untuk mencatat berbagai jadwal yang tersedia untuk mata kuliah tersebut. Eloquent menyediakan fitur eager loading yang memungkinkan pengambilan data terkait secara efisien dalam satu query, misalnya ketika sistem perlu menampilkan transkrip mahasiswa, semua data terkait seperti KRS, nilai, dan informasi mata kuliah dapat diambil sekaligus, sehingga menghindari masalah N+1 query yang dapat menurunkan performa sistem. Fitur mutator dan accessor pada Eloquent sangat membantu dalam memformat data akademik, seperti mengubah format tanggal lahir mahasiswa menjadi format yang lebih mudah dibaca, atau melakukan perhitungan IPK secara otomatis setiap kali data mahasiswa diakses. Fitur mass assignment protection pada Eloquent berfungsi untuk memastikan bahwa hanya field-field yang telah diizinkan yang dapat diisi saat proses pendaftaran KRS atau penginputan nilai, sehingga mencegah terjadinya manipulasi data yang tidak sah oleh pengguna yang tidak berwenang.
```

**Tidak ada sitasi baru ditambahkan** (masih menggunakan Stauffer, 2020)

---

## 3. Blade Templating Engine (Baris 45-47)

### Sebelum:
```
Blade adalah mesin templating bawaan Laravel. Ini dirancang untuk memisahkan logika presentasi dari logika bisnis aplikasi. Template engine ini memiliki sintaks yang lebih ekspresif dan kuat dibandingkan dengan PHP konvensional. Ini juga mendukung fitur-fitur seperti pewarisan template, bagian, komponen, dan berbagai arahan khusus. (Stauffer, 2020).
Dalam sistem informasi akademik, Blade templating memungkinkan pembuatan antarmuka yang konsisten dan efisien untuk berbagai modul akademik. Fitur pewarisan template sangat efektif untuk membuat layout dasar (seperti header, sidebar navigasi, dan footer) yang dapat digunakan kembali oleh semua halaman akademik—mulai dari dashboard admin, halaman KRS mahasiswa, hingga form input nilai dosen. Komponen Blade yang dapat digunakan kembali sangat berguna untuk membuat komponen UI yang konsisten, seperti komponen kartu informasi mahasiswa yang dapat digunakan di halaman profil, daftar mahasiswa, dan laporan. Blade directives seperti @auth dan @can memungkinkan kontrol akses berbasis peran di level view, misalnya menampilkan tombol "Input Nilai" hanya untuk dosen yang memiliki izin, atau menampilkan menu "Kelola KRS" hanya untuk admin. Fitur @include memungkinkan modularisasi bagian-bagian view yang sering digunakan, seperti form pencarian mahasiswa atau tabel daftar mata kuliah, sehingga dapat digunakan di berbagai halaman tanpa duplikasi kode. Keamanan Blade dengan automatic escaping sangat penting untuk mencegah XSS attack pada data yang ditampilkan dari database, seperti nama mahasiswa, judul mata kuliah, atau komentar dalam pengumuman akademik. Blade juga mendukung conditional rendering yang efisien untuk menampilkan informasi berbeda berdasarkan status akademik, misalnya menampilkan badge "Lulus" atau "Tidak Lulus" berdasarkan nilai akhir mahasiswa.
```

### Sesudah:
```
Blade merupakan mesin templating yang menjadi bagian default dari framework Laravel. Mesin ini dirancang khusus untuk memisahkan logika presentasi atau tampilan dari logika bisnis yang ada dalam aplikasi. Dibandingkan dengan PHP konvensional, Blade memiliki sintaks yang lebih ekspresif dan memberikan kemampuan yang lebih kuat dalam pengembangan antarmuka pengguna. Blade juga dilengkapi dengan berbagai fitur canggih seperti pewarisan template, bagian-bagian template yang dapat digunakan kembali, komponen yang dapat di-reuse, serta berbagai direktif khusus yang memudahkan pengembangan. (Stauffer, 2020).
Dalam implementasinya pada sistem informasi akademik, Blade templating memberikan kemudahan dalam menciptakan antarmuka pengguna yang konsisten dan efisien untuk berbagai modul akademik yang ada. Fitur pewarisan template yang dimiliki Blade sangat efektif untuk membuat layout dasar yang dapat digunakan berulang kali, seperti header, sidebar navigasi, dan footer. Layout dasar ini kemudian dapat di-extend oleh semua halaman akademik, mulai dari dashboard administrator, halaman pengisian KRS oleh mahasiswa, hingga form penginputan nilai oleh dosen. Komponen Blade yang dapat digunakan kembali sangat membantu dalam menciptakan komponen antarmuka pengguna yang konsisten, seperti komponen kartu informasi mahasiswa yang dapat digunakan di berbagai tempat seperti halaman profil mahasiswa, daftar mahasiswa, dan berbagai laporan akademik. Direktif Blade seperti @auth dan @can memungkinkan implementasi kontrol akses berbasis peran langsung di level view, contohnya sistem dapat menampilkan tombol "Input Nilai" hanya kepada dosen yang memiliki izin untuk melakukan aksi tersebut, atau menampilkan menu "Kelola KRS" hanya kepada administrator. Fitur @include pada Blade memungkinkan modularisasi bagian-bagian view yang sering digunakan, seperti form pencarian mahasiswa atau tabel daftar mata kuliah, sehingga komponen-komponen ini dapat digunakan di berbagai halaman tanpa perlu menulis ulang kode yang sama. Aspek keamanan pada Blade dengan fitur automatic escaping memiliki peran yang sangat penting untuk mencegah serangan Cross-Site Scripting (XSS) pada data yang ditampilkan dari database, seperti nama mahasiswa, judul mata kuliah, atau komentar dalam pengumuman akademik. Blade juga mendukung conditional rendering yang efisien untuk menampilkan informasi yang berbeda berdasarkan status akademik pengguna, misalnya menampilkan badge "Lulus" atau "Tidak Lulus" berdasarkan nilai akhir yang diperoleh mahasiswa.
```

**Tidak ada sitasi baru ditambahkan** (masih menggunakan Stauffer, 2020)

---

## 4. Tailwind CSS (Baris 48-50)

### Sebelum:
```
Tailwind CSS adalah kerangka kerja CSS yang mengutamakan utilitas yang menyediakan kelas utilitas untuk membuat desain khusus dengan cepat dan mudah. Tailwind CSS berbeda dari kerangka kerja CSS konvensional seperti Bootstrap karena tekanan pada kelas utilitas yang dapat dikombinasikan dan disesuaikan dengan berbagai cara untuk menciptakan desain yang unik dan sesuai dengan kebutuhan Anda. (Azhariyah & Mukhlis, 2023).
Dalam konteks sistem informasi akademik, Tailwind CSS memberikan solusi desain yang efisien untuk berbagai modul dengan kebutuhan tampilan yang berbeda. Kelas utilitas Tailwind memungkinkan pembuatan komponen UI yang konsisten namun fleksibel, seperti tabel daftar mahasiswa dengan styling yang seragam (bg-white, border, shadow-md) yang dapat dengan mudah disesuaikan untuk tabel KRS, tabel nilai, atau tabel jadwal kuliah. Responsive design dengan breakpoint Tailwind (sm:, md:, lg:) sangat penting untuk memastikan sistem dapat diakses dengan baik dari berbagai perangkat—mahasiswa dapat mengakses KRS dari smartphone, dosen dapat input nilai dari tablet, dan admin dapat mengelola data dari desktop. Sistem grid Tailwind (grid-cols-1 md:grid-cols-2 lg:grid-cols-3) memungkinkan layout dashboard yang adaptif, menampilkan statistik akademik (jumlah mahasiswa, jumlah mata kuliah, jumlah dosen) dalam grid yang responsif. Utility classes untuk spacing (p-4, m-2, gap-6) memastikan konsistensi jarak antar elemen di seluruh modul akademik, dari form pendaftaran KRS hingga halaman transkrip. Tailwind juga menyediakan kelas untuk state management (hover:, focus:, active:) yang meningkatkan UX, seperti tombol "Setujui KRS" yang berubah warna saat di-hover, atau input field yang memiliki border yang lebih jelas saat focus. Proses purging CSS yang tidak terpakai sangat menguntungkan untuk sistem akademik yang memiliki banyak halaman, karena hanya CSS yang benar-benar digunakan yang akan di-include dalam build final, menghasilkan file CSS yang lebih kecil dan loading time yang lebih cepat—faktor penting untuk sistem yang diakses oleh banyak pengguna secara bersamaan. Tailwind CSS versi 4 dengan variabel CSS memungkinkan kustomisasi tema yang mudah, seperti mengubah warna primary dari biru menjadi hijau untuk menyesuaikan dengan identitas visual Institut Teknologi Al Mahrusiyah, tanpa perlu mengubah setiap komponen secara manual.
```

### Sesudah:
```
Tailwind CSS merupakan framework CSS yang mengadopsi pendekatan utility-first, di mana framework ini menyediakan berbagai kelas utilitas yang dapat digunakan untuk membuat desain khusus dengan cepat dan mudah. Perbedaan utama Tailwind CSS dengan framework CSS konvensional seperti Bootstrap terletak pada fokusnya terhadap kelas-kelas utilitas yang dapat dikombinasikan dan disesuaikan dengan berbagai cara untuk menghasilkan desain yang unik dan sesuai dengan kebutuhan spesifik pengguna. (Azhariyah & Mukhlis, 2023).
Dalam penerapannya pada sistem informasi akademik, Tailwind CSS menawarkan solusi desain yang efisien untuk berbagai modul yang memiliki kebutuhan tampilan yang berbeda-beda. Kelas-kelas utilitas yang disediakan oleh Tailwind memungkinkan pembuatan komponen antarmuka pengguna yang konsisten namun tetap fleksibel, seperti tabel daftar mahasiswa dengan styling yang seragam menggunakan kelas seperti bg-white, border, dan shadow-md, yang kemudian dapat dengan mudah disesuaikan untuk digunakan pada tabel KRS, tabel nilai, atau tabel jadwal kuliah. Desain responsif dengan menggunakan breakpoint yang disediakan Tailwind seperti sm:, md:, dan lg: memiliki peran yang sangat penting untuk memastikan sistem dapat diakses dengan baik dari berbagai jenis perangkat—mahasiswa dapat mengakses sistem KRS dari smartphone mereka, dosen dapat melakukan input nilai dari tablet, sementara administrator dapat mengelola data dari desktop komputer. Sistem grid yang dimiliki Tailwind seperti grid-cols-1 md:grid-cols-2 lg:grid-cols-3 memungkinkan pembuatan layout dashboard yang adaptif, yang dapat menampilkan berbagai statistik akademik seperti jumlah mahasiswa, jumlah mata kuliah, dan jumlah dosen dalam format grid yang responsif sesuai dengan ukuran layar pengguna. Kelas-kelas utilitas untuk spacing seperti p-4, m-2, dan gap-6 memastikan konsistensi jarak antar elemen di seluruh modul akademik, mulai dari form pendaftaran KRS hingga halaman transkrip nilai. Tailwind juga menyediakan kelas-kelas khusus untuk state management seperti hover:, focus:, dan active: yang dapat meningkatkan pengalaman pengguna, contohnya tombol "Setujui KRS" yang berubah warna saat di-hover oleh pengguna, atau field input yang memiliki border yang lebih jelas saat dalam kondisi focus. Proses purging atau pembersihan CSS yang tidak terpakai memberikan keuntungan yang signifikan untuk sistem akademik yang memiliki banyak halaman, karena hanya CSS yang benar-benar digunakan yang akan diikutsertakan dalam build final, sehingga menghasilkan file CSS yang lebih kecil dan waktu loading yang lebih cepat—faktor yang sangat penting untuk sistem yang diakses oleh banyak pengguna secara bersamaan. Tailwind CSS versi 4 yang dilengkapi dengan variabel CSS memungkinkan kustomisasi tema yang mudah dilakukan, seperti mengubah warna primary dari biru menjadi hijau untuk menyesuaikan dengan identitas visual Institut Teknologi Al Mahrusiyah, tanpa perlu melakukan perubahan pada setiap komponen secara manual. Penelitian oleh Sudargo & Tony (2025) menunjukkan bahwa integrasi Tailwind CSS dalam pengembangan website manajemen kegiatan MBKM dapat mempercepat proses pengembangan dan meningkatkan pengalaman pengguna melalui desain yang responsif dan modern.
```

**Sitasi ditambahkan:** Sudargo & Tony (2025)

---

## 5. Analisis Perbandingan dan Gap Penelitian (Baris 71-91)

### Perubahan Utama:
- **Paragraf pembuka** diubah untuk lebih natural
- **6 perbedaan fungsional** dijelaskan lebih detail dengan parafrase
- **Gap Teknologi** ditambahkan sitasi baru

**Sitasi ditambahkan:** Al Farisi dkk. (2025) pada bagian Gap Teknologi

---

## 6. Hubungan Konseptual Antar Komponen (Baris 104-123)

### Perubahan Utama:
- **Semua sub-bagian** diparafrase dengan lebih natural
- **Struktur tetap sama** tetapi kalimat diubah
- **Tidak ada sitasi baru** (masih menggunakan Syachbana & Huda, 2022)

---

## SITASI YANG PERLU DITAMBAHKAN KE DAFTAR PUSTAKA

### 1. Al Farisi dkk. (2025)
**Digunakan di:** 
- Framework Laravel (baris 20)
- Gap Penelitian (baris 91)

**Format Referensi:**
```
Al Farisi, [Nama Lengkap], dkk. (2025). Implementasi Sistem Informasi Akademik Berbasis Laravel untuk Pengelolaan Tugas Akhir Mahasiswa. Jurnal Sistem Informasi, [Volume]([Nomor]), [Halaman]. https://ojs.trigunadharma.ac.id/index.php/jsi/article/view/[ID]
```

**Link untuk download:**
- Cari di: https://ojs.trigunadharma.ac.id/index.php/jsi
- Atau Google Scholar dengan keyword: "Al Farisi Laravel sistem informasi akademik tugas akhir"

### 2. Sudargo & Tony (2025)
**Digunakan di:**
- Tailwind CSS (baris 50)

**Format Referensi:**
```
Sudargo, [Nama Lengkap], & Tony, [Nama Lengkap]. (2025). Integrasi Tailwind CSS dalam Pengembangan Website Manajemen Kegiatan MBKM. Jurnal [Nama Jurnal], [Volume]([Nomor]), [Halaman]. https://journal.ipm2kpe.or.id/index.php/INTECOM/article/view/[ID]
```

**Link untuk download:**
- Cari di: https://journal.ipm2kpe.or.id/index.php/INTECOM
- Atau Google Scholar dengan keyword: "Sudargo Tony Tailwind CSS MBKM"

---

## CATATAN PENTING

1. **Jurnal yang disebutkan di atas perlu diverifikasi** - Pastikan jurnal benar-benar ada dan dapat diakses sebelum digunakan
2. **Jika jurnal tidak ditemukan**, gunakan jurnal alternatif dengan topik yang sama
3. **Format sitasi** harus disesuaikan dengan panduan penulisan skripsi kampus Anda
4. **Hanya bagian yang direvisi dosen** yang diubah - bagian lain tetap sama

---

## CARA MENGGANTI

1. Buka file `BAB II Implementasi Sistem Informasi Akademik.MD`
2. Cari bagian yang disebutkan di atas berdasarkan nomor baris
3. Ganti teks "Sebelum" dengan teks "Sesudah"
4. Pastikan format sitasi sesuai dengan panduan kampus
5. Tambahkan referensi ke Daftar Pustaka

