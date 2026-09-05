# DSN-03 — Presensi Manual

| Shape | Route |
|-------|-------|
| Process | `dosen.presensi.index` |
| Process | `dosen.presensi.create` |
| Process | `dosen.presensi.store` |
| Process | `dosen.presensi.show` |
| Process | `dosen.presensi.edit` |
| Process | `dosen.presensi.update` |

**Decision status:** `hadir` | `izin` | `sakit` | `alpa`

**Process sync:** Jika ada `ClassSession` matching → update `ClassAttendance`
