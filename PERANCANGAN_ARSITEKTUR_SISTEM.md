# 1. Perancangan Arsitektur Sistem

## 1.1 Gambaran Umum Sistem

Sistem Informasi Akademik (SIAKAD) merupakan sistem berbasis web yang dirancang untuk mengelola proses akademik di perguruan tinggi secara terintegrasi dan efisien. Sistem ini dibangun dengan arsitektur client-server yang memungkinkan pengguna mengakses sistem melalui browser web (client) yang terhubung ke server aplikasi.

SIAKAD dirancang dengan pendekatan three-tier architecture yang terdiri dari tiga layer utama. Layer pertama adalah Presentation Layer atau Layer Presentasi yang menangani antarmuka pengguna berbasis web menggunakan HTML, CSS, dan JavaScript. Layer kedua adalah Application Layer atau Layer Aplikasi yang merupakan server aplikasi yang menangani logika bisnis menggunakan framework Laravel 11. Layer ketiga adalah Data Layer atau Layer Data yang berupa database untuk menyimpan semua data akademik menggunakan SQLite atau MySQL.

## 1.2 Hubungan Antara Komponen Sistem

### 1.2.1 Hubungan Pengguna dengan Sistem

Sistem SIAKAD memiliki tiga jenis pengguna utama dengan peran dan hak akses yang berbeda, yaitu Admin, Dosen, dan Mahasiswa.

Admin merupakan pengguna dengan fungsi mengelola seluruh operasional sistem dan memiliki full access ke semua modul dan data. Tugas utama admin meliputi mengelola master data seperti Program Studi, Mahasiswa, Dosen, Mata Kuliah, Semester, dan Jadwal Kuliah. Admin juga bertanggung jawab untuk menyetujui atau menolak KRS (Kartu Rencana Studi) mahasiswa, membuat dan mengelola pengumuman, melihat laporan dan statistik sistem, serta mengkonfigurasi pengaturan sistem.

Dosen merupakan pengguna dengan fungsi mengelola proses pembelajaran dan penilaian, dengan akses yang terbatas pada modul yang relevan dengan tugas mengajar. Tugas utama dosen meliputi melihat jadwal mengajar, menginput nilai mahasiswa yang mencakup Nilai Tugas, UTS, dan UAS, melihat daftar mahasiswa di kelas yang diampu, serta melihat pengumuman yang ditujukan untuk dosen.

Mahasiswa merupakan pengguna dengan fungsi mengakses informasi akademik pribadi, dengan akses yang terbatas pada data akademik pribadi. Tugas utama mahasiswa meliputi melihat jadwal kuliah hari ini dan minggu ini, mengambil KRS untuk semester aktif, melihat KHS per semester, melihat transkrip nilai dan IPK, serta melihat pengumuman yang ditujukan untuk mahasiswa.

### 1.2.2 Arsitektur Sistem Berbasis Web

Sistem SIAKAD menggunakan arsitektur MVC (Model-View-Controller) yang memisahkan logika bisnis, presentasi, dan data. Pada layer pertama, terdapat client berupa browser web yang digunakan oleh ketiga jenis pengguna yaitu Mahasiswa, Dosen, dan Admin. Browser web ini menampilkan antarmuka menggunakan HTML, CSS, dan JavaScript.

Pada layer kedua, terdapat web server berbasis Laravel yang terdiri dari beberapa komponen. Routing Layer menangani routing request melalui web.php untuk web routes dan api.php untuk API routes. Middleware Layer berfungsi untuk melakukan pengecekan autentikasi, otorisasi berbasis role (Admin, Dosen, Mahasiswa), dan proteksi CSRF. Controller Layer menangani business logic melalui berbagai controller seperti AdminController, DosenController, MahasiswaController, DashboardController, dan AuthController untuk login dan logout. Model Layer berfungsi sebagai data access layer yang berisi model-model seperti User, Mahasiswa, Dosen, MataKuliah, KRS, Nilai, JadwalKuliah, dan lain-lain.

