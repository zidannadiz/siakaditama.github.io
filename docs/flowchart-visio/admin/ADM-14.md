# ADM-14 — Pengguna Aktif

| Shape | Route |
|-------|-------|
| Terminator | Mulai |
| Process | `admin.active-users.index` |
| Stored Data | Tabel `sessions` + `users` |
| Process | Filter dosen/mahasiswa dalam `session.lifetime` |
| Terminator | Selesai (read-only) |
