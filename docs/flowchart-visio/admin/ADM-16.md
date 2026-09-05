# ADM-16 — Pengaturan Sistem

| Shape | Route |
|-------|-------|
| Process | `admin.system-settings.index` (?tab=) |
| Process | `admin.system-settings.update-semester` |
| Process | `admin.system-settings.update-grading` |
| Process | `admin.system-settings.store-letter-grade` |
| Process | `admin.system-settings.update-letter-grade` |
| Process | `admin.system-settings.delete-letter-grade` |
| Process | `admin.system-settings.update-app-info` |

**Decisions:**
- Huruf mutu overlap? → tolak
- Delete letter grade → `is_active = false` (soft)
