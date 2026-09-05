# DSN-09b — Ujian: Soal & Penilaian

| Route | Shape |
|-------|-------|
| `dosen.exam.add-question` | Process |
| `dosen.exam.generate-questions` | Process |
| `dosen.exam.update-question` | Process |
| `dosen.exam.delete-question` | Process |
| `dosen.exam.results` | Process |
| `dosen.exam.grade-session` | Process (GET) |
| `dosen.exam.grade-session.store` | Process (POST) |

**Decision:** `dosen.exam.generate-questions` — hanya jika belum ada soal.
