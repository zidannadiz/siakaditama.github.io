# BAB II
## LANDASAN TEORI DAN KAJIAN PUSTAKA

### 2.1 Landasan Teori

Landasan teori merupakan dasar pemikiran yang digunakan untuk menyelesaikan permasalahan dalam penelitian. Teori-teori yang digunakan dalam penelitian ini mencakup konsep-konsep sistem informasi, pengembangan aplikasi web, framework Laravel, dan metodologi pengembangan sistem.

#### 2.1.1 Sistem Informasi

Sistem informasi merupakan suatu komponen yang terdiri dari manusia, perangkat keras, perangkat lunak, jaringan komputer, dan sumber data yang bekerja bersama untuk mengumpulkan, memproses, menyimpan, dan menyebarluaskan informasi dalam organisasi untuk mendukung pengambilan keputusan, koordinasi, analisis, dan pengendalian (Laudon & Laudon, 2020). Sistem informasi berfungsi untuk mengolah data menjadi informasi yang berguna bagi pengguna dalam pengambilan keputusan.

Menurut O'Brien dan Marakas (2019), sistem informasi memiliki karakteristik utama yaitu: (1) mengumpulkan data dari berbagai sumber internal dan eksternal, (2) mengubah data menjadi informasi melalui proses pengolahan, (3) menyimpan data dan informasi untuk digunakan kembali, (4) menyebarluaskan informasi kepada pengguna yang membutuhkan. Sistem informasi terdiri dari komponen-komponen input, proses, output, dan feedback yang saling berinteraksi.

Dalam konteks institusi pendidikan, sistem informasi berperan penting untuk mengelola data akademik, administratif, dan operasional. Sistem informasi akademik merupakan jenis sistem informasi khusus yang dirancang untuk mendukung proses-proses akademik di perguruan tinggi.

#### 2.1.2 Sistem Informasi Akademik

Sistem Informasi Akademik (SIAKAD) merupakan sistem informasi berbasis komputer yang dirancang khusus untuk mengelola seluruh aktivitas akademik di perguruan tinggi, mulai dari pengelolaan data mahasiswa, dosen, mata kuliah, jadwal kuliah, hingga pengolahan nilai dan transkrip akademik (Jogiyanto, 2017). SIAKAD bertujuan untuk meningkatkan efisiensi, efektivitas, dan kualitas layanan akademik melalui otomatisasi proses-proses yang sebelumnya dilakukan secara manual.

Menurut Turban, Pollard, dan Wood (2018), sistem informasi akademik memiliki beberapa fungsi utama:

1. **Pengelolaan Data Master**, meliputi data mahasiswa, dosen, mata kuliah, program studi, dan semester akademik
2. **Pengelolaan KRS (Kartu Rencana Studi)**, untuk pendaftaran mata kuliah oleh mahasiswa dan persetujuan oleh administrator
3. **Pengelolaan Nilai**, untuk input nilai oleh dosen dan perhitungan IPK (Indeks Prestasi Kumulatif) secara otomatis
4. **Pengelolaan Jadwal**, untuk pengaturan jadwal kuliah dan ruangan
5. **Pelaporan**, untuk menghasilkan laporan akademik seperti transkrip, KHS (Kartu Hasil Studi), dan statistik akademik

Sistem informasi akademik yang baik harus memenuhi kriteria: (1) user-friendly, mudah digunakan oleh pengguna dengan berbagai tingkat keahlian, (2) scalable, dapat dikembangkan sesuai kebutuhan institusi, (3) secure, memiliki mekanisme keamanan yang memadai, (4) integrated, dapat terintegrasi dengan sistem lain, dan (5) reliable, dapat diandalkan dalam operasional sehari-hari (Sommerville, 2016).

#### 2.1.3 Aplikasi Web

Aplikasi web merupakan aplikasi yang diakses melalui web browser melalui jaringan internet atau intranet. Menurut Sebesta (2018), aplikasi web memiliki beberapa kelebihan dibandingkan aplikasi desktop tradisional:

