# DSN-04 — Presensi Kelas

| Shape | Route |
|-------|-------|
| Process | `dosen.presensi-kelas.index` |
| Process | `dosen.presensi-kelas.buka` |
| Process | `dosen.presensi-kelas.show` |
| Process | `dosen.presensi-kelas.peserta` |
| Process | `dosen.presensi-kelas.tutup` |
| Process | `dosen.presensi-kelas.kick` |
| Process | `dosen.presensi-kelas.update-status` |

```mermaid
flowchart TD
    start[dosen.presensi-kelas.index] --> buka{Buka kelas?}
    buka -->|Ya| c1{Sesi aktif ada?}
    c1 -->|Ya| err[Tolak]
    c1 -->|Tidak| c2{Duplikat pertemuan?}
    c2 -->|Ya| err
    c2 -->|Tidak| gen[dosen.presensi-kelas.buka]
    gen --> live[dosen.presensi-kelas.show]
    live --> aksi{Aksi?}
    aksi -->|Tutup| tutup[dosen.presensi-kelas.tutup]
    aksi -->|Kick| kick[dosen.presensi-kelas.kick]
    aksi -->|Izin/Sakit| upd[dosen.presensi-kelas.update-status]
    aksi -->|Monitor| live
```

**Off-page:** [X-02](../cross/X-02.md)
