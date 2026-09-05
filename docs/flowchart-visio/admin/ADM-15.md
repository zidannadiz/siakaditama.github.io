# ADM-15 — Backup & Restore

| Shape | Route |
|-------|-------|
| Process | `admin.backup.index` |
| Decision | Aksi? Backup / Restore |
| Process | `admin.backup.create` |
| Decision | DB SQLite? |
| Process | Copy file / mysqldump |
| Process | `admin.backup.download` |
| Process | `admin.backup.restore` |
| Process | `admin.backup.destroy` |

```mermaid
flowchart TD
    start([admin.backup.index]) --> act{Aksi?}
    act -->|Backup| create[admin.backup.create]
    create --> sqlite{SQLite?}
    sqlite -->|Ya| cp[Copy DB file]
    sqlite -->|Tidak| dump[mysqldump]
    cp --> audit[Audit log]
    dump --> audit
    act -->|Restore| restore[admin.backup.restore]
    restore --> ok{Berhasil?}
    ok -->|Ya| success[Flash sukses]
    ok -->|Tidak| fail[Flash error]
```