Pada layer ketiga, terdapat database server yang menyimpan data akademik, komunikasi, pembelajaran daring, pembayaran, dan jejak audit. Tabel master meliputi users, mahasiswas, dosens, prodis, mata_kuliahs, semesters, dan jadwal_kuliahs. Tabel transaksional akademik meliputi krs, nilais, pengumumans, kalender_akademik, letter_grades, dan template_krs_khs. Tabel komunikasi meliputi conversations, messages, forum_topics, forum_posts, questions, answers, dan notifikasis. Tabel pembelajaran meliputi presensis, qr_code_sessions, class_sessions, class_attendances, assignments, assignment_submissions, exams, exam_questions, exam_sessions, exam_answers, dan exam_violation_rules. Tabel pembayaran meliputi banks dan payments. Tabel keamanan meliputi audit_logs, personal_access_tokens, sessions, dan password_reset_tokens. Komunikasi antara application layer dan data layer menggunakan Eloquent ORM atau Query Builder. Rincian ERD dan kamus data terdapat pada dokumen DESAIN_BASIS_DATA_ERD_BAB_III.md.

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT (Browser Web)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Mahasiswa  │  │    Dosen     │  │    Admin     │      │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘      │
│         │                  │                  │              │
│         └──────────────────┼──────────────────┘              │
│                            │                                  │
│                    ┌───────▼────────┐                        │
│                    │  Web Browser   │                        │
│                    │  (HTML/CSS/JS) │                        │
│                    └───────┬────────┘                        │
└────────────────────────────┼──────────────────────────────────┘
                             │
                             │ HTTP/HTTPS Request
                             │
┌────────────────────────────▼──────────────────────────────────┐
│                    WEB SERVER (Laravel)                       │
│  ┌──────────────────────────────────────────────────────┐    │
│  │              ROUTING LAYER                           │    │
│  │  - web.php (Web Routes)                              │    │
│  │  - api.php (API Routes)                              │    │
│  └───────────────┬──────────────────────────────────────┘    │
│                  │                                            │
│  ┌───────────────▼──────────────────────────────────────┐    │
│  │        MIDDLEWARE LAYER                               │    │
│  │  - Authentication Middleware                          │    │
│  │  - Role Middleware (Admin, Dosen, Mahasiswa)         │    │
│  │  - CSRF Protection                                    │    │
│  └───────────────┬──────────────────────────────────────┘    │
│                  │                                            │
│  ┌───────────────▼──────────────────────────────────────┐    │
│  │        CONTROLLER LAYER (Business Logic)             │    │
│  │  - AdminController, DosenController,                 │    │
│  │    MahasiswaController, DashboardController          │    │
│  │  - AuthController (Login, Logout)                    │    │
│  └───────────────┬──────────────────────────────────────┘    │
│                  │                                            │
│  ┌───────────────▼──────────────────────────────────────┐    │
│  │        MODEL LAYER (Data Access)                     │    │
│  │  - User, Mahasiswa, Dosen, MataKuliah,              │    │
│  │    KRS, Nilai, JadwalKuliah, dll                    │    │
│  └───────────────┬──────────────────────────────────────┘    │
└──────────────────┼────────────────────────────────────────────┘
                   │
                   │ Eloquent ORM / Query Builder
                   │
