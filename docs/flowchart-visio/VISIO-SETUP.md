# Membuat File Visio dari Dokumentasi Ini

## Langkah 1 — File baru

1. Buka **Microsoft Visio**.
2. Pilih template **Basic Flowchart**.
3. Simpan sebagai `SIAKAD-Flowchart.vsdx`.

## Langkah 2 — Tambah halaman (Page)

Buat page dengan nama persis seperti kode dokumen:

- `SYS-00`, `SYS-01`, `SYS-02`
- `X-01`, `X-02`, `X-03`, `X-04`, `X-05`, `X-05a`, `X-05b`
- `ADM-00` … `ADM-22`
- `DSN-00` … `DSN-09c`, `DSN-10`
- `MHS-00` … `MHS-13`

Total: **~61 halaman**.

## Langkah 3 — Stencil

**More Shapes → Basic Flowchart Shapes**

Lihat [00-PANDUAN-SHAPE-VISIO.md](00-PANDUAN-SHAPE-VISIO.md).

## Langkah 4 — Isi tiap page

1. Buka file `.md` yang sesuai (contoh: `admin/ADM-09.md`).
2. Untuk setiap baris tabel **Shape mapping**, tarik shape ke kanvas.
3. Di dalam shape **Process**, ketik 2 baris:
   - Baris 1: deskripsi singkat (Bahasa Indonesia)
   - Baris 2: `route: admin.krs.approve`
4. Hubungkan dengan **Dynamic Connector**; label cabang decision.
5. Tambah **Off-page Connector** ke halaman terkait (lihat link di setiap `.md`).

## Langkah 5 — Swimlane (X-01, X-02, X-04, X-05)

1. Insert → **Cross-Functional Flowchart**.
2. Tambah lane: Mahasiswa | Sistem | Admin (atau Dosen).
3. Ikuti diagram di folder [cross/](cross/).

## Langkah 6 — Duplikasi CRUD

1. Gambar sekali dari [_TEMPLATE-CRUD.md](_TEMPLATE-CRUD.md).
2. Duplicate page → rename `ADM-03`, `ADM-04`, dll.
3. Ganti label route sesuai [ROUTE-INDEX.md](ROUTE-INDEX.md).

## Langkah 7 — Export

- PDF per role: Admin (ADM-*), Dosen (DSN-*), Mahasiswa (MHS-*)
- Simpan `.vsdx` di folder dokumentasi proyek

## Checklist selesai

Gunakan [CHECKLIST-VISIO.md](CHECKLIST-VISIO.md) untuk menandai halaman yang sudah digambar.