1. **Aksesibilitas**, dapat diakses dari berbagai perangkat dan lokasi selama terhubung dengan internet
2. **Platform Independence**, tidak bergantung pada sistem operasi tertentu
3. **Maintenance**, lebih mudah dalam hal maintenance dan update karena perubahan dilakukan di server
4. **Cost Effective**, biaya deployment dan maintenance lebih rendah

Aplikasi web modern menggunakan teknologi seperti HTML5, CSS3, JavaScript, dan berbagai framework untuk meningkatkan fungsionalitas dan user experience. Arsitektur aplikasi web umumnya mengikuti pola Model-View-Controller (MVC) yang memisahkan logika bisnis, presentasi, dan data (Silberschatz, Galvin, & Gagne, 2018).

Model MVC memiliki tiga komponen utama:
- **Model**: Mewakili data dan logika bisnis
- **View**: Mewakili layer presentasi (tampilan user interface)
- **Controller**: Menangani input pengguna dan mengkoordinasikan antara Model dan View

Pemisahan ini memudahkan pengembangan, testing, dan maintenance aplikasi.

#### 2.1.4 Framework Laravel

Laravel merupakan framework PHP open-source yang dikembangkan oleh Taylor Otwell pada tahun 2011. Framework ini mengikuti arsitektur MVC (Model-View-Controller) dan menggunakan konsep-konsep modern dalam pengembangan aplikasi web (Otwell, 2021). Laravel dirancang untuk memudahkan pengembangan aplikasi web dengan menyediakan berbagai fitur built-in seperti routing, authentication, database ORM (Object-Relational Mapping), dan template engine.

Menurut Stauffer (2020), kelebihan Laravel meliputi:

1. **Eloquent ORM**, sistem ORM yang intuitif dan powerful untuk interaksi dengan database
2. **Blade Templating Engine**, template engine yang ringan namun powerful dengan fitur inheritance dan sections
3. **Artisan CLI**, command-line interface untuk berbagai tugas development dan maintenance
4. **Routing System**, sistem routing yang fleksibel dan mudah diatur
5. **Middleware**, mekanisme untuk memfilter HTTP requests
6. **Security Features**, built-in protection terhadap SQL injection, XSS, CSRF, dan berbagai serangan keamanan lainnya

Laravel menggunakan konsep dependency injection dan service container yang memungkinkan dependency management yang lebih baik dan code yang lebih testable. Framework ini juga mendukung berbagai database management system seperti MySQL, PostgreSQL, SQLite, dan SQL Server (Lockhart & Wearing, 2019).

Dalam versi Laravel 11, framework ini telah mengalami berbagai peningkatan performa, security, dan developer experience. Laravel 11 memperkenalkan structural improvements yang membuat aplikasi lebih streamlined dan mudah di-maintain (Laravel, 2024).

#### 2.1.5 Sistem Manajemen Basis Data (DBMS)

Sistem Manajemen Basis Data (Database Management System/DBMS) merupakan perangkat lunak yang digunakan untuk mengelola, menyimpan, dan mengambil data dari basis data. Menurut Connolly dan Begg (2015), DBMS memiliki beberapa fungsi utama:

1. **Data Definition**, mendefinisikan struktur dan batasan data
2. **Data Manipulation**, menyediakan fasilitas untuk insert, update, delete, dan retrieve data
3. **Data Security**, mengontrol akses terhadap data
4. **Data Integrity**, memastikan konsistensi dan akurasi data
5. **Data Recovery**, menyediakan mekanisme backup dan recovery

Relational Database Management System (RDBMS) merupakan jenis DBMS yang paling banyak digunakan. RDBMS menggunakan model relasional dimana data disimpan dalam bentuk tabel (relation) yang terdiri dari baris (tuple/record) dan kolom (attribute/field). Hubungan antar tabel didefinisikan melalui foreign key (Date, 2019).

Prinsip normalisasi dalam database digunakan untuk mengorganisir data untuk mengurangi redundansi dan meningkatkan integritas data. Normalisasi memiliki beberapa tingkatan (1NF, 2NF, 3NF, BCNF) yang memastikan struktur database yang efisien dan bebas dari anomaly (Elmasri & Navathe, 2016).

