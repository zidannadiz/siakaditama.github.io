# DSN-08 — Tugas (Assignment)

| Route | Shape |
|-------|-------|
| `dosen.assignment.index` | Process |
| `dosen.assignment.create` | Process (wajib `jadwal_id`) |
| `dosen.assignment.store` | Process |
| `dosen.assignment.show` | Process |
| `dosen.assignment.edit` | Process |
| `dosen.assignment.update` | Process |
| `dosen.assignment.destroy` | Process |
| `dosen.assignment.grade-submission` | Process |

**Decisions:**
- Jadwal milik dosen? → 403
- `status` draft / published; deadline > now
- Grade: set `nilai` + `feedback`
