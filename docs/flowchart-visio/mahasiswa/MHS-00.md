# MHS-00 — Navigasi Mahasiswa

| Menu | Route |
|------|-------|
| Dashboard | `mahasiswa.dashboard` |
| KRS | `mahasiswa.krs.index` |
| KHS | `mahasiswa.khs.index` |
| Transkrip | `mahasiswa.transcript.index` |
| Tugas | `mahasiswa.assignment.index` |
| Ujian | `mahasiswa.exam.index` |
| Presensi | `mahasiswa.presensi.index` |
| Presensi Kelas | `mahasiswa.presensi-kelas.index` |
| Pembayaran | `payment.index` |
| Kalender | `mahasiswa.kalender-akademik.index` |
| Statistik Keaktifan | `mahasiswa.statistik-keaktifan.index` |
| Generate KRS/KHS | `mahasiswa.generate-krs-khs.index` (tidak di sidebar) |
| Komunikasi | SYS-02 |

```mermaid
flowchart TD
    dash[mahasiswa.dashboard] --> m{Menu}
    m --> krs[mahasiswa.krs.*]
    m --> khs[mahasiswa.khs.* / transcript.*]
    m --> tugas[mahasiswa.assignment.*]
    m --> ujian[mahasiswa.exam.*]
    m --> pres[mahasiswa.presensi.* / presensi-kelas.*]
    m --> bayar[payment.*]
    m --> kal[mahasiswa.kalender-akademik.*]
    m --> stat[mahasiswa.statistik-keaktifan.index]
    m --> kom[SYS-02]
```
