# Daftar Workflow SIAKAD-BARU (Siap Konversi ke Visio)

Setiap langkah bertipe **Start**, **Process**, **Decision** (Ya/Tidak), atau **End**.  
Route Laravel dicantumkan untuk label kotak Process di Visio.

**Legenda shape Visio:**

| Tipe di list | Shape Visio |
|--------------|-------------|
| **START** | Terminator (oval) — "Mulai" |
| **END** | Terminator (oval) — "Selesai" |
| **PROCESS** | Process (persegi) |
| **DECISION** | Decision (belah ketupat) + panah Ya / Tidak |

**Urutan menggambar:** WF-00 → WF-01 → WF-02 … WF-05 (swimlane) → WF-06 s/d WF-11 → WF-12 (anti-cheat, swimlane).

---

## WF-00 — Masuk Sistem & Autentikasi

**Visio page disarankan:** `SYS-00`

| # | Tipe | Langkah | Route / catatan |
|---|------|---------|-----------------|
| 1 | **START** | Pengguna membuka aplikasi | `GET /` |
| 2 | **PROCESS** | Sistem redirect ke halaman login | → `login` |
| 3 | **DECISION** | Pengguna memilih aksi? | |
| | | **Ya → Login** | lanjut #4 |
| | | **Tidak → Lupa password** | lanjut #15 |
| 4 | **PROCESS** | Pengguna isi email & password, submit | `POST /login` |
| 5 | **DECISION** | Rate limit OK? (max 5/menit) | |
| | | **Tidak** | #6 |
| | | **Ya** | #7 |
| 6 | **PROCESS** | Tampilkan error throttle | kembali ke #2 |
| 7 | **DECISION** | Kredensial valid? | |
| | | **Tidak** | #8 |
| | | **Ya** | #9 |
| 8 | **PROCESS** | Tampilkan error login gagal | kembali ke #2 |
| 9 | **PROCESS** | Buat session, redirect dashboard | `dashboard` |
| 10 | **DECISION** | Role user? | |
| | | **admin** | #11 |
| | | **dosen** | #12 |
| | | **mahasiswa** | #13 |
| | | **lainnya** | #14 |
| 11 | **PROCESS** | Buka dashboard admin | `admin.dashboard` → WF-01A |
| 12 | **PROCESS** | Buka dashboard dosen | `dosen.dashboard` → WF-01B |
| 13 | **PROCESS** | Buka dashboard mahasiswa | `mahasiswa.dashboard` → WF-01C |
| 14 | **PROCESS** | Redirect ke login | `login` |
| 15 | **PROCESS** | Form lupa password | `password.request` |
| 16 | **PROCESS** | Kirim link reset email | `password.email` |
| 17 | **PROCESS** | Form reset password (token) | `password.reset` |
| 18 | **PROCESS** | Simpan password baru | `password.update` |
| 19 | **PROCESS** | Kembali ke login | → #2 |
| 20 | **PROCESS** | Pengguna logout | `logout` |
| 21 | **END** | Session berakhir | — |

**Off-page:** Lanjut ke [SYS-01](sys/SYS-01.md) setelah login sukses.

---

## WF-01 — Akses Menu per Role (Decision Gate)

**Visio page disarankan:** `SYS-01`

Setiap request setelah login:

| # | Tipe | Langkah |
|---|------|---------|
| 1 | **DECISION** | User sudah login (`auth`)? |
| | **Tidak** | redirect `login` → **END** |
| | **Ya** | lanjut |
| 2 | **DECISION** | Role sesuai route? (`role:admin` / `dosen` / `mahasiswa`) |
| | **Tidak** | **PROCESS** tampilkan 403 → **END** |
| | **Ya** | lanjut ke modul yang dipilih |

### WF-01A — Navigasi Admin

**Visio page:** `ADM-00` | Detail: [admin/ADM-00.md](admin/ADM-00.md)

### WF-01B — Navigasi Dosen

**Visio page:** `DSN-00` | Detail: [dosen/DSN-00.md](dosen/DSN-00.md)

### WF-01C — Navigasi Mahasiswa

**Visio page:** `MHS-00` | Detail: [mahasiswa/MHS-00.md](mahasiswa/MHS-00.md)

---

## WF-02 — Siklus KRS (Mahasiswa ↔ Admin)

**Visio page disarankan:** `X-01` (swimlane: Mahasiswa | Sistem | Admin)

