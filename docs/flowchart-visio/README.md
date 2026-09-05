# Flowchart SIAKAD-BARU untuk Microsoft Visio

Dokumentasi lengkap untuk menggambar ~56 halaman flowchart di Microsoft Visio (notasi **Basic Flowchart**).

## Workflow terstruktur (Start / Process / Decision / End)

Untuk konversi cepat ke diagram alur, gunakan **[WORKFLOW-LIST.md](WORKFLOW-LIST.md)** — 11 workflow (WF-00 s/d WF-11) dalam format list siap copy ke Visio.

## Cara pakai

1. Buka Microsoft Visio → **Basic Flowchart** template.
2. Buat satu file `.vsdx` (mis. `SIAKAD-Flowchart.vsdx`).
3. Tambah **Page** per kode dokumen (contoh: `SYS-00`, `ADM-09`).
4. Buka file `.md` yang sesuai di folder ini; salin alur ke Visio menggunakan shape di [00-PANDUAN-SHAPE-VISIO.md](00-PANDUAN-SHAPE-VISIO.md).
5. Setiap kotak **Process** sudah dilabeli dengan **nama route Laravel** untuk traceability ke kode.

## Struktur folder

| Folder | Isi |
|--------|-----|
| [sys/](sys/) | SYS-00, SYS-01, SYS-02 — autentikasi & komunikasi |
| [cross/](cross/) | X-01 … X-05b — alur lintas role |
| [admin/](admin/) | ADM-00 … ADM-22 |
| [dosen/](dosen/) | DSN-00 … DSN-09 (+ sub ujian) |
| [mahasiswa/](mahasiswa/) | MHS-00 … MHS-13 |
| [_TEMPLATE-CRUD.md](_TEMPLATE-CRUD.md) | Template duplikat untuk modul CRUD admin |

## Daftar halaman Visio

### Sistem umum
- [SYS-00 Login & password](sys/SYS-00.md)
- [SYS-01 Dashboard redirect](sys/SYS-01.md)
- [SYS-02 Komunikasi & profil](sys/SYS-02.md)

### Lintas role
- [X-01 KRS](cross/X-01.md)
- [X-02 Presensi kelas](cross/X-02.md)
- [X-03 Nilai → KHS](cross/X-03.md)
- [X-04 Pembayaran](cross/X-04.md)
- [X-05 Ujian anti-cheat](cross/X-05.md)
- [X-05a Aturan anti-cheat dosen](cross/X-05a.md)
- [X-05b Deteksi anti-cheat mahasiswa](cross/X-05b.md)

### Admin (ADM-00 … ADM-22)
Lihat [admin/README.md](admin/README.md)

### Dosen (DSN-00 … DSN-09)
Lihat [dosen/README.md](dosen/README.md)

### Mahasiswa (MHS-00 … MHS-13)
Lihat [mahasiswa/README.md](mahasiswa/README.md)

## Urutan menggambar (disarankan)

1. SYS-00 → SYS-01 → SYS-02  
2. X-01 → X-05  
3. ADM-00 / DSN-00 / MHS-00 (navigasi)  
4. Modul detail per role  
5. Duplikasi [_TEMPLATE-CRUD.md](_TEMPLATE-CRUD.md) untuk halaman CRUD admin

## Sumber kode

- Routes: `routes/web.php`
- Sidebar: `resources/views/layouts/sidebar.blade.php`
- Middleware role: `App\Http\Middleware\RoleMiddleware`
