# BAB V

# PENUTUP

## Implementasi Sistem Informasi Akademik (SIAKAD) pada Institut Teknologi Al Mahrusiyah Kota Kediri

---

## A. KESIMPULAN

Berdasarkan hasil penelitian yang telah dilakukan mengenai pengembangan dan implementasi Sistem Informasi Akademik (SIAKAD) berbasis web menggunakan framework Laravel pada Institut Teknologi Al Mahrusiyah Kota Kediri, dapat ditarik beberapa kesimpulan sebagai berikut:

### 1. Jawaban atas Rumusan Masalah

Pertanyaan Penelitian: Bagaimana mengembangkan dan mengimplementasikan Sistem Informasi Akademik (SIAKAD) berbasis web menggunakan framework Laravel untuk meningkatkan efisiensi pengelolaan data akademik di Institut Teknologi Al Mahrusiyah Kota Kediri?

Jawaban:

a. Pengembangan Sistem Berhasil Dilakukan

Sistem Informasi Akademik (SIAKAD) telah berhasil dikembangkan menggunakan framework Laravel 11 dengan arsitektur three-tier yang terdiri dari Presentation Layer (Blade + Tailwind CSS), Application Layer (Laravel), dan Data Layer (MySQL). Sistem dikembangkan melalui metodologi SDLC model Waterfall yang meliputi tahapan analisis kebutuhan, perancangan sistem, implementasi, dan pengujian. Pengembangan sistem dilakukan dengan memperhatikan kebutuhan fungsional dan non-fungsional yang telah diidentifikasi melalui observasi dan wawancara dengan stakeholder di Institut Teknologi Al Mahrusiyah.

b. Implementasi Sistem Berhasil Diterapkan

Sistem SIAKAD telah berhasil diimplementasikan di lingkungan Institut Teknologi Al Mahrusiyah dengan 70+ fitur terintegrasi yang mencakup:

-   Modul Master Data: Pengelolaan Program Studi, Mahasiswa, Dosen, Mata Kuliah, Semester, dan Jadwal Kuliah
-   Modul KRS (Kartu Rencana Studi): Sistem pendaftaran mata kuliah dengan approval oleh admin
-   Modul KHS dan Transkrip: Kartu Hasil Studi per semester dan transkrip akademik dengan perhitungan IPK otomatis
-   Modul Input Nilai: Sistem pengisian nilai oleh dosen dengan validasi otomatis
-   Modul Presensi: Presensi QR Code real-time dan presensi manual oleh dosen
-   Modul Tugas dan Ujian Online: Sistem tugas dan ujian dengan anti-cheat system
-   Modul Pembayaran: Integrasi dengan payment gateway Xendit
-   Modul Komunikasi: Chat real-time, forum diskusi, dan Q&A
-   Modul Pengumuman dan Notifikasi: Sistem pengumuman dengan notifikasi in-app dan email

Sistem telah diuji melalui unit testing, integration testing, dan user acceptance testing dengan hasil yang memuaskan. Semua modul dapat berfungsi dengan baik sesuai dengan role pengguna (Admin, Dosen, Mahasiswa).

c. Efisiensi Pengelolaan Data Akademik Meningkat

Implementasi sistem SIAKAD telah berhasil meningkatkan efisiensi pengelolaan data akademik di Institut Teknologi Al Mahrusiyah. Hal ini terlihat dari:

-   Proses yang Lebih Cepat: Proses pengolahan data akademik yang sebelumnya manual kini dapat dilakukan secara digital dengan waktu yang lebih singkat
-   Pengurangan Kesalahan: Sistem validasi otomatis mengurangi risiko kesalahan input data yang sering terjadi pada sistem manual
-   Akses Real-Time: Data akademik dapat diakses secara real-time oleh admin, dosen, dan mahasiswa kapan saja dan di mana saja selama terhubung dengan internet
-   Terintegrasi: Semua proses akademik terintegrasi dalam satu platform, menghindari duplikasi data dan inkonsistensi informasi
-   Monitoring yang Lebih Baik: Admin dapat memantau berbagai statistik dan informasi penting melalui dashboard tanpa harus mencari data dari berbagai tempat