### Bagian A — Mahasiswa ajukan KRS

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Mahasiswa buka menu KRS | `mahasiswa.krs.index` |
| 2 | **DECISION** | Semester aktif ada? | |
| | **Tidak** | **PROCESS** tampilkan error → **END** | |
| | **Ya** | #3 | |
| 3 | **PROCESS** | Buka form tambah mata kuliah | `mahasiswa.krs.create` |
| 4 | **PROCESS** | Pilih jadwal kuliah | — |
| 5 | **DECISION** | Sudah ada di KRS? | |
| | **Ya** | **PROCESS** tolak → **END** | |
| | **Tidak** | #6 | |
| 6 | **DECISION** | Kuota jadwal penuh? | |
| | **Ya** | **PROCESS** tolak → **END** | |
| | **Tidak** | #7 | |
| 7 | **DECISION** | Prodi & jadwal status aktif OK? | |
| | **Tidak** | **PROCESS** tolak → **END** | |
| | **Ya** | #8 | |
| 8 | **PROCESS** | Simpan KRS status `pending`, increment `terisi` | `mahasiswa.krs.store` |
| 9 | **PROCESS** | Kirim notifikasi ke admin | — |
| 10 | **END** | Menunggu persetujuan admin | → WF-02B |

Detail: [mahasiswa/MHS-02.md](mahasiswa/MHS-02.md)

### Bagian B — Admin setujui/tolak KRS

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Admin buka daftar KRS | `admin.krs.index` |
| 2 | **PROCESS** | Pilih KRS berstatus pending | — |
| 3 | **DECISION** | Keputusan admin? | |
| | **Approve** | #4 | |
| | **Reject** | #8 | |
| 4 | **PROCESS** | Set status `disetujui` | `admin.krs.approve` |
| 5 | **PROCESS** | Catat audit log | — |
| 6 | **PROCESS** | Notifikasi + email ke mahasiswa | — |
| 7 | **END** | KRS disetujui → mahasiswa bisa akses presensi/tugas/ujian | |
| 8 | **PROCESS** | Set status `ditolak` | `admin.krs.reject` |
| 9 | **PROCESS** | Decrement `jadwal.terisi` | — |
| 10 | **PROCESS** | Audit log + notifikasi + email | — |
| 11 | **END** | KRS ditolak | |

Detail: [admin/ADM-09.md](admin/ADM-09.md)

### Bagian C — Mahasiswa hapus KRS

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Mahasiswa pilih hapus KRS milik sendiri | `mahasiswa.krs.destroy` |
| 2 | **PROCESS** | Decrement `terisi` jadwal | — |
| 3 | **END** | KRS dihapus | |

---

## WF-03 — Presensi Kelas (Dosen ↔ Mahasiswa)

**Visio page disarankan:** `X-02` (swimlane: Dosen | Mahasiswa | Sistem)

### Bagian A — Dosen buka & tutup kelas

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Dosen buka presensi kelas | `dosen.presensi-kelas.index` |
| 2 | **DECISION** | Dosen klik buka kelas? | |
| | **Tidak** | **END** (hanya lihat daftar) | |
| | **Ya** | #3 | |
| 3 | **DECISION** | Sudah ada sesi aktif untuk jadwal ini? | |
| | **Ya** | **PROCESS** tolak → **END** | |
| | **Tidak** | #4 | |
| 4 | **DECISION** | Pertemuan + tanggal duplikat? | |
| | **Ya** | **PROCESS** tolak → **END** | |
| | **Tidak** | #5 | |
| 5 | **PROCESS** | Generate `kode_kelas`, buka ClassSession | `dosen.presensi-kelas.buka` |
| 6 | **PROCESS** | Monitor peserta (live) | `dosen.presensi-kelas.show` |
| 7 | **DECISION** | Aksi dosen di sesi? | |
| | **Tutup kelas** | #8 | |
| | **Kick mahasiswa** | **PROCESS** tandai kicked + presensi alpa → kembali #6 | `dosen.presensi-kelas.kick` |
| | **Update izin/sakit** | **PROCESS** sync presensi → kembali #6 | `dosen.presensi-kelas.update-status` |
| | **Lanjut monitor** | kembali #6 | |
| 8 | **PROCESS** | Tutup sesi | `dosen.presensi-kelas.tutup` |
| 9 | **PROCESS** | Sync presensi: hadir untuk yang hadir, alpa untuk yang tidak | — |
| 10 | **END** | Sesi presensi selesai | |

Detail: [dosen/DSN-04.md](dosen/DSN-04.md)