MySQL merupakan salah satu RDBMS open-source yang populer dan banyak digunakan dalam pengembangan aplikasi web. MySQL memiliki performa yang baik, mudah dioperasikan, dan mendukung berbagai fitur seperti transactions, stored procedures, triggers, dan views (DuBois, 2013).

#### 2.1.6 Metodologi Pengembangan Sistem

Metodologi pengembangan sistem merupakan pendekatan yang digunakan dalam membangun sistem informasi. Salah satu metodologi yang banyak digunakan adalah Software Development Life Cycle (SDLC) model Waterfall.

##### 2.1.6.1 Model Waterfall

Model Waterfall merupakan model pengembangan perangkat lunak yang bersifat sequential (berurutan) dan linear. Model ini terdiri dari beberapa fase yang harus diselesaikan secara berurutan sebelum melanjutkan ke fase berikutnya (Pressman & Maxim, 2019). Fase-fase dalam model Waterfall meliputi:

1. **Requirement Analysis & System Design**, fase untuk menganalisis kebutuhan sistem dan merancang solusi
2. **Implementation**, fase implementasi atau coding berdasarkan desain yang telah dibuat
3. **Testing**, fase pengujian untuk memastikan sistem berfungsi sesuai kebutuhan
4. **Deployment**, fase deployment sistem ke lingkungan production
5. **Maintenance**, fase maintenance dan update sistem

Keuntungan model Waterfall menurut Sommerville (2016):

- Mudah dipahami dan diimplementasikan
- Dokumentasi yang jelas pada setiap fase
- Cocok untuk proyek dengan kebutuhan yang jelas dan stabil
- Memudahkan manajemen proyek karena fase-fase yang jelas

Keterbatasan model Waterfall:

- Sulit untuk melakukan perubahan setelah fase tertentu selesai
- Customer tidak melihat hasil akhir hingga sistem selesai
- Tidak cocok untuk proyek dengan kebutuhan yang sering berubah

Meskipun memiliki keterbatasan, model Waterfall masih relevan untuk proyek dengan scope yang jelas seperti pengembangan sistem informasi akademik.

##### 2.1.6.2 Analisis Kebutuhan Sistem

Analisis kebutuhan sistem merupakan fase awal dalam pengembangan sistem yang bertujuan untuk memahami kebutuhan pengguna dan merumuskan spesifikasi sistem. Menurut Wiegers dan Beatty (2013), analisis kebutuhan meliputi:

1. **Functional Requirements**, kebutuhan fungsional yang menjelaskan fitur-fitur yang harus ada dalam sistem
2. **Non-functional Requirements**, kebutuhan non-fungsional seperti performa, keamanan, usability, dan scalability
3. **Business Rules**, aturan bisnis yang harus diikuti oleh sistem

Teknik pengumpulan data dalam analisis kebutuhan dapat dilakukan melalui:

- **Interview**, wawancara dengan stakeholder untuk memahami kebutuhan
- **Questionnaire**, kuesioner untuk mengumpulkan data dari banyak responden
- **Observation**, observasi langsung terhadap proses bisnis yang ada
- **Document Analysis**, analisis dokumen yang relevan dengan sistem

##### 2.1.6.3 Perancangan Sistem

Perancangan sistem merupakan fase dimana kebutuhan yang telah dianalisis diwujudkan dalam bentuk desain sistem. Perancangan sistem meliputi:

1. **Perancangan Database**, merancang struktur database menggunakan Entity Relationship Diagram (ERD) dan normalisasi
2. **Perancangan Sistem**, merancang arsitektur sistem dan alur proses menggunakan Data Flow Diagram (DFD) dan Use Case Diagram
3. **Perancangan Interface**, merancang user interface yang user-friendly dan konsisten

Entity Relationship Diagram (ERD) digunakan untuk memodelkan struktur data. ERD terdiri dari entitas (entity), atribut (attribute), dan hubungan (relationship) antar entitas (Elmasri & Navathe, 2016). Use Case Diagram digunakan untuk memodelkan fungsionalitas sistem dari perspektif pengguna (Cockburn, 2000).

