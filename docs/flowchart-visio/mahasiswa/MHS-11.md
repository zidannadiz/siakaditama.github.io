# MHS-11 — Tugas

| Shape | Route |
|-------|-------|
| Process | `mahasiswa.assignment.index` |
| Process | `mahasiswa.assignment.show` |
| Process | `mahasiswa.assignment.submit` |
| Process | `mahasiswa.assignment.update-submission` |
| Process | `mahasiswa.assignment.download` |
| Decision | KRS disetujui? |
| Decision | Lewat deadline? |
| Decision | Sudah ada submission? |

```mermaid
flowchart TD
    start[mahasiswa.assignment.index] --> show[mahasiswa.assignment.show]
    show --> enr{KRS disetujui?}
    enr -->|Tidak| e403[403]
    enr -->|Ya| dl{Deadline lewat?}
    dl -->|Ya| block[Tolak submit baru]
    dl -->|Tidak| sub[mahasiswa.assignment.submit]
    sub --> exists{Submission ada?}
    exists -->|Ya| upd[mahasiswa.assignment.update-submission]
    exists -->|Tidak| new[Submission baru]
```