### Bagian B — Mahasiswa join kelas

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Mahasiswa buka presensi kelas | `mahasiswa.presensi-kelas.index` |
| 2 | **PROCESS** | Input kode kelas | `mahasiswa.presensi-kelas.join` |
| 3 | **DECISION** | Session masih buka? | |
| | **Tidak** | **PROCESS** tolak → **END** | |
| | **Ya** | #4 | |
| 4 | **DECISION** | KRS `disetujui` untuk jadwal ini? | |
| | **Tidak** | **PROCESS** tolak → **END** | |
| | **Ya** | #5 | |
| 5 | **DECISION** | Sudah kicked atau sudah absen? | |
| | **Ya** | **PROCESS** tolak → **END** | |
| | **Tidak** | #6 | |
| 6 | **PROCESS** | Buat ClassAttendance + sync Presensi `hadir` | — |
| 7 | **END** | Presensi tercatat | |

Detail: [mahasiswa/MHS-08.md](mahasiswa/MHS-08.md)

### Bagian C — Konfirmasi izin/sakit (mahasiswa)

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Mahasiswa buka riwayat | `mahasiswa.presensi-kelas.history` |
| 2 | **PROCESS** | Konfirmasi izin atau sakit | `mahasiswa.presensi-kelas.konfirmasi-izin` / `konfirmasi-sakit` |
| 3 | **PROCESS** | Sync ke tabel Presensi | — |
| 4 | **END** | Status diperbarui | |

### Bagian D — Presensi manual dosen (alternatif)

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Dosen buka presensi manual | `dosen.presensi.index` |
| 2 | **PROCESS** | Pilih jadwal & pertemuan | `dosen.presensi.create` |
| 3 | **PROCESS** | Input status per mahasiswa (hadir/izin/sakit/alpa) | `dosen.presensi.store` |
| 4 | **PROCESS** | Sync ke ClassAttendance jika ada sesi matching | — |
| 5 | **END** | Presensi tersimpan | |

Detail: [dosen/DSN-03.md](dosen/DSN-03.md)

---

## WF-04 — Input Nilai & KHS

**Visio page disarankan:** `X-03`

### Bagian A — Dosen input nilai

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Dosen buka input nilai | `dosen.nilai.index` |
| 2 | **PROCESS** | Pilih jadwal kuliah | — |
| 3 | **DECISION** | Jadwal milik dosen ini? | |
| | **Tidak** | **PROCESS** 403 → **END** | |
| | **Ya** | #4 | |
| 4 | **PROCESS** | Tampilkan mahasiswa KRS disetujui | `dosen.nilai.create` |
| 5 | **PROCESS** | Input nilai tugas, UTS, UAS | — |
| 6 | **PROCESS** | Hitung nilai akhir (30%-30%-40%) + huruf A–E | — |
| 7 | **DECISION** | Semua komponen terisi? | |
| | **Ya** | status = `selesai` | |
| | **Tidak** | status = `sedang` | |
| 8 | **PROCESS** | Simpan + audit log | `dosen.nilai.store` / `dosen.nilai.update` |
| 9 | **DECISION** | Record nilai baru? | |
| | **Ya** | **PROCESS** notifikasi + email ke mahasiswa | |
| | **Tidak** | — | |
| 10 | **END** | Nilai tersimpan | |

Detail: [dosen/DSN-02.md](dosen/DSN-02.md)

### Bagian B — Mahasiswa lihat KHS & transkrip

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Mahasiswa buka KHS | `mahasiswa.khs.index` |
| 2 | **PROCESS** | Pilih semester (default: aktif) | — |
| 3 | **PROCESS** | Tampilkan nilai + IP semester (read-only) | — |
| 4 | **END** | — | |
| 5 | **START** | (Opsional) Download transkrip PDF | `mahasiswa.transcript.download` |
| 6 | **END** | — | |

Detail: [mahasiswa/MHS-03.md](mahasiswa/MHS-03.md), [MHS-04.md](mahasiswa/MHS-04.md)

---

## WF-05 — Pembayaran

