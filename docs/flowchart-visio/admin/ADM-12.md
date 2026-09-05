# ADM-12 — Generate KRS/KHS (Admin)

| Shape | Route |
|-------|-------|
| Process | `admin.generate-krs-khs.index` |
| Data | Pilih template, mahasiswa, semester |
| Process | `admin.generate-krs-khs.generate` |
| Document | Download file Word |
| Decision | Error? → `back()` |
