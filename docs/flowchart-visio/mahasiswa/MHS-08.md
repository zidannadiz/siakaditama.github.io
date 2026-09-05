# MHS-08 — Presensi Kelas

| Shape | Route |
|-------|-------|
| Process | `mahasiswa.presensi-kelas.index` |
| Process | `mahasiswa.presensi-kelas.join` |
| Process | `mahasiswa.presensi-kelas.history` |
| Process | `mahasiswa.presensi-kelas.konfirmasi-izin` |
| Process | `mahasiswa.presensi-kelas.konfirmasi-sakit` |

```mermaid
flowchart TD
    start[mahasiswa.presensi-kelas.index] --> join[mahasiswa.presensi-kelas.join]
    join --> v1{Session buka?}
    v1 -->|Tidak| e1[Tolak]
    v1 -->|Ya| v2{KRS disetujui?}
    v2 -->|Tidak| e2[Tolak]
    v2 -->|Ya| v3{Kicked / duplikat?}
    v3 -->|Ya| e3[Tolak]
    v3 -->|Tidak| ok[Attendance + Presensi hadir]
    start --> hist[mahasiswa.presensi-kelas.history]
    hist --> izin[konfirmasi-izin / konfirmasi-sakit]
```

**Off-page:** [X-02](../cross/X-02.md)