**Visio page disarankan:** `X-04` (swimlane: Mahasiswa | Sistem | Admin)

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Mahasiswa buka pembayaran | `payment.index` |
| 2 | **PROCESS** | Buat tagihan baru | `payment.create` → `payment.store` |
| 3 | **DECISION** | Validasi OK? (min Rp1000, bank aktif) | |
| | **Tidak** | **PROCESS** error → **END** | |
| | **Ya** | #4 | |
| 4 | **PROCESS** | Status `pending`, buat VA Xendit / manual | — |
| 5 | **DECISION** | Mahasiswa bayar? | |
| | **Via Xendit** | #6 | |
| | **Manual / tunggu** | #8 | |
| 6 | **PROCESS** | Webhook Xendit update status | `payment.xendit.webhook` |
| 7 | **PROCESS** | Status → `paid` | — |
| 8 | **DECISION** | Lewat 24 jam belum bayar? | |
| | **Ya** | **PROCESS** auto-expire | |
| | **Tidak** | #9 | |
| 9 | **DECISION** | Admin verifikasi manual? | |
| | **Ya** | **PROCESS** status → `paid` | `admin.payment.verify` |
| | **Tidak** | #10 | |
| 10 | **DECISION** | Admin cancel? status pending? | |
| | **Ya** | **PROCESS** cancel payment | `admin.payment.cancel` |
| | **Tidak** | — | |
| 11 | **PROCESS** | Mahasiswa lihat detail | `payment.show` |
| 12 | **DECISION** | Mahasiswa cancel sendiri? pending? | |
| | **Ya** | **PROCESS** cancel | `payment.cancel` |
| | **Tidak** | — | |
| 13 | **END** | Pembayaran selesai / expired / cancelled | |

Detail: [mahasiswa/MHS-13.md](mahasiswa/MHS-13.md), [admin/ADM-22.md](admin/ADM-22.md)

---

## WF-06 — Tugas (Assignment)

### Mahasiswa

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Lihat daftar tugas | `mahasiswa.assignment.index` |
| 2 | **PROCESS** | Buka detail tugas | `mahasiswa.assignment.show` |
| 3 | **DECISION** | Terdaftar via KRS disetujui? | |
| | **Tidak** | 403 → **END** | |
| | **Ya** | #4 | |
| 4 | **DECISION** | Lewat deadline? | |
| | **Ya** | **PROCESS** tidak bisa submit baru → **END** | |
| | **Tidak** | #5 | |
| 5 | **DECISION** | Sudah ada submission? | |
| | **Ya** | **PROCESS** update file | `mahasiswa.assignment.update-submission` |
| | **Tidak** | **PROCESS** upload submit | `mahasiswa.assignment.submit` |
| 6 | **END** | Tugas terkirim | |

Detail: [mahasiswa/MHS-11.md](mahasiswa/MHS-11.md)

### Dosen

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | CRUD tugas per jadwal | `dosen.assignment.index` |
| 2 | **PROCESS** | Buat/edit tugas | `dosen.assignment.create` / `.store` |
| 3 | **DECISION** | Publish? deadline > now? | |
| | **Tidak** | status draft | |
| | **Ya** | status published | |
| 4 | **PROCESS** | Buka submission mahasiswa | `dosen.assignment.show` |
| 5 | **PROCESS** | Beri nilai + feedback | `dosen.assignment.grade-submission` |
| 6 | **END** | Submission dinilai | |

Detail: [dosen/DSN-08.md](dosen/DSN-08.md)

---

## WF-07 — Ujian Mahasiswa

**Visio page disarankan:** `MHS-12`

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Lihat daftar ujian | `mahasiswa.exam.index` |
| 2 | **PROCESS** | Buka detail ujian | `mahasiswa.exam.show` |
| 3 | **DECISION** | Waktu ujian belum mulai? | |
| | **Ya** | **PROCESS** tampil not-started → **END** | |
| | **Tidak** | #4 | |
| 4 | **DECISION** | Waktu ujian sudah selesai? | |
| | **Ya** | **PROCESS** tampil ended/result → **END** | |
| | **Tidak** | #5 | |
| 5 | **PROCESS** | Mulai ujian / lanjut session | `mahasiswa.exam.start` |
| 6 | **PROCESS** | Kerjakan soal, simpan jawaban AJAX | `mahasiswa.exam.take`, `mahasiswa.exam.save-answer` |
| 7 | **DECISION** | Pelanggaran proctoring melewati threshold? | `mahasiswa.exam.log-violation` |
| | **Ya** | **PROCESS** session terminated → dashboard → **END** | |
| | **Tidak** | #8 | |
| 8 | **DECISION** | Waktu habis atau submit manual? | |
| | **Ya** | #9 | |
| | **Tidak** | kembali #6 | |
| 9 | **PROCESS** | Submit, nilai pilgan otomatis | `mahasiswa.exam.submit` |
| 10 | **PROCESS** | Tampilkan hasil | `mahasiswa.exam.result` |
| 11 | **END** | Ujian selesai | |