### 2. Tujuan Penelitian Tercapai

Tujuan penelitian untuk mengembangkan dan mengimplementasikan Sistem Informasi Akademik (SIAKAD) berbasis web menggunakan framework Laravel yang dapat mengintegrasikan seluruh proses akademik telah tercapai. Sistem yang dikembangkan dapat mengelola KRS, data mahasiswa, input nilai, jadwal kuliah, dan laporan akademik secara terintegrasi dalam satu platform terpusat.

### 3. Manfaat Penelitian Terwujud

a. Manfaat Teoritis:

-   Penelitian ini memberikan kontribusi pada pengembangan ilmu pengetahuan di bidang sistem informasi, khususnya dalam penerapan metodologi SDLC Waterfall untuk pengembangan sistem informasi akademik berbasis web
-   Hasil penelitian dapat menjadi referensi untuk penelitian selanjutnya dalam pengembangan sistem akademik menggunakan framework Laravel
-   Dokumentasi metodologi dan perancangan sistem dapat menjadi acuan untuk peneliti lain yang ingin mengembangkan sistem serupa

b. Manfaat Praktis:

-   Bagi Institut Teknologi Al Mahrusiyah: Meningkatkan efisiensi operasional, kualitas layanan akademik, dan manajemen data yang lebih baik
-   Bagi Dosen: Memudahkan input nilai, akses jadwal mengajar, dan manajemen kelas secara online
-   Bagi Mahasiswa: Memudahkan pengambilan KRS, akses informasi akademik real-time, dan self-service akademik tanpa harus datang ke kampus
-   Bagi Staf Administrasi: Mengurangi beban administratif, meningkatkan akurasi data, dan efisiensi kerja

### 4. Kontribusi Penelitian

Penelitian ini memberikan kontribusi baru dalam implementasi sistem informasi akademik menggunakan framework Laravel 11 di perguruan tinggi dengan fitur-fitur modern seperti presensi QR Code, tugas online dengan anti-cheat system, dan integrasi payment gateway. Berbeda dengan penelitian terdahulu yang sebagian besar fokus di level sekolah dan menggunakan teknologi konvensional, penelitian ini berhasil mengimplementasikan sistem di perguruan tinggi dengan kompleksitas kebutuhan yang lebih tinggi.

---

## B. SARAN

Berdasarkan hasil penelitian dan implementasi Sistem Informasi Akademik (SIAKAD) yang telah dilakukan, peneliti mengajukan beberapa saran sebagai berikut:

### 1. Saran untuk Pengembangan Sistem

a. Pengembangan Modul Tambahan

Sistem SIAKAD yang telah dikembangkan dapat dikembangkan lebih lanjut dengan menambahkan modul-modul baru yang dapat meningkatkan fungsionalitas sistem, antara lain:

-   Sistem Keuangan: Modul untuk mengelola pembayaran SPP, biaya kuliah, dan keuangan mahasiswa secara terintegrasi
-   Perpustakaan Digital: Modul untuk manajemen perpustakaan, peminjaman buku, dan katalog digital
-   Sistem Manajemen Skripsi: Modul untuk mengelola proses skripsi mulai dari proposal, bimbingan, hingga ujian skripsi
-   Sistem Penelitian Dosen: Modul untuk mengelola data penelitian dan publikasi dosen
-   Integrasi dengan Sistem Eksternal: Integrasi dengan sistem eksternal seperti sistem keuangan pemerintah, sistem perpustakaan nasional, atau sistem lain yang relevan

b. Peningkatan Fitur Keamanan

Untuk meningkatkan keamanan sistem, disarankan untuk:

-   Implementasi two-factor authentication (2FA) untuk login pengguna
-   Audit log yang lebih detail untuk tracking aktivitas pengguna
-   Enkripsi data sensitif di database
-   Regular security audit dan penetration testing
-   Backup data otomatis dengan frekuensi yang lebih sering

c. Optimasi Performa

Untuk meningkatkan performa sistem, disarankan untuk:

-   Implementasi caching untuk data yang sering diakses
-   Optimasi query database untuk mengurangi waktu loading
-   CDN (Content Delivery Network) untuk static assets
-   Load balancing jika traffic meningkat
-   Monitoring performa sistem secara real-time

d. Peningkatan User Experience

Untuk meningkatkan pengalaman pengguna, disarankan untuk:

-   Responsive design yang lebih optimal untuk berbagai ukuran layar
-   Aplikasi mobile native (Android/iOS) untuk akses yang lebih mudah
-   Notifikasi push untuk informasi penting
-   Dashboard yang lebih interaktif dengan visualisasi data
-   Tutorial dan help center yang lebih lengkap

### 2. Saran untuk Penelitian Lanjutan

a. Evaluasi Jangka Panjang

Disarankan untuk melakukan evaluasi jangka panjang terhadap efektivitas sistem setelah digunakan dalam periode yang lebih lama (minimal 1-2 tahun). Evaluasi ini dapat mencakup:

-   Analisis kepuasan pengguna (user satisfaction) melalui survei dan wawancara
-   Pengukuran dampak sistem terhadap efisiensi operasional institusi
-   Analisis ROI (Return on Investment) dari implementasi sistem
-   Identifikasi masalah dan kendala yang muncul selama penggunaan sistem

b. Studi Komparatif

Disarankan untuk melakukan studi komparatif dengan sistem informasi akademik lain, baik yang menggunakan teknologi berbeda maupun yang digunakan di institusi lain. Studi ini dapat memberikan wawasan tentang:

-   Kelebihan dan kekurangan sistem yang dikembangkan dibandingkan sistem lain
-   Best practices yang dapat diadopsi dari sistem lain
-   Standar dan benchmark untuk sistem informasi akademik

c. Pengembangan Aplikasi Mobile Native

Disarankan untuk mengembangkan aplikasi mobile native (Android/iOS) yang terintegrasi dengan sistem web. Aplikasi mobile dapat memberikan:

-   Akses yang lebih mudah dan cepat untuk pengguna
-   Notifikasi push yang lebih efektif
-   Fitur offline untuk beberapa fungsi tertentu
-   Pengalaman pengguna yang lebih baik di perangkat mobile

d. Integrasi dengan Teknologi Emerging

Disarankan untuk mengeksplorasi integrasi dengan teknologi emerging seperti:

-   Artificial Intelligence (AI): Untuk rekomendasi mata kuliah, prediksi performa mahasiswa, atau chatbot untuk customer service
-   Machine Learning: Untuk analisis data akademik dan prediksi tren
-   Blockchain: Untuk sertifikat digital dan transkrip yang tidak dapat dipalsukan
-   Internet of Things (IoT): Untuk presensi otomatis menggunakan sensor atau smart devices

### 3. Saran untuk Institusi

a. Pelatihan dan Sosialisasi

Disarankan untuk melakukan pelatihan dan sosialisasi yang lebih intensif kepada semua pengguna sistem (admin, dosen, mahasiswa) untuk memastikan mereka dapat menggunakan sistem dengan optimal. Pelatihan dapat dilakukan melalui:

-   Workshop dan training session
-   Video tutorial dan dokumentasi yang mudah dipahami
-   Help desk dan support system yang responsif
-   User manual yang lengkap dan terstruktur

b. Maintenance dan Update Berkala

Disarankan untuk melakukan maintenance dan update sistem secara berkala untuk:

-   Memperbaiki bug dan error yang ditemukan
-   Menambahkan fitur-fitur baru sesuai kebutuhan
-   Meningkatkan keamanan sistem
-   Mengoptimalkan performa sistem

c. Monitoring dan Evaluasi Kontinyu

Disarankan untuk melakukan monitoring dan evaluasi kontinyu terhadap penggunaan sistem untuk:

-   Mengidentifikasi masalah dan kendala yang muncul
-   Mengumpulkan feedback dari pengguna
-   Menganalisis data penggunaan sistem
-   Menentukan prioritas pengembangan selanjutnya

d. Backup dan Disaster Recovery

Disarankan untuk memiliki strategi backup dan disaster recovery yang komprehensif untuk:

