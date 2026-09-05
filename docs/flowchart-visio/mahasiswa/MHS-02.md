# MHS-02 — KRS

| Shape | Route |
|-------|-------|
| Process | `mahasiswa.krs.index` |
| Process | `mahasiswa.krs.create` |
| Process | `mahasiswa.krs.store` |
| Process | `mahasiswa.krs.destroy` |
| Decision | Semester aktif? |
| Decision | Sudah di KRS? / Kuota? / Prodi OK? |

```mermaid
flowchart TD
    start[mahasiswa.krs.index] --> sem{Semester aktif?}
    sem -->|Tidak| err[Error]
    sem -->|Ya| create[mahasiswa.krs.create]
    create --> store[mahasiswa.krs.store]
    store --> d1{Duplikat?}
    d1 -->|Ya| b1[Tolak]
    d1 -->|Tidak| d2{Kuota penuh?}
    d2 -->|Ya| b2[Tolak]
    d2 -->|Tidak| d3{Prodi & jadwal OK?}
    d3 -->|Tidak| b3[Tolak]
    d3 -->|Ya| pending[pending + terisi++]
    pending --> wait[Menunggu admin]
    start --> hapus[mahasiswa.krs.destroy]
    hapus --> dec[terisi--]
```

**Off-page:** [X-01](../cross/X-01.md)