Detail: [mahasiswa/MHS-12.md](mahasiswa/MHS-12.md)

---

## WF-08 — Ujian Dosen

**Visio page disarankan:** `DSN-09a`, `DSN-09b`, `DSN-09c`

### Buat & kelola ujian (DSN-09a)

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Buat ujian untuk jadwal | `dosen.exam.create` → `dosen.exam.store` |
| 2 | **DECISION** | Edit/hapus ujian? Ada session started? | |
| | **Ya** | **PROCESS** blokir → **END** | |
| | **Tidak** | #3 | |
| 3 | **PROCESS** | Update/hapus ujian | `dosen.exam.update` / `dosen.exam.destroy` |
| 4 | **END** | — | |

### Soal & penilaian (DSN-09b)

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Kelola soal | `dosen.exam.add-question` |
| 2 | **DECISION** | Belum ada soal? | |
| | **Ya** | **PROCESS** generate placeholder | `dosen.exam.generate-questions` |
| | **Tidak** | — | |
| 3 | **PROCESS** | Update/hapus soal | `update-question`, `delete-question` |
| 4 | **PROCESS** | Lihat hasil semua peserta | `dosen.exam.results` |
| 5 | **PROCESS** | Nilai essay manual | `dosen.exam.grade-session` |
| 6 | **END** | — | |

### Monitoring (DSN-09c)

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Monitor ujian | `dosen.exam.ongoing` |
| 2 | **PROCESS** | Lihat mahasiswa aktif | `dosen.exam.active-students` |
| 3 | **PROCESS** | Lihat pelanggaran | `dosen.exam.violations` |
| 4 | **PROCESS** | Atur violation rules | `dosen.exam.violation-rules` |
| 5 | **PROCESS** | Ujian selesai | `dosen.exam.finished` |
| 6 | **END** | — | |

---

## WF-09 — CRUD Admin (Template Umum)

**Visio page:** duplikat dari [_TEMPLATE-CRUD.md](_TEMPLATE-CRUD.md)

Berlaku untuk: Prodi, Mahasiswa, Dosen, Mata Kuliah, Semester, Jadwal, Pengumuman, Admin user, Kalender, Template KRS/KHS, Bank.

| # | Tipe | Langkah | Route pola |
|---|------|---------|------------|
| 1 | **START** | Admin buka daftar data | `{resource}.index` |
| 2 | **DECISION** | Aksi admin? | |
| | **Tambah** | #3 | |
| | **Edit** | #5 | |
| | **Hapus** | #8 | |
| | **Kembali** | **END** | |
| 3 | **PROCESS** | Form tambah | `{resource}.create` |
| 4 | **DECISION** | Validasi OK? | |
| | **Tidak** | kembali #3 | |
| | **Ya** | **PROCESS** simpan → #11 | `{resource}.store` |
| 5 | **PROCESS** | Form edit | `{resource}.edit` |
| 6 | **DECISION** | Validasi OK? | |
| | **Tidak** | kembali #5 | |
| | **Ya** | **PROCESS** update → #11 | `{resource}.update` |
| 7 | **DECISION** | Aturan bisnis OK? | |
| | **Tidak** | **PROCESS** error → #1 | |
| | **Ya** | #10 | |
| 8 | **PROCESS** | Konfirmasi hapus | — |
| 9 | **DECISION** | Aturan bisnis OK? | → sama #7 |
| 10 | **PROCESS** | Hapus record | `{resource}.destroy` |
| 11 | **PROCESS** | Flash sukses, kembali ke index | — |
| 12 | **END** | — | |

**Decision khusus per modul:** lihat tabel di [_TEMPLATE-CRUD.md](_TEMPLATE-CRUD.md).

---

## WF-10 — Admin Setup Akademik (Urutan Dependensi)

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Admin setup master data | — |
| 2 | **PROCESS** | Buat Program Studi | `admin.prodi.store` |
| 3 | **PROCESS** | Buat Semester, set aktif | `admin.semester.store` |
| 4 | **PROCESS** | Buat Mata Kuliah | `admin.mata-kuliah.store` |
| 5 | **PROCESS** | Buat Dosen & Mahasiswa | `admin.dosen.store`, `admin.mahasiswa.store` |
| 6 | **PROCESS** | Buat Jadwal Kuliah (`terisi = 0`) | `admin.jadwal-kuliah.store` |
| 7 | **END** | Sistem siap → lanjut WF-02 KRS | |