-   Melindungi data dari kehilangan atau kerusakan
-   Memastikan sistem dapat pulih dengan cepat jika terjadi masalah
-   Meminimalkan downtime sistem
-   Memastikan business continuity

### 4. Saran untuk Institusi Lain

Bagi institusi lain yang ingin mengimplementasikan sistem serupa, disarankan untuk:

a. Analisis Kebutuhan Spesifik

Setiap institusi memiliki karakteristik dan kebutuhan yang berbeda. Oleh karena itu, sebelum mengimplementasikan sistem, perlu dilakukan analisis kebutuhan yang mendalam untuk:

-   Mengidentifikasi kebutuhan fungsional dan non-fungsional yang spesifik
-   Memahami alur bisnis dan proses akademik yang ada
-   Menentukan prioritas fitur yang akan dikembangkan
-   Menyesuaikan sistem dengan kebijakan dan regulasi institusi

b. Adaptasi dan Kustomisasi

Sistem yang dikembangkan untuk Institut Teknologi Al Mahrusiyah dapat diadaptasi untuk institusi lain, namun perlu dilakukan kustomisasi sesuai dengan kebutuhan spesifik masing-masing institusi. Kustomisasi dapat mencakup:

-   Penyesuaian workflow dan business process
-   Modifikasi fitur sesuai kebutuhan
-   Integrasi dengan sistem yang sudah ada
-   Penyesuaian dengan regulasi dan kebijakan institusi

c. Perencanaan Implementasi

Implementasi sistem informasi akademik memerlukan perencanaan yang matang. Disarankan untuk:

-   Membuat timeline implementasi yang realistis
-   Menyiapkan sumber daya yang diperlukan (SDM, infrastruktur, budget)
-   Melakukan pilot testing sebelum implementasi penuh
-   Memiliki rencana rollback jika terjadi masalah

---

## C. PENUTUP

Penelitian mengenai pengembangan dan implementasi Sistem Informasi Akademik (SIAKAD) berbasis web menggunakan framework Laravel pada Institut Teknologi Al Mahrusiyah Kota Kediri telah berhasil dilaksanakan. Sistem yang dikembangkan telah berhasil mengintegrasikan seluruh proses akademik dalam satu platform terpusat, meningkatkan efisiensi pengelolaan data akademik, dan memberikan layanan akademik yang lebih baik bagi semua pengguna sistem.

Melalui penelitian ini, telah terbukti bahwa pengembangan sistem informasi akademik menggunakan framework Laravel dengan metodologi SDLC Waterfall dapat menghasilkan sistem yang robust, scalable, dan user-friendly. Sistem yang dikembangkan tidak hanya memenuhi kebutuhan fungsional, tetapi juga memberikan pengalaman pengguna yang baik dan keamanan yang memadai.

Penelitian ini memberikan kontribusi baik secara teoritis maupun praktis. Secara teoritis, penelitian ini berkontribusi pada pengembangan ilmu pengetahuan di bidang sistem informasi dan metodologi pengembangan perangkat lunak. Secara praktis, sistem yang dikembangkan memberikan solusi konkret untuk meningkatkan efisiensi dan kualitas layanan akademik di Institut Teknologi Al Mahrusiyah.

Meskipun penelitian ini telah mencapai tujuan yang ditetapkan, peneliti menyadari bahwa masih ada ruang untuk pengembangan dan perbaikan lebih lanjut. Saran-saran yang telah disampaikan diharapkan dapat menjadi acuan untuk pengembangan sistem di masa depan dan penelitian lanjutan yang dapat memperkaya khazanah ilmu pengetahuan di bidang sistem informasi akademik.

Akhirnya, peneliti berharap bahwa penelitian ini dapat memberikan manfaat yang sebesar-besarnya bagi Institut Teknologi Al Mahrusiyah, pengguna sistem, dan dunia akademik pada umumnya. Peneliti juga berharap bahwa sistem yang dikembangkan dapat terus dikembangkan dan ditingkatkan untuk memberikan layanan yang lebih baik di masa depan.

---

SELESAI
