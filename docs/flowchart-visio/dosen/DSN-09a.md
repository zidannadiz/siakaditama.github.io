# DSN-09a — Ujian: Buat & Kelola

| Route | Shape |
|-------|-------|
| `dosen.exam.index` | Process |
| `dosen.exam.create` | Process |
| `dosen.exam.store` | Process |
| `dosen.exam.show` | Process |
| `dosen.exam.edit` | Process |
| `dosen.exam.update` | Process |
| `dosen.exam.destroy` | Process |

**Decisions:**
- `dosen.exam.store`: tipe `pilgan|essay|campuran`; buat default violation rules
- `dosen.exam.update` / `destroy`: ada session `started`? → blokir

**Off-page:** DSN-09b, DSN-09c, [X-05](../cross/X-05.md)
