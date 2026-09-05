# SYS-01 — Dashboard Redirect per Role

**Visio page:** `SYS-01`

## Shape mapping

| Shape | Label | Route |
|-------|-------|-------|
| Terminator | Mulai (setelah login) | — |
| Process | Akses dashboard | `dashboard` |
| Decision | Role user? | `auth()->user()->role` |
| Process | Redirect admin | `admin.dashboard` |
| Process | Redirect dosen | `dosen.dashboard` |
| Process | Redirect mahasiswa | `mahasiswa.dashboard` |
| Process | Redirect login | `login` (default) |
| Decision | Middleware role | `role:admin` / `dosen` / `mahasiswa` |
| Process | 403 Forbidden | RoleMiddleware |
| Terminator | Selesai | — |

```mermaid
flowchart TD
    start([Login sukses]) --> dash["GET dashboard"]
    dash --> role{role user?}
    role -->|admin| adm["admin.dashboard"]
    role -->|dosen| dsn["dosen.dashboard"]
    role -->|mahasiswa| mhs["mahasiswa.dashboard"]
    role -->|lainnya| login["login"]
    adm --> guardAdmin{Akses /admin/*?}
    guardAdmin -->|Bukan admin| e403[403 RoleMiddleware]
    guardAdmin -->|OK| admDash[Admin dashboard view]
    dsn --> dsnDash[Dosen dashboard]
    mhs --> mhsDash[Mahasiswa dashboard]
```

**Off-page:** ADM-00, DSN-00, MHS-00
