# ADM-09 — Persetujuan KRS

| Shape | Route |
|-------|-------|
| Terminator | Mulai |
| Process | `admin.krs.index` |
| Process | Pilih KRS pending |
| Decision | Approve atau Reject? |
| Process | `admin.krs.approve` |
| Process | `admin.krs.reject` |
| Terminator | Selesai |

```mermaid
flowchart TD
    start([Mulai]) --> list[admin.krs.index]
    list --> pick[Pilih KRS]
    pick --> action{Approve / Reject?}
    action -->|Approve| appr[admin.krs.approve → disetujui]
    appr --> audit1[Audit log]
    audit1 --> notif1[Notifikasi + email]
    action -->|Reject| rej[admin.krs.reject → ditolak]
    rej --> dec[Decrement jadwal terisi]
    dec --> audit2[Audit log]
    audit2 --> notif2[Notifikasi + email]
    notif1 --> endNode([Selesai])
    notif2 --> endNode
```

**Off-page:** [X-01](../cross/X-01.md)
