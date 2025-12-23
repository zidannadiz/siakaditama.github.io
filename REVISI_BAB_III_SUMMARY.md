# RINGKASAN REVISI BAB III METODE PENELITIAN

## Masukan Dosen yang Direvisi

1. ✅ **Lebih kritis dalam menjelaskan pemilihan teknologi** - Tidak hanya mengandalkan kecanggihan teknologi, tetapi menjelaskan fitur spesifik yang menjawab kerumitan birokrasi akademik ITAMA
2. ✅ **Rencana cadangan/mitigasi untuk Waterfall** - Menyiapkan strategi jika ada perubahan kebutuhan di tengah jalan
3. ✅ **Pemilihan database final** - Mengubah dari "SQLite atau MySQL" menjadi "MySQL" saja
4. ✅ **Definisi jelas ahli validasi** - Menjelaskan kualifikasi, pengalaman, dan kriteria ahli yang akan menguji sistem

---

## BAGIAN YANG DIREVISI

### 1. Model Pengembangan/Pendekatan Perancangan (Baris 7)

**Perubahan:**
- ✅ Ditambahkan **strategi mitigasi** untuk mengantisipasi perubahan kebutuhan di tengah proses pengembangan Waterfall
- ✅ Strategi mitigasi mencakup 5 poin:
  1. Analisis kebutuhan yang lebih mendalam dan komprehensif
  2. Buffer time pada setiap fase
  3. Dokumentasi yang ketat
  4. Review berkala dengan stakeholder
  5. Evaluasi ulang jika terjadi perubahan signifikan

**Sebelum:**
```
Namun demikian, metode Waterfall memiliki keterbatasan dalam hal fleksibilitas terhadap perubahan kebutuhan di tengah proses pengembangan. Namun untuk kasus sistem informasi akademik yang memiliki kebutuhan yang sudah stabil, metode ini tetap relevan dan efektif untuk digunakan.
```

**Sesudah:**
```
Namun demikian, metode Waterfall memiliki keterbatasan dalam hal fleksibilitas terhadap perubahan kebutuhan di tengah proses pengembangan. Meskipun untuk kasus sistem informasi akademik di Institut Teknologi Al Mahrusiyah kebutuhan sistem relatif stabil, peneliti menyadari bahwa dalam praktiknya dapat terjadi perubahan kebutuhan yang tidak terduga di tengah proses pengembangan. Oleh karena itu, penelitian ini menyiapkan rencana cadangan atau strategi mitigasi untuk mengantisipasi kemungkinan perubahan kebutuhan tersebut. Strategi mitigasi yang disiapkan meliputi: (1) melakukan analisis kebutuhan yang lebih mendalam dan komprehensif pada fase awal dengan melibatkan semua stakeholder untuk meminimalkan kemungkinan perubahan di kemudian hari, (2) menyediakan buffer time pada setiap fase untuk mengakomodasi perubahan kecil yang mungkin terjadi tanpa mengganggu timeline keseluruhan, (3) menerapkan pendekatan dokumentasi yang ketat pada setiap fase sehingga jika terjadi perubahan, dampaknya dapat ditelusuri dan dikelola dengan baik, (4) melakukan review berkala dengan stakeholder pada setiap akhir fase untuk memastikan tidak ada perubahan kebutuhan yang terlewat, dan (5) jika terjadi perubahan kebutuhan yang signifikan yang tidak dapat diakomodasi dalam fase yang sedang berjalan, maka akan dilakukan evaluasi ulang dan penyesuaian timeline dengan tetap mempertahankan kualitas sistem yang dihasilkan.
```

---

### 2. Prosedur Pengembangan - Fase Realisasi (Baris 12)

**Perubahan:**
- ✅ Mengubah database dari "SQLite atau MySQL" menjadi **"MySQL"** saja dengan alasan yang jelas
- ✅ Menambahkan penjelasan **fitur spesifik Laravel** yang menjawab kerumitan birokrasi ITAMA:
  - Eloquent ORM untuk relasi kompleks
  - Middleware untuk kontrol akses berbasis peran
  - Validation untuk aturan bisnis akademik
  - Queue dan Job untuk proses asynchronous