---

## WF-11 — Komunikasi & Profil (Semua Role)

**Visio page disarankan:** `SYS-02`

| # | Tipe | Langkah | Route |
|---|------|---------|-------|
| 1 | **START** | Buka menu komunikasi/profil | — |
| 2 | **DECISION** | Pilih fitur? | |
| | **Notifikasi** | `notifikasi.index` → read / read-all | |
| | **Chat** | `chat.index` → show → message | |
| | **Forum** | `forum.index` → show → reply | |
| | **Q&A** | `qna.index` → show → answer | |
| | **Profil** | `profile.show` → update / password.update | |
| 3 | **END** | — | |

Detail: [sys/SYS-02.md](sys/SYS-02.md)

---

## WF-12 — Ujian Anti-Cheat (Dosen ↔ Mahasiswa)

**Visio page disarankan:** `X-05` (gabungan) · pecahan `X-05a` (aturan dosen) · `X-05b` (deteksi mahasiswa)

| # | Tipe | Langkah | Route / catatan |
|---|------|---------|-----------------|
| 1 | **START** | Dosen buat ujian (flag copy/tab/fullscreen) | `dosen.exam.store` |
| 2 | **PROCESS** | Sistem buat aturan default | tabel `exam_violation_rules` |
| 3 | **PROCESS** | Dosen ubah limit & pesan | `dosen.exam.violation-rules.update` |
| 4 | **PROCESS** | Mahasiswa mulai / lanjut session | `mahasiswa.exam.start` → `mahasiswa.exam.take` |
| 5 | **DECISION** | Event browser terdeteksi? | tab / copy / blur / keluar fullscreen |
| | **Tidak** | jawab soal `save-answer` atau submit | kembali #4 |
| | **Ya** | #6 | |
| 6 | **PROCESS** | Kirim log pelanggaran | `mahasiswa.exam.log-violation` |
| 7 | **DECISION** | Deteksi tipe enabled? | |
| | **Tidak** | session tetap `started` | kembali #4 |
| | **Ya** | simpan JSON + counter | #8 |
| 8 | **DECISION** | Limit tipe atau total pelanggaran terlampaui? | |
| | **Ya** | `status = terminated` → dashboard → **END** | |
| | **Tidak** | tampil `warning_message` | kembali #4 |
| 9 | **DECISION** | Waktu habis? | |
| | **Ya** | `auto_submitted` | #11 |
| | **Tidak** | submit manual | #10 |
| 10 | **PROCESS** | Submit + nilai pilgan | `mahasiswa.exam.submit` |
| 11 | **PROCESS** | Hasil mahasiswa | `mahasiswa.exam.result` |
| 12 | **PROCESS** | Dosen lihat pelanggaran | `dosen.exam.violations` / `violation-detail` |
| 13 | **END** | — | |

Detail: [X-05](cross/X-05.md) · [X-05a](cross/X-05a.md) · [X-05b](cross/X-05b.md)

---

## Peta Workflow → Halaman Visio

| Workflow | Halaman Visio | Swimlane? |
|----------|---------------|-----------|
| WF-00 | SYS-00 | Tidak |
| WF-01 | SYS-01, ADM/DSN/MHS-00 | Tidak |
| WF-02 | X-01 | Ya |
| WF-03 | X-02, DSN-04, MHS-08 | Ya (X-02) |
| WF-04 | X-03, DSN-02, MHS-03 | Tidak |
| WF-05 | X-04, MHS-13, ADM-22 | Ya (X-04) |
| WF-06 | MHS-11, DSN-08 | Tidak |
| WF-07 | MHS-12 | Tidak |
| WF-08 | DSN-09a/b/c | Tidak |
| WF-09 | ADM-02 s/d ADM-08, dll. | Tidak |
| WF-10 | ADM-03 s/d ADM-08 (gabung) | Tidak |
| WF-11 | SYS-02 | Tidak |
| WF-12 | X-05, X-05a, X-05b, MHS-12, DSN-09c | Ya (X-05) |

---

## Sumber

- Routes: `routes/web.php`
- Diagram per modul: folder `admin/`, `dosen/`, `mahasiswa/`, `cross/`, `sys/`
- Checklist: [CHECKLIST-VISIO.md](CHECKLIST-VISIO.md)
- Index route: [ROUTE-INDEX.md](ROUTE-INDEX.md)
