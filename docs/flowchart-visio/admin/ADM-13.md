# ADM-13 — Kalender Akademik

| Route | Shape |
|-------|-------|
| `admin.kalender-akademik.index` | Process |
| `admin.kalender-akademik.create` | Process |
| `admin.kalender-akademik.store` | Process |
| `admin.kalender-akademik.edit` | Process |
| `admin.kalender-akademik.update` | Process |
| `admin.kalender-akademik.destroy` | Process |
| `admin.kalender-akademik.get-events` | Process (JSON FullCalendar) |

**Decision:** `is_important`? → kirim notifikasi ke `target_role`.

`admin.kalender-akademik.show` → redirect ke index.