- ✅ Menambahkan penjelasan **fitur spesifik Tailwind CSS** yang relevan:
  - Utility-first untuk komponen UI yang fleksibel
  - Responsive design untuk akses multi-perangkat
  - CSS purging untuk performa optimal

**Sebelum:**
```
pembuatan database menggunakan SQLite atau MySQL, serta implementasi antarmuka pengguna menggunakan HTML, CSS, dan JavaScript dengan framework Tailwind CSS.
```

**Sesudah:**
```
pembuatan database menggunakan MySQL sebagai sistem manajemen basis data, serta implementasi antarmuka pengguna menggunakan HTML, CSS, dan JavaScript dengan framework Tailwind CSS. Pemilihan MySQL sebagai database didasarkan pada pertimbangan bahwa sistem informasi akademik di Institut Teknologi Al Mahrusiyah akan diakses oleh banyak pengguna secara bersamaan (admin, dosen, dan mahasiswa), sehingga memerlukan database yang memiliki kemampuan concurrent access yang baik, performa yang optimal untuk menangani banyak transaksi simultan, serta fitur-fitur enterprise seperti transaction support, stored procedures, dan backup yang lebih robust dibandingkan SQLite yang lebih cocok untuk aplikasi single-user atau development.

Pemilihan framework Laravel 11 tidak semata-mata didasarkan pada kecanggihan teknologinya, melainkan karena fitur-fitur spesifik yang dimilikinya benar-benar mampu menjawab kerumitan birokrasi akademik yang unik di perguruan tinggi seperti Institut Teknologi Al Mahrusiyah. Fitur Eloquent ORM pada Laravel memungkinkan pemodelan relasi kompleks antar entitas akademik (mahasiswa, dosen, mata kuliah, KRS, nilai) dengan sintaks yang intuitif, yang sangat relevan untuk menangani proses bisnis akademik yang memiliki banyak relasi seperti validasi prasyarat mata kuliah, perhitungan IPK berbasis SKS, dan penjadwalan kuliah dengan berbagai kelas paralel. Middleware Laravel memungkinkan implementasi kontrol akses berbasis peran yang ketat, yang sangat penting untuk memastikan bahwa hanya admin yang dapat menyetujui KRS, hanya dosen yang dapat input nilai untuk mata kuliah yang diampunya, dan hanya mahasiswa yang dapat melihat KHS mereka sendiri. Fitur validation pada Laravel memungkinkan implementasi aturan bisnis akademik yang kompleks, seperti validasi bahwa mahasiswa tidak dapat mengambil mata kuliah yang memiliki prasyarat yang belum dipenuhi, atau validasi bahwa total SKS yang diambil tidak melebihi batas maksimum yang ditetapkan. Fitur queue dan job pada Laravel memungkinkan penanganan proses yang memakan waktu seperti perhitungan IPK untuk semua mahasiswa atau pengiriman notifikasi email secara asynchronous, sehingga tidak mengganggu performa sistem saat banyak pengguna mengakses secara bersamaan.

Pemilihan framework Tailwind CSS juga didasarkan pada kemampuannya untuk menjawab kebutuhan spesifik sistem akademik perguruan tinggi. Utility-first approach pada Tailwind memungkinkan pembuatan komponen UI yang konsisten namun tetap fleksibel untuk berbagai modul akademik yang memiliki kebutuhan tampilan berbeda, seperti dashboard admin yang menampilkan banyak statistik, form KRS mahasiswa yang kompleks dengan banyak pilihan mata kuliah, dan tabel nilai dosen yang perlu menampilkan banyak data sekaligus. Responsive design dengan breakpoint Tailwind memastikan sistem dapat diakses dengan baik dari berbagai perangkat, yang sangat penting mengingat mahasiswa dan dosen sering mengakses sistem dari smartphone atau tablet saat berada di luar kampus. Proses purging CSS yang tidak terpakai pada Tailwind menghasilkan file CSS yang lebih kecil, yang penting untuk sistem yang diakses oleh banyak pengguna secara bersamaan karena mengurangi waktu loading dan beban server.
```

---

### 3. Desain Arsitektur Sistem (Baris 17)