┌──────────────────▼────────────────────────────────────────────┐
│                    DATABASE SERVER                            │
│  ┌──────────────────────────────────────────────────────┐    │
│  │              DATABASE (SQLite/MySQL)                 │    │
│  │                                                       │    │
│  │  Master: users, prodis, mahasiswas, dosens,          │    │
│  │          mata_kuliahs, semesters, jadwal_kuliahs     │    │
│  │  Akademik: krs, nilais, pengumumans, kalender        │    │
│  │  Komunikasi: conversations, messages, forum_*,       │    │
│  │              questions, answers, notifikasis         │    │
│  │  Pembelajaran: presensis, qr_code_sessions,          │    │
│  │                class_sessions, class_attendances,    │    │
│  │                assignments, assignment_submissions,  │    │
│  │                exams, exam_questions, exam_sessions, │    │
│  │                exam_answers, exam_violation_rules    │    │
│  │  Pembayaran: banks, payments                         │    │
│  │  Keamanan: audit_logs, tokens, sessions              │    │
│  └──────────────────────────────────────────────────────┘    │
└───────────────────────────────────────────────────────────────┘
```

### 1.2.3 Alur Interaksi Pengguna dengan Sistem

Alur login dan autentikasi dimulai ketika pengguna mengakses sistem melalui web browser. Sistem kemudian menampilkan halaman login dimana pengguna memasukkan email dan password. Server memverifikasi kredensial terhadap database, dan jika valid, server membuat session dan menentukan role pengguna. Setelah itu, sistem mengarahkan pengguna ke dashboard sesuai dengan role mereka, baik sebagai Admin, Dosen, maupun Mahasiswa.

Alur request-response dimulai ketika pengguna melakukan aksi seperti mengambil KRS atau input nilai. Laravel Router menerima request dan mengarahkan ke Controller yang sesuai. Sistem kemudian melakukan pengecekan autentikasi dan otorisasi melalui middleware dengan role-based access control. Controller memproses logika bisnis dan berinteraksi dengan Model. Model mengakses database melalui Eloquent ORM untuk melakukan operasi CRUD (Create, Read, Update, Delete). Data kemudian dikembalikan melalui Controller ke View. View yang menggunakan Blade Template merender HTML dengan data yang diterima, dan akhirnya browser menampilkan hasil kepada pengguna.

### 1.2.4 Hubungan Server dengan Database

Konfigurasi database menggunakan SQLite untuk development atau MySQL untuk production. Sistem menggunakan Eloquent ORM sebagai abstraksi database yang memudahkan interaksi dengan database. Pengelolaan struktur database dilakukan menggunakan Laravel Migrations untuk version control, memastikan konsistensi struktur database di berbagai lingkungan.

Database menggunakan relasi antar tabel untuk menjaga integritas data. Relasi User dengan Mahasiswa atau Dosen menggunakan pola One-to-One, dimana setiap user dapat memiliki satu profil mahasiswa atau dosen. Relasi Program Studi dengan Mahasiswa dan Mata Kuliah menggunakan pola One-to-Many. Relasi KRS merepresentasikan Many-to-Many antara Mahasiswa dan Jadwal Kuliah. Relasi Nilai terhubung ke KRS, Mahasiswa, Jadwal Kuliah, dan Dosen. Modul komunikasi memakai One-to-Many dari User ke Forum Topic, Forum Post, Message, Question, Answer, dan Notifikasi; Conversation menghubungkan dua User dan memiliki banyak Message. Modul pembelajaran memakai One-to-Many dari Jadwal Kuliah ke Presensi, Assignment, dan Exam; Assignment ke Assignment Submission bersifat One-to-Many dengan unik per mahasiswa; Exam ke Exam Session dan Exam Question bersifat One-to-Many, Exam Session ke Exam Answer One-to-Many, serta Exam ke Exam Violation Rule bersifat One-to-One. Audit Log bersifat polimorfik terhadap model yang diubah dan terhubung ke User secara nullable.

Keamanan database diimplementasikan melalui beberapa mekanisme. Password di-hash menggunakan bcrypt untuk mencegah akses tidak sah. Proteksi terhadap SQL Injection dilakukan dengan menggunakan Eloquent ORM yang sudah terproteksi secara built-in. Foreign Key Constraints digunakan untuk menjaga integritas referensial antar tabel. Untuk operasi kompleks, sistem menggunakan database transaction untuk memastikan konsistensi data.

### 1.2.5 Arsitektur API untuk Mobile Application

Selain sistem berbasis web, SIAKAD juga menyediakan RESTful API menggunakan Laravel Sanctum untuk autentikasi berbasis token, yang memungkinkan pengembangan aplikasi mobile. Aplikasi mobile seperti yang dibangun dengan Flutter atau React Native dapat berkomunikasi dengan server melalui HTTP/HTTPS request dengan Bearer Token Authentication. API routes yang tersedia meliputi POST /api/login untuk autentikasi, GET /api/dashboard untuk dashboard per role, GET /api/mahasiswa/krs untuk mengakses KRS mahasiswa, POST /api/dosen/nilai untuk input nilai oleh dosen, dan berbagai endpoint lainnya. API menggunakan Laravel Sanctum Token untuk autentikasi dan menggunakan controllers dan models yang sama dengan web application, sehingga business logic dapat digunakan bersama. Database yang digunakan juga sama dengan web application, memastikan konsistensi data antara web dan mobile.

```
┌──────────────────┐
│  Mobile App      │
│  (Flutter/RN)    │
└────────┬─────────┘
         │
         │ HTTP/HTTPS Request
         │ (Bearer Token Authentication)
         │
