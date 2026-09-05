# ADM-11 — Template KRS/KHS

| Route | Shape |
|-------|-------|
| `admin.template-krs-khs.index` | Process |
| `admin.template-krs-khs.create` | Process |
| `admin.template-krs-khs.store` | Process + Document (.doc/.docx) |
| `admin.template-krs-khs.edit` | Process |
| `admin.template-krs-khs.update` | Process |
| `admin.template-krs-khs.destroy` | Process |
| `admin.template-krs-khs.toggle-status` | Decision: satu aktif per `jenis` |
| `admin.template-krs-khs.download` | Document |

**Decision toggle:** Aktifkan template → nonaktifkan template lain dengan `jenis` sama (krs/khs).