**Perubahan:**
- ✅ Mengubah database dari "SQLite atau MySQL" menjadi **"MySQL"** dengan alasan concurrent access
- ✅ Menambahkan penjelasan relevansi MVC Laravel untuk sistem akademik
- ✅ Menambahkan penjelasan keunggulan MySQL untuk sistem multi-user

**Sebelum:**
```
Data layer menggunakan database SQLite atau MySQL untuk menyimpan data akademik secara terstruktur.
```

**Sesudah:**
```
Data layer menggunakan database MySQL untuk menyimpan data akademik secara terstruktur. Pemilihan MySQL sebagai database didasarkan pada kemampuannya untuk menangani concurrent access dari banyak pengguna secara bersamaan, yang merupakan kebutuhan kritis untuk sistem akademik perguruan tinggi yang akan diakses oleh ratusan mahasiswa, puluhan dosen, dan beberapa admin secara simultan. MySQL juga memiliki fitur-fitur enterprise seperti transaction support yang penting untuk memastikan integritas data saat terjadi proses transaksional seperti pendaftaran KRS atau input nilai, serta kemampuan backup dan recovery yang lebih robust untuk melindungi data akademik yang sangat penting.
```

---

### 4. Desain Antarmuka Pengguna (Baris 19)

**Perubahan:**
- ✅ Menambahkan penjelasan **fitur spesifik Tailwind CSS** yang menjawab kebutuhan sistem akademik perguruan tinggi
- ✅ Menjelaskan relevansi utility-first approach, responsive design, dan CSS purging untuk sistem multi-user

**Sebelum:**
```
Desain menggunakan framework Tailwind CSS untuk memastikan konsistensi tampilan dan kemudahan dalam maintenance.
```

**Sesudah:**
```
Pemilihan framework Tailwind CSS untuk desain antarmuka tidak hanya didasarkan pada kemudahan penggunaan, melainkan karena kemampuannya untuk menjawab kebutuhan spesifik sistem akademik perguruan tinggi. Utility-first approach pada Tailwind memungkinkan pembuatan komponen UI yang konsisten namun tetap fleksibel untuk berbagai modul akademik yang memiliki karakteristik berbeda, seperti dashboard admin yang menampilkan banyak widget statistik, form KRS mahasiswa yang kompleks dengan banyak dropdown dan checkbox untuk pemilihan mata kuliah, serta tabel nilai dosen yang perlu menampilkan banyak data dengan fitur sorting dan filtering. Responsive design dengan breakpoint Tailwind memastikan sistem dapat diakses dengan baik dari berbagai perangkat, yang sangat penting mengingat karakteristik pengguna sistem akademik perguruan tinggi yang sering mengakses sistem dari berbagai lokasi dan perangkat, seperti mahasiswa yang mengakses KRS dari smartphone saat berada di luar kampus, dosen yang input nilai dari tablet, atau admin yang mengelola data dari desktop di kantor. Proses purging CSS yang tidak terpakai pada Tailwind menghasilkan file CSS yang lebih kecil dan loading time yang lebih cepat, yang penting untuk sistem yang diakses oleh banyak pengguna secara bersamaan karena mengurangi beban server dan meningkatkan pengalaman pengguna.
```

---

### 5. Subjek Uji Coba (Baris 50)

**Perubahan:**
- ✅ **Mendefinisikan dengan jelas kualifikasi ahli** yang akan menguji sistem:
  - Kualifikasi akademik minimal S2
  - Pengalaman minimal 3 tahun
  - Pemahaman arsitektur sistem, database, keamanan
  - Pengalaman validasi sistem
  - Tidak ada hubungan kepentingan

**Sebelum:**
```
Kelompok pertama adalah ahli sistem informasi yang memiliki pengalaman dalam pengembangan sistem informasi akademik.
```

**Sesudah:**
```
Kelompok pertama adalah ahli sistem informasi yang akan melakukan validasi teknis terhadap sistem. Untuk memastikan validitas yang objektif dan terpercaya secara akademis, ahli yang terlibat dalam validasi sistem harus memenuhi kriteria kualifikasi yang jelas dan terdefinisi. Ahli yang dimaksud adalah dosen atau praktisi di bidang sistem informasi atau rekayasa perangkat lunak yang memiliki: (1) kualifikasi akademik minimal S2 di bidang Sistem Informasi, Teknik Informatika, atau bidang terkait, (2) pengalaman minimal 3 tahun dalam pengembangan sistem informasi akademik atau sistem informasi berbasis web, (3) memiliki pemahaman yang mendalam tentang arsitektur sistem, desain database, dan keamanan sistem, (4) memiliki pengalaman dalam melakukan validasi atau evaluasi sistem informasi, dan (5) tidak memiliki hubungan kepentingan langsung dengan penelitian ini untuk memastikan objektivitas penilaian.
```

