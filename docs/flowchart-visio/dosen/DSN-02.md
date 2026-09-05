# DSN-02 — Input Nilai

| Shape | Route |
|-------|-------|
| Process | `dosen.nilai.index` |
| Process | `dosen.nilai.create` |
| Process | `dosen.nilai.store` |
| Process | `dosen.nilai.edit` |
| Process | `dosen.nilai.update` |
| Decision | Jadwal milik dosen? |
| Decision | Record baru? → notifikasi |

```mermaid
flowchart TD
    start[dosen.nilai.index] --> pilih[Pilih jadwal]
    pilih --> own{Milik dosen?}
    own -->|Tidak| e403[403]
    own -->|Ya| create[dosen.nilai.create]
    create --> store[dosen.nilai.store]
    store --> hitung[30-30-40 + huruf A-E]
    hitung --> status{Semua komponen?}
    status -->|Ya| selesai[status selesai]
    status -->|Tidak| proses[status sedang]
    selesai --> save[Simpan + audit]
    proses --> save
    save --> baru{Baru?}
    baru -->|Ya| notif[Notifikasi + email]
    baru -->|Tidak| endNode([index])
    notif --> endNode
```

**Off-page:** [X-03](../cross/X-03.md)