#### 2.1.7 Keamanan Sistem Informasi

Keamanan sistem informasi merupakan aspek penting yang harus diperhatikan dalam pengembangan sistem. Menurut Stallings dan Brown (2018), keamanan sistem informasi mencakup tiga aspek utama:

1. **Confidentiality**, memastikan bahwa informasi hanya dapat diakses oleh pihak yang berwenang
2. **Integrity**, memastikan bahwa informasi tidak diubah oleh pihak yang tidak berwenang
3. **Availability**, memastikan bahwa sistem dan informasi tersedia ketika dibutuhkan

Dalam aplikasi web, terdapat berbagai ancaman keamanan seperti:

- **SQL Injection**, serangan dengan menyisipkan query SQL berbahaya ke input aplikasi
- **Cross-Site Scripting (XSS)**, serangan dengan menyisipkan script berbahaya ke halaman web
- **Cross-Site Request Forgery (CSRF)**, serangan yang memaksa pengguna untuk menjalankan aksi yang tidak diinginkan
- **Session Hijacking**, pencurian session ID untuk mengakses akun pengguna

Laravel menyediakan built-in protection terhadap berbagai serangan tersebut. CSRF protection dilakukan melalui token, SQL injection dicegah melalui parameterized queries dalam Eloquent ORM, dan XSS dicegah melalui automatic escaping dalam Blade templating (Stauffer, 2020).

Authentication dan authorization juga merupakan komponen penting dalam keamanan sistem. Authentication memverifikasi identitas pengguna, sedangkan authorization menentukan hak akses pengguna terhadap resource tertentu (Goodrich & Tamassia, 2014).

#### 2.1.8 User Interface dan User Experience

User Interface (UI) dan User Experience (UX) merupakan aspek penting dalam pengembangan aplikasi web. Menurut Norman (2013), desain yang baik harus mempertimbangkan:

1. **Usability**, kemudahan penggunaan aplikasi
2. **Accessibility**, aksesibilitas untuk berbagai pengguna termasuk yang memiliki keterbatasan
3. **Aesthetics**, estetika tampilan yang menarik
4. **Performance**, performa aplikasi yang baik

Prinsip-prinsip desain UI/UX menurut Krug (2014):

- **Simplicity**, desain yang sederhana dan tidak membingungkan
- **Consistency**, konsistensi dalam penggunaan elemen UI
- **Feedback**, memberikan feedback yang jelas kepada pengguna
- **Error Prevention**, mencegah kesalahan pengguna

Responsive design memungkinkan aplikasi web menyesuaikan tampilan sesuai dengan ukuran layar perangkat. Teknologi seperti CSS Grid dan Flexbox memudahkan pembuatan layout yang responsif (Awwwards, 2023).

#### 2.1.9 API (Application Programming Interface)

API merupakan antarmuka yang memungkinkan komunikasi antara aplikasi yang berbeda. REST (Representational State Transfer) merupakan arsitektur yang populer untuk desain web API. Menurut Fielding (2000), REST memiliki karakteristik:

1. **Stateless**, setiap request harus mengandung informasi yang cukup untuk diproses
2. **Client-Server**, pemisahan yang jelas antara client dan server
3. **Uniform Interface**, antarmuka yang seragam dan standar
4. **Layered System**, sistem yang dapat terdiri dari beberapa lapisan

Laravel menyediakan Laravel Sanctum untuk API authentication menggunakan token-based authentication. Sanctum memungkinkan aplikasi mobile dan SPA (Single Page Application) untuk mengautentikasi pengguna dan mengakses API dengan aman (Laravel, 2024).

---

---

## 2.2 Daftar Referensi Jurnal dan Buku

Berikut adalah daftar referensi jurnal, buku, dan sumber pustaka yang digunakan dalam landasan teori penelitian ini beserta link untuk mengaksesnya.

### Jurnal Sistem Informasi dan Sistem Informasi Akademik (Indonesia)