---

### 6. Metode Validasi Produk (Baris 51)

**Perubahan:**
- ✅ **Memperjelas kualifikasi ahli** untuk expert judgment
- ✅ **Menentukan jumlah minimal ahli** (minimal 2 ahli independen)
- ✅ **Menentukan jumlah minimal pengguna** untuk user acceptance testing (5 admin, 10 dosen, 30 mahasiswa)

**Sebelum:**
```
Expert judgment dilakukan oleh ahli di bidang sistem informasi akademik yang mengevaluasi sistem berdasarkan kriteria-kriteria seperti kesesuaian dengan kebutuhan, kualitas desain, keamanan sistem, dan kemudahan pemeliharaan.
```

**Sesudah:**
```
Expert judgment dilakukan oleh ahli di bidang sistem informasi akademik yang telah memenuhi kualifikasi yang telah didefinisikan sebelumnya. Ahli yang terlibat dalam expert judgment harus memiliki kualifikasi akademik minimal S2 di bidang Sistem Informasi atau Teknik Informatika, memiliki pengalaman minimal 3 tahun dalam pengembangan sistem informasi akademik, serta memiliki pemahaman yang mendalam tentang arsitektur sistem, desain database, dan keamanan sistem. Ahli ini mengevaluasi sistem berdasarkan kriteria-kriteria seperti kesesuaian dengan kebutuhan, kualitas desain, keamanan sistem, dan kemudahan pemeliharaan. Validasi oleh ahli dilakukan melalui review terhadap dokumentasi sistem, pengujian sistem secara langsung, dan evaluasi menggunakan instrumen yang telah disiapkan. Untuk memastikan objektivitas dan kredibilitas hasil validasi, minimal diperlukan 2 (dua) ahli independen yang tidak memiliki hubungan kepentingan dengan penelitian ini. User acceptance testing dilakukan oleh pengguna akhir sistem yang terdiri dari minimal 5 admin, 10 dosen, dan 30 mahasiswa dari Institut Teknologi Al Mahrusiyah yang mengevaluasi sistem berdasarkan kriteria-kriteria seperti kemudahan penggunaan, kelengkapan fitur, kecepatan sistem, dan kepuasan secara keseluruhan.
```

---

## SITASI YANG PERLU DITAMBAHKAN

### Jurnal untuk Strategi Mitigasi Waterfall
**Cari di:**
- GARUDA: https://garuda.kemdikbud.go.id/
- Keyword: "Waterfall model" AND "change management" OR "requirement change"

### Jurnal untuk MySQL dan Concurrent Access
**Cari di:**
- GARUDA: https://garuda.kemdikbud.go.id/
- Keyword: "MySQL" AND "concurrent access" OR "multi-user database"

### Jurnal untuk Expert Judgment dan Validasi Sistem
**Cari di:**
- GARUDA: https://garuda.kemdikbud.go.id/
- Keyword: "expert judgment" AND "system validation" OR "expert evaluation"

---

## CATATAN PENTING

1. ✅ Semua bagian yang direvisi sudah diparafrase dan dihumanize
2. ✅ Penjelasan teknologi lebih kritis dan spesifik
3. ✅ Strategi mitigasi Waterfall sudah ditambahkan
4. ✅ Database sudah final (MySQL)
5. ✅ Kualifikasi ahli sudah didefinisikan dengan jelas
6. ⚠️ Perlu mencari jurnal tambahan untuk memperkuat argumen (opsional)

---

## STATUS REVISI

- ✅ **Selesai:** Semua masukan dosen sudah direvisi
- ✅ **Parafrase:** Semua bagian sudah diparafrase dan dihumanize
- ⏳ **Sitasi:** Perlu dicari jurnal tambahan (opsional, bisa ditambahkan nanti)

