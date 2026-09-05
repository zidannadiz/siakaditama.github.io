# DSN-01 — Dashboard Dosen

| Shape | Route |
|-------|-------|
| Process | `dosen.dashboard` |
| Decision | Profil Dosen ada? |
| Process | Jadwal hari ini (hari + status aktif) |
| Process | Badge tugas/ujian belum dinilai |
| Terminator | Selesai |

**Decision Tidak:** 404 jika tidak ada record `Dosen` untuk `user_id`.