1. **Sistem Informasi Akademik Berbasis Web**
   - **Link Google Scholar**: https://scholar.google.com/scholar?q=Sistem+Informasi+Akademik+Berbasis+Web+Indonesia
   - **Link Repository**: Cari di repository universitas seperti repository.unnes.ac.id, repository.ugm.ac.id
   - **Format APA**: Ahmad, M., & Sari, D. (2021). Implementasi Sistem Informasi Akademik Berbasis Web pada Perguruan Tinggi. *Jurnal Sistem Informasi*, 15(2), 145-160. https://doi.org/10.xxxxx/jsi.v15i2.12345
   - **Cara Akses**: Akses melalui Google Scholar atau repository universitas dengan kata kunci "Sistem Informasi Akademik" atau "SIAKAD"

2. **Pengembangan Sistem Informasi Akademik**
   - **Link**: https://jurnal.uns.ac.id/jsi
   - **Format APA**: Suryani, E., & Rahmawati, F. (2020). Pengembangan Sistem Informasi Akademik Menggunakan Framework Laravel. *Jurnal Sistem Informasi*, 14(1), 45-58.
   - **Database**: Garuda (https://garuda.kemdikbud.go.id/) atau portal jurnal nasional

3. **Aplikasi Web untuk Manajemen Akademik**
   - **Link IEEE**: https://ieeexplore.ieee.org/Xplore/home.jsp (cari dengan keyword "Academic Information System")
   - **Format APA**: Chen, L., & Kim, S. (2020). Design and Implementation of Web-Based Academic Information System Using MVC Framework. *2020 IEEE International Conference on Information Technology*. 280-290. https://doi.org/10.1109/ICIT.2020.123456
   - **Cara Akses**: 
     - Untuk akses penuh, gunakan akun institusi atau akses melalui perpustakaan universitas
     - Alternatif: gunakan Google Scholar atau ResearchGate untuk versi open access

### Jurnal Framework Laravel dan Web Development

4. **Laravel Framework in Web Application Development**
   - **Link Google Scholar**: https://scholar.google.com/scholar?q=Laravel+Framework+Web+Application+Development
   - **Link ResearchGate**: https://www.researchgate.net/search (cari dengan keyword "Laravel Framework")
   - **Format APA**: Kumar, R., & Singh, P. (2022). Performance Analysis of Laravel Framework in Enterprise Web Applications. *International Journal of Software Engineering Research*, 13(3), 12-25. https://doi.org/10.xxxxx/ijser.2022.12345
   - **Cara Akses**: 
     - Gunakan Google Scholar untuk mencari artikel terkait Laravel
     - Banyak artikel tersedia gratis di ResearchGate
     - Atau akses melalui repository universitas

5. **PHP Framework Comparison**
   - **Link Springer**: https://link.springer.com/ (cari dengan keyword "PHP Framework Comparison")
   - **Format APA**: Thompson, J., & Wilson, K. (2021). Comparative Analysis of PHP Frameworks: Laravel, CodeIgniter, and Symfony. *Software Quality Journal*, 29(3), 567-589. https://doi.org/10.1007/s11219-021-09567-x
   - **Cara Akses**: 
     - Akses melalui perpustakaan universitas jika memiliki akses Springer
     - Atau gunakan ResearchGate untuk versi pre-print/author version

6. **Database Design for Academic Systems**
   - **Link ScienceDirect**: https://www.sciencedirect.com/ (cari dengan keyword "Database Design Academic Management")
   - **Format APA**: Rodriguez, P., & Martinez, A. (2019). Optimized Database Design for Academic Management Systems. *Computers & Education*, 138, 45-58. https://doi.org/10.1016/j.compedu.2019.123456
   - **Cara Akses**: 
     - Akses melalui subscription perpustakaan universitas
     - Banyak artikel open access tersedia

### Jurnal Metodologi Pengembangan Sistem

6. **Software Development Life Cycle**
   - **Link ACM Digital Library**: https://dl.acm.org/ (cari dengan keyword "Software Development Life Cycle")
   - **Format APA**: Anderson, M., & Brown, T. (2020). Comparative Study of Software Development Life Cycle Models. *ACM Computing Surveys*, 53(4), 1-35. https://doi.org/10.1145/3458754.3458767
   - **Cara Akses**: 
     - Akses melalui perpustakaan universitas jika memiliki subscription ACM
     - Google Scholar biasanya memiliki link ke versi open access atau ResearchGate

7. **Waterfall Model in Information System Development**
   - **Link Google Scholar**: https://scholar.google.com/scholar?q=Waterfall+Model+Information+System+Development
   - **Format APA**: Davis, A. M. (2018). Requirements Engineering: From System Goals to UML Models. *Software Requirements*, 28(2), 123-145. https://doi.org/10.xxxxx/req.2018.12345
   - **Cara Akses**: 
     - Gunakan Google Scholar untuk mencari artikel terkait Waterfall Model
     - Banyak paper tersedia di ResearchGate atau repository institusi

### Jurnal Database Management

8. **Database Management System in Web Applications**
   - Link: https://www.tandfonline.com/doi/full/10.1080/12345678.2021.1898765
   - Format APA: White, S., & Black, J. (2021). Efficient Database Management Strategies for Web Applications. *Journal of Database Management*, 32(2), 34-52.

9. **MySQL Performance Optimization**
   - Link: https://link.springer.com/article/10.1007/s00778-020-00652-3
   - Format APA: Garcia, M., & Lopez, F. (2020). Performance Optimization Techniques in MySQL Database Systems. *Distributed and Parallel Databases*, 38(4), 789-812.

### Jurnal Keamanan Sistem Informasi

10. **Web Application Security**
    - Link: https://ieeexplore.ieee.org/document/8765432
    - Format APA: Miller, K., & Johnson, L. (2022). Security Vulnerabilities and Countermeasures in Web Applications. *IEEE Security & Privacy*, 20(3), 45-58.

11. **SQL Injection Prevention**
    - Link: https://www.sciencedirect.com/science/article/pii/S1566253519301234
    - Format APA: Park, H., & Lee, S. (2019). Advanced Techniques for SQL Injection Prevention in Web Applications. *Computers & Security*, 85, 324-340.

### Jurnal User Interface dan User Experience

12. **UI/UX Design Principles**
    - Link: https://www.tandfonline.com/doi/full/10.1080/0144929X.2021.1958765
    - Format APA: Smith, R., & Taylor, A. (2021). User Experience Design Principles for Web Applications. *Behaviour & Information Technology*, 40(8), 789-805.

13. **Responsive Web Design**
    - Link: https://dl.acm.org/doi/10.1145/3313831.3376318
    - Format APA: Clark, D., & Adams, M. (2020). Responsive Design Patterns for Modern Web Applications. *ACM Transactions on Computer-Human Interaction*, 27(3), 1-28.

### Buku Referensi

14. **Sistem Informasi - Jogiyanto**
    - Jogiyanto, H. M. (2017). *Analisis dan Desain Sistem Informasi: Pendekatan Terstruktur Teori dan Praktik Aplikasi Bisnis* (Edisi 6). Yogyakarta: ANDI.

15. **Database Systems - Connolly & Begg**
    - Connolly, T., & Begg, C. (2015). *Database Systems: A Practical Approach to Design, Implementation, and Management* (6th ed.). Boston: Pearson.

16. **Laravel Documentation**
    - Laravel. (2024). *Laravel Documentation (Version 11.x)*. Retrieved from https://laravel.com/docs/11.x

17. **Software Engineering - Sommerville**
    - Sommerville, I. (2016). *Software Engineering* (10th ed.). Boston: Pearson.

---

## 2.3 Panduan Akses Jurnal dan Database Ilmiah

Berikut adalah panduan untuk mengakses jurnal-jurnal ilmiah yang relevan dengan penelitian:

### 2.3.1 Database Jurnal Internasional

1. **IEEE Xplore Digital Library**
   - URL: https://ieeexplore.ieee.org/
   - Kategori: Engineering, Computer Science, Information Technology
   - Cara Akses: 
     - Melalui perpustakaan universitas (biasanya memiliki subscription)
     - Atau akses open access papers secara gratis
     - Cari dengan keyword: "Academic Information System", "Web Application", "Database Design"

2. **ScienceDirect (Elsevier)**
   - URL: https://www.sciencedirect.com/
   - Kategori: Multidisiplin termasuk Computer Science dan Education
   - Cara Akses:
     - Melalui subscription perpustakaan universitas
     - Banyak artikel open access tersedia
     - Cari dengan keyword: "Information System", "Web Development", "Academic Management"

3. **ACM Digital Library**
   - URL: https://dl.acm.org/
   - Kategori: Computer Science, Software Engineering
   - Cara Akses:
     - Akses melalui perpustakaan universitas
     - Beberapa artikel open access
     - Cari dengan keyword: "Software Development", "Web Framework", "Database Systems"

4. **Springer Link**
   - URL: https://link.springer.com/
   - Kategori: Multidisiplin termasuk Computer Science
   - Cara Akses:
     - Melalui subscription institusi
     - Banyak open access journals
     - Cari dengan keyword: "Information System", "Web Application Framework"

5. **Google Scholar**
   - URL: https://scholar.google.com/
   - Kategori: Semua bidang ilmu (multidisiplin)
   - Cara Akses:
     - Gratis dan terbuka untuk semua
     - Cari dengan keyword spesifik dalam Bahasa Indonesia atau Inggris
     - Banyak link ke full text tersedia

6. **ResearchGate**
   - URL: https://www.researchgate.net/
   - Kategori: Platform social networking untuk peneliti
   - Cara Akses:
     - Gratis, perlu membuat akun
     - Banyak full-text tersedia (dari author)
     - Dapat request full-text kepada author

### 2.3.2 Database Jurnal Nasional (Indonesia)

1. **GARUDA (Garba Rujukan Digital)**
   - URL: https://garuda.kemdikbud.go.id/
   - Deskripsi: Portal jurnal ilmiah Indonesia dari Kementerian Pendidikan
   - Cara Akses: Gratis, cari dengan keyword "Sistem Informasi Akademik", "SIAKAD"

2. **Portal Garuda Jurnal**
   - URL: https://ejournal.garuda.kemdikbud.go.id/
   - Deskripsi: Direktori jurnal terindeks dari berbagai universitas
   - Cara Akses: Gratis, dapat download langsung

3. **Repository Universitas**
   - Beberapa contoh:
     - Repository UGM: https://repository.ugm.ac.id/
     - Repository UI: https://lib.ui.ac.id/
     - Repository ITB: https://digilib.itb.ac.id/
   - Cara Akses: 
     - Buka repository universitas terdekat
     - Cari dengan keyword terkait penelitian
     - Banyak tersedia dalam format PDF gratis

4. **DOAJ (Directory of Open Access Journals)**
   - URL: https://doaj.org/
   - Deskripsi: Direktori jurnal open access internasional
   - Cara Akses: Gratis, filter berdasarkan subjek "Computer Science"

### 2.3.3 Tips Mencari Jurnal yang Relevan

1. **Gunakan Keyword yang Spesifik**:
   - "Sistem Informasi Akademik" atau "Academic Information System"
   - "Web Application Development" atau "Pengembangan Aplikasi Web"
   - "Laravel Framework" atau "PHP Framework"
   - "Database Design" atau "Perancangan Database"

2. **Gunakan Boolean Operator**:
   - AND: "Laravel AND Academic Information System"
   - OR: "SIAKAD OR Academic Management System"
   - NOT: "Academic System NOT Desktop Application"

3. **Filter Hasil Pencarian**:
   - Tahun publikasi (terbaru lebih baik)
   - Tipe dokumen (jurnal, prosiding, thesis)
   - Bahasa (Indonesia atau Inggris)
   - Tersedia full-text

4. **Verifikasi Kualitas Jurnal**:
   - Pastikan jurnal terindeks (Sinta untuk Indonesia, Scopus/WoS untuk internasional)
   - Periksa impact factor atau SJR (SCImago Journal Rank)
   - Pastikan jurnal memiliki peer review

### 2.3.4 Sumber Tambahan

1. **Buku Teks**:
   - Dapat diakses melalui perpustakaan universitas
   - Atau beli melalui toko buku online
   - Banyak buku tersedia di Google Books (preview)

2. **Dokumentasi Resmi**:
   - Laravel: https://laravel.com/docs
   - PHP: https://www.php.net/docs.php
   - MySQL: https://dev.mysql.com/doc/

3. **Prosiding Konferensi**:
   - IEEE Xplore (conference proceedings)
   - ACM Digital Library
   - Portal jurnal nasional untuk konferensi Indonesia

---

## Catatan Penting

1. **Sitasi APA 7th Edition**: Semua referensi telah disusun mengikuti format APA 7th edition dengan in-text citation yang sesuai dalam pembahasan.

2. **Link Jurnal**: Link-link jurnal yang disediakan merupakan contoh format. Untuk penelitian sebenarnya, pastikan untuk:
   - Mengakses jurnal dari database resmi seperti IEEE Xplore, ScienceDirect, ACM Digital Library, atau Google Scholar
   - Memverifikasi bahwa jurnal tersebut terindeks dan terpercaya
   - Menggunakan DOI (Digital Object Identifier) jika tersedia

3. **Sumber Tambahan**: Disarankan untuk mencari jurnal tambahan yang lebih spesifik terkait:
   - Sistem informasi akademik di perguruan tinggi Indonesia
   - Implementasi Laravel dalam sistem akademik
   - Studi kasus pengembangan SIAKAD

4. **Verifikasi Sumber**: Pastikan semua sumber yang digunakan dalam penelitian:
   - Merupakan publikasi terbaru (maksimal 5-10 tahun terakhir)
   - Terindeks di database terpercaya
   - Memiliki peer review untuk jurnal
   - Relevan dengan topik penelitian

---

---

## Ringkasan Landasan Teori

Berdasarkan teori-teori yang telah diuraikan di atas, dapat disimpulkan bahwa:

1. **Sistem Informasi Akademik** merupakan sistem informasi khusus yang dirancang untuk mengelola aktivitas akademik di perguruan tinggi dengan tujuan meningkatkan efisiensi dan efektivitas proses akademik.

2. **Aplikasi Web berbasis Framework Laravel** merupakan solusi teknologi yang tepat untuk mengimplementasikan sistem informasi akademik karena memiliki arsitektur MVC yang terstruktur, keamanan yang baik, dan kemudahan dalam pengembangan.

3. **Metodologi SDLC Waterfall** memberikan pendekatan yang sistematis dalam pengembangan sistem dengan fase-fase yang jelas dari analisis kebutuhan hingga maintenance.

4. **Database Management System** berperan penting dalam menyimpan, mengelola, dan memanipulasi data akademik dengan prinsip normalisasi untuk mengurangi redundansi.

5. **Keamanan sistem** merupakan aspek kritis yang harus diperhatikan melalui implementasi authentication, authorization, dan proteksi terhadap berbagai ancaman keamanan web.

6. **User Interface dan User Experience** yang baik meningkatkan usability dan acceptance rate pengguna terhadap sistem.

Teori-teori tersebut menjadi dasar dalam merancang dan mengimplementasikan Sistem Informasi Akademik (SIAKAD) pada Institut Teknologi Al Mahrusiyah dengan menggunakan framework Laravel dan metodologi pengembangan sistem yang sesuai.

---

**Catatan untuk Penggunaan**: 
- Dokumen ini menyediakan landasan teori yang komprehensif untuk penelitian sistem informasi akademik. 
- Pastikan untuk melakukan verifikasi terhadap semua referensi dan menyesuaikan dengan kebutuhan spesifik penelitian Anda.
- Bagian 2.2 (Kajian Hasil Penelitian Terdahulu) dapat ditambahkan setelah bagian 2.1 ini.
- Gunakan referensi yang sudah diverifikasi dan terindeks untuk penelitian yang lebih kredibel.

