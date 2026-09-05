# Indeks Route per Halaman Visio

Referensi cepat: setiap kotak **Process** di Visio harus memuat nama route di bawah ini.

## SYS

| Page | Routes |
|------|--------|
| SYS-00 | `login`, `logout`, `password.request`, `password.email`, `password.reset`, `password.update` |
| SYS-01 | `dashboard`, `admin.dashboard`, `dosen.dashboard`, `mahasiswa.dashboard` |
| SYS-02 | `notifikasi.*`, `chat.*`, `forum.*`, `qna.*`, `profile.*` |

## Cross (X)

| Page | Routes utama |
|------|----------------|
| X-01 | `mahasiswa.krs.store`, `admin.krs.approve`, `admin.krs.reject` |
| X-02 | `dosen.presensi-kelas.*`, `mahasiswa.presensi-kelas.*` |
| X-03 | `dosen.nilai.*`, `mahasiswa.khs.index`, `mahasiswa.transcript.*` |
| X-04 | `payment.*`, `admin.payment.*`, `payment.xendit.webhook` |
| X-05 | `dosen.exam.store`, `dosen.exam.violation-rules*`, `dosen.exam.violations`, `mahasiswa.exam.start`, `mahasiswa.exam.take`, `mahasiswa.exam.log-violation`, `mahasiswa.exam.submit` |
| X-05a | `dosen.exam.create`, `dosen.exam.store`, `dosen.exam.violation-rules`, `dosen.exam.violation-rules.update`, `dosen.exam.violations` |
| X-05b | `mahasiswa.exam.start`, `mahasiswa.exam.take`, `mahasiswa.exam.log-violation`, `mahasiswa.exam.save-answer`, `mahasiswa.exam.submit` |

## Admin (ADM)

| Page | Resource / routes |
|------|-------------------|
| ADM-00 | Navigasi — lihat [admin/ADM-00.md](admin/ADM-00.md) |
| ADM-01 | `admin.dashboard` |
| ADM-02 | `admin.admin.*` |
| ADM-03 | `admin.prodi.*` |
| ADM-04 | `admin.mahasiswa.*` |
| ADM-05 | `admin.dosen.*` |
| ADM-06 | `admin.mata-kuliah.*` |
| ADM-07 | `admin.semester.*` |
| ADM-08 | `admin.jadwal-kuliah.*` |
| ADM-09 | `admin.krs.index`, `admin.krs.approve`, `admin.krs.reject` |
| ADM-10 | `admin.pengumuman.*` |
| ADM-11 | `admin.template-krs-khs.*` |
| ADM-12 | `admin.generate-krs-khs.*` |
| ADM-13 | `admin.kalender-akademik.*` |
| ADM-14 | `admin.active-users.index` |
| ADM-15 | `admin.backup.*` |
| ADM-16 | `admin.system-settings.*` |
| ADM-17 | `admin.audit-log.*` |
| ADM-18 | `admin.statistik-presensi.index` |
| ADM-19 | `admin.statistik-presensi-per-prodi.index` |
| ADM-20 | `admin.laporan.pembayaran.*` |
| ADM-21 | `admin.laporan.akademik.*` |
| ADM-22 | `admin.payment.*`, `admin.bank.*` |

## Dosen (DSN)

| Page | Routes |
|------|--------|
| DSN-00 | Navigasi `dosen.*` |
| DSN-01 | `dosen.dashboard` |
| DSN-02 | `dosen.nilai.*` |
| DSN-03 | `dosen.presensi.*` |
| DSN-04 | `dosen.presensi-kelas.*` |
| DSN-05 | `dosen.statistik-presensi.index` |
| DSN-06 | `dosen.statistik-presensi-per-prodi.index` |
| DSN-07 | `dosen.kalender-akademik.*` |
| DSN-08 | `dosen.assignment.*` |
| DSN-09a | `dosen.exam.{index,create,store,show,edit,update,destroy}` |
| DSN-09b | `dosen.exam.add-question`, `generate-questions`, `update-question`, `delete-question`, `results`, `grade-session*` |
| DSN-09c | `dosen.exam.ongoing`, `finished`, `active-students`, `violation-*`, `all-violations` |
| DSN-10 | SYS-02 |

## Mahasiswa (MHS)

| Page | Routes |
|------|--------|
| MHS-00 | Navigasi |
| MHS-01 | `mahasiswa.dashboard` |
| MHS-02 | `mahasiswa.krs.*` |
| MHS-03 | `mahasiswa.khs.index` |
| MHS-04 | `mahasiswa.transcript.*` |
| MHS-05 | `mahasiswa.export.krs`, `mahasiswa.export.khs` |
| MHS-06 | `mahasiswa.generate-krs-khs.*` |
| MHS-07 | `mahasiswa.presensi.index` |
| MHS-08 | `mahasiswa.presensi-kelas.*` |
| MHS-09 | `mahasiswa.statistik-keaktifan.index` |
| MHS-10 | `mahasiswa.kalender-akademik.*` |
| MHS-11 | `mahasiswa.assignment.*` |
| MHS-12 | `mahasiswa.exam.*` |
| MHS-13 | `payment.*` |

Sumber lengkap: `routes/web.php` (baris 34–366).