┌────────▼────────────────────────────────────┐
│  API ROUTES (api.php)                       │
│  - POST /api/login                          │
│  - GET /api/dashboard                       │
│  - GET /api/mahasiswa/krs                   │
│  - POST /api/dosen/nilai                    │
│  - dll...                                    │
└────────┬────────────────────────────────────┘
         │
         │ Laravel Sanctum Token
         │
┌────────▼────────────────────────────────────┐
│  Same Controllers & Models                  │
│  (Shared Business Logic)                    │
└────────┬────────────────────────────────────┘
         │
         │
┌────────▼────────────────────────────────────┐
│  Database (Same as Web)                     │
└─────────────────────────────────────────────┘
```

### 1.2.6 Keamanan Sistem Informasi

Keamanan sistem informasi dalam SIAKAD diimplementasikan melalui beberapa lapisan proteksi untuk menjaga confidentiality, integrity, dan availability data serta sistem. Sistem menggunakan mekanisme autentikasi yang memverifikasi identitas pengguna melalui email dan password yang di-hash menggunakan algoritma bcrypt. Setelah proses login berhasil, sistem melakukan session regeneration untuk mencegah session hijacking, dimana session ID lama di-invalidate dan diganti dengan session ID baru. Proses autentikasi ini memastikan bahwa hanya pengguna yang memiliki kredensial valid yang dapat mengakses sistem.

Sistem juga mengimplementasikan authorization melalui role-based access control (RBAC) yang membatasi akses pengguna berdasarkan peran mereka. Middleware RoleMiddleware mengecek role pengguna sebelum memberikan akses ke route tertentu, sehingga admin hanya dapat mengakses route admin, dosen hanya dapat mengakses route dosen, dan mahasiswa hanya dapat mengakses route mahasiswa. Setiap request yang masuk ke sistem akan melalui middleware layer yang melakukan pengecekan autentikasi dan otorisasi sebelum diteruskan ke controller.

Proteksi terhadap serangan Cross-Site Request Forgery (CSRF) dilakukan melalui CSRF token yang di-generate secara otomatis oleh Laravel untuk setiap form. Token ini di-verify pada setiap POST request untuk memastikan bahwa request berasal dari aplikasi yang legitimate, bukan dari sumber eksternal yang berbahaya. Proteksi terhadap SQL Injection dilakukan melalui Eloquent ORM yang menggunakan parameterized queries, sehingga input dari pengguna tidak dapat dieksekusi sebagai bagian dari SQL query secara langsung.

Keamanan data di database dijaga melalui beberapa mekanisme. Password disimpan dalam bentuk hash menggunakan bcrypt dengan cost factor yang tinggi, sehingga tidak dapat di-reverse menjadi plain text. Foreign key constraints digunakan untuk menjaga integritas referensial antar tabel, mencegah data yang tidak konsisten. Database transactions digunakan untuk operasi kompleks yang melibatkan multiple queries, memastikan bahwa semua operasi berhasil atau tidak ada perubahan yang dilakukan sama sekali (atomicity).

Keamanan session dikelola melalui Laravel session management yang menyimpan session data di database atau file storage. Session memiliki expiry time yang membatasi durasi aktifitas pengguna tanpa melakukan aktivitas, dan session dapat di-regenerate setelah login untuk mencegah session fixation attack. Sistem juga menggunakan HTTPS untuk enkripsi data yang ditransmisikan antara client dan server, mencegah man-in-the-middle attack.

Proteksi terhadap Cross-Site Scripting (XSS) dilakukan melalui automatic escaping dalam Blade templating engine, dimana semua output ke view secara otomatis di-escape untuk mencegah eksekusi script berbahaya. Input validation dilakukan pada setiap form untuk memastikan data yang masuk sesuai dengan format yang diharapkan, mencegah injection attack melalui input yang tidak valid. Sistem juga mengimplementasikan audit logging untuk mencatat aktivitas penting seperti login, perubahan data, dan akses ke modul sensitif, memungkinkan administrator untuk melakukan monitoring dan investigasi jika terjadi insiden keamanan.

### 1.2.7 User Interface dan User Experience

Sistem SIAKAD dirancang dengan memperhatikan aspek User Interface (UI) dan User Experience (UX) untuk memberikan pengalaman yang mudah, nyaman, dan efisien bagi pengguna. Antarmuka pengguna dibangun menggunakan framework Tailwind CSS yang menyediakan utility classes untuk styling yang konsisten dan modern. Sistem menggunakan Blade templating engine dari Laravel untuk memisahkan logic dan presentation, memungkinkan pengembangan UI yang lebih terstruktur dan mudah dirawat.

Prinsip simplicity diterapkan dalam desain interface dengan menghindari elemen yang tidak diperlukan dan fokus pada fungsionalitas utama. Layout menggunakan komponen yang konsisten seperti sidebar navigation untuk menu utama, header untuk informasi pengguna dan notifikasi, dan content area untuk menampilkan informasi utama. Setiap halaman memiliki struktur yang seragam sehingga pengguna dapat dengan mudah memahami dan menavigasi sistem tanpa perlu belajar ulang untuk setiap halaman.

Konsistensi desain dijaga melalui penggunaan design system yang seragam. Komponen UI seperti button, form input, card, table, dan modal menggunakan styling yang konsisten di seluruh aplikasi. Warna utama menggunakan skema warna biru (blue) yang memberikan kesan profesional dan dapat dipercaya. Sistem juga menggunakan icon set yang konsisten untuk memperjelas fungsi setiap elemen dan mempercepat pemahaman pengguna.

Feedback kepada pengguna disediakan melalui berbagai mekanisme. Pesan sukses ditampilkan ketika operasi berhasil dilakukan, pesan error ditampilkan ketika terjadi kesalahan, dan loading indicator ditampilkan ketika sistem sedang memproses request. Notifikasi real-time digunakan untuk menginformasikan pengguna tentang peristiwa penting seperti pengumuman baru, nilai baru, atau status KRS yang berubah.

Error prevention dilakukan melalui validasi input yang dilakukan baik di sisi client menggunakan JavaScript maupun di sisi server menggunakan Laravel validation. Pesan error yang jelas dan spesifik membantu pengguna memahami kesalahan yang terjadi dan bagaimana memperbaikinya. Sistem juga menggunakan konfirmasi dialog untuk operasi yang bersifat destruktif seperti menghapus data, mencegah pengguna melakukan kesalahan yang tidak dapat di-undo.

Responsive design diimplementasikan untuk memastikan aplikasi dapat diakses dengan baik dari berbagai perangkat dan ukuran layar. Layout menggunakan CSS Grid dan Flexbox yang fleksibel, sehingga dapat menyesuaikan diri dengan ukuran viewport. Sistem menggunakan mobile-first approach dimana desain dasar dirancang untuk perangkat mobile, kemudian ditingkatkan untuk perangkat yang lebih besar. Menu sidebar dapat di-collapse pada perangkat mobile untuk menghemat ruang layar, dan form layout disesuaikan untuk memberikan pengalaman input yang optimal pada layar kecil.

Aksesibilitas diperhatikan melalui penggunaan semantic HTML, kontras warna yang cukup untuk readability, dan struktur heading yang hierarkis untuk screen reader. Form menggunakan label yang jelas dan error message yang dapat diakses oleh assistive technology. Sistem juga menyediakan keyboard navigation untuk pengguna yang tidak dapat menggunakan mouse, dengan focus indicator yang jelas untuk menunjukkan elemen yang sedang aktif.

Performance optimization dilakukan melalui lazy loading untuk gambar, code splitting untuk JavaScript, dan caching untuk assets static. Vite digunakan sebagai build tool yang melakukan bundling dan minification untuk mengurangi ukuran file dan meningkatkan loading time. Sistem menggunakan CDN untuk library eksternal dan melakukan optimasi CSS dan JavaScript untuk mengurangi waktu render halaman.

### 1.2.8 Application Programming Interface (API)

Sistem SIAKAD menyediakan RESTful API (Application Programming Interface) menggunakan Laravel Sanctum untuk mendukung integrasi dengan aplikasi mobile dan sistem eksternal lainnya. API dirancang mengikuti standar REST dimana setiap endpoint merepresentasikan resource tertentu dan menggunakan HTTP method yang sesuai untuk operasi CRUD (Create, Read, Update, Delete). Base URL untuk API adalah /api dan semua endpoint mengembalikan response dalam format JSON.

Autentikasi API dilakukan menggunakan token-based authentication melalui Laravel Sanctum. Pengguna melakukan login melalui endpoint POST /api/login dengan mengirimkan email dan password, dan sistem mengembalikan access token yang harus disertakan dalam header Authorization dengan format Bearer {token} untuk setiap request selanjutnya. Token ini digunakan untuk mengidentifikasi dan mengotentikasi pengguna, menggantikan session-based authentication yang digunakan pada web application. Setelah pengguna logout melalui endpoint POST /api/logout, token akan dihapus dan tidak dapat digunakan lagi.

API menyediakan endpoint untuk semua fitur utama sistem yang dapat diakses melalui web. Endpoint GET /api/dashboard mengembalikan data dashboard yang disesuaikan dengan role pengguna, memberikan statistik dan informasi ringkas yang relevan. Endpoint untuk mahasiswa meliputi GET /api/mahasiswa/krs untuk mengambil KRS mahasiswa, GET /api/mahasiswa/khs untuk mengambil KHS per semester, GET /api/mahasiswa/jadwal untuk melihat jadwal kuliah, dan POST /api/mahasiswa/krs untuk membuat KRS baru. Endpoint untuk dosen meliputi GET /api/dosen/jadwal untuk melihat jadwal mengajar, GET /api/dosen/nilai untuk melihat daftar nilai, dan POST /api/dosen/nilai untuk menginput nilai mahasiswa.

API mengimplementasikan role-based access control yang sama dengan web application, dimana setiap endpoint mengecek role pengguna sebelum memberikan akses. Middleware auth:sanctum digunakan untuk memastikan bahwa request datang dari pengguna yang terautentikasi, dan middleware role digunakan untuk membatasi akses berdasarkan role pengguna. Response API mengikuti format standar dengan struktur yang konsisten, menggunakan field success untuk menandai keberhasilan atau kegagalan request, message untuk pesan yang informatif, dan data untuk payload yang sebenarnya.

Error handling dalam API dilakukan melalui HTTP status code yang sesuai, seperti 200 untuk sukses, 201 untuk resource yang berhasil dibuat, 400 untuk bad request, 401 untuk unauthorized, 403 untuk forbidden, 404 untuk not found, dan 500 untuk server error. Pesan error yang dikembalikan bersifat informatif tetapi tidak mengungkapkan informasi sensitif tentang struktur sistem atau database. Validasi input dilakukan menggunakan Laravel validation yang sama dengan web application, memastikan konsistensi validasi antara web dan API.

API menggunakan shared business logic dengan web application, dimana controller dan model yang sama digunakan untuk web dan API. Hal ini memastikan konsistensi logika bisnis dan memudahkan maintenance karena perubahan logika bisnis hanya perlu dilakukan di satu tempat. Database yang digunakan juga sama dengan web application, memastikan bahwa data yang diakses melalui API sama dengan data yang diakses melalui web interface.

Rate limiting dapat diimplementasikan pada API endpoint untuk mencegah abuse dan memastikan fair usage. Response API menggunakan pagination untuk endpoint yang mengembalikan list data, membatasi jumlah data yang dikembalikan per request dan menyediakan metadata untuk navigasi ke halaman berikutnya. API documentation tersedia untuk membantu developer yang ingin mengintegrasikan sistem mereka dengan SIAKAD, menjelaskan setiap endpoint, parameter yang dibutuhkan, format request dan response, serta contoh penggunaan.

## 1.3 Keunggulan Arsitektur Sistem

Arsitektur sistem SIAKAD memiliki beberapa keunggulan yang mendukung pengembangan dan pengoperasian sistem. Arsitektur MVC memungkinkan pengembangan fitur baru tanpa mengganggu komponen lain, sehingga sistem memiliki skalabilitas yang baik. Pemisahan concerns membuat kode mudah dirawat dan di-debug, memberikan maintainability yang tinggi. Middleware layer memberikan proteksi di level aplikasi untuk meningkatkan security sistem. Sistem juga memiliki fleksibilitas tinggi karena mendukung akses melalui web dan mobile application melalui API. Eloquent ORM dengan query optimization dan caching memberikan performa yang optimal. Role-based access memberikan interface yang sesuai dengan kebutuhan setiap pengguna, sehingga meningkatkan user experience.

## 1.4 Teknologi yang Digunakan

Teknologi yang digunakan dalam pengembangan sistem SIAKAD terdiri dari beberapa komponen utama. Backend Framework menggunakan Laravel 11 yang dibangun dengan PHP. Frontend menggunakan Blade Templates sebagai templating engine, Tailwind CSS 4 untuk styling, dan JavaScript untuk interaktivitas. Database menggunakan SQLite untuk development dan MySQL untuk production. Authentication menggunakan Laravel Session untuk web application dan Laravel Sanctum untuk API. Build Tool menggunakan Vite untuk bundling dan optimasi assets. Server dapat menggunakan Apache atau Nginx dengan PHP-FPM untuk menjalankan aplikasi Laravel.

## 1.5 Kesimpulan

Arsitektur SIAKAD dirancang dengan pendekatan modern yang memisahkan concerns dengan jelas antara presentation layer, application layer, dan data layer. Sistem ini mampu melayani tiga jenis pengguna yaitu Admin, Dosen, dan Mahasiswa dengan hak akses yang berbeda sesuai dengan peran masing-masing. Sistem juga mendukung akses melalui web browser dan aplikasi mobile melalui RESTful API, memberikan fleksibilitas kepada pengguna untuk mengakses sistem dari berbagai platform. Hubungan antara pengguna, sistem web, server aplikasi, dan database terjalin secara terstruktur dengan menggunakan pola MVC dan ORM untuk memastikan keamanan, performa, dan kemudahan maintenance sistem secara keseluruhan.
