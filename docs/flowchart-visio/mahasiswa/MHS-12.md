# MHS-12 — Ujian

| Shape | Route |
|-------|-------|
| Process | `mahasiswa.exam.index` |
| Process | `mahasiswa.exam.show` |
| Process | `mahasiswa.exam.start` |
| Process | `mahasiswa.exam.take` |
| Process | `mahasiswa.exam.save-answer` |
| Process | `mahasiswa.exam.submit` |
| Process | `mahasiswa.exam.log-violation` |
| Process | `mahasiswa.exam.result` |

```mermaid
flowchart TD
    start[mahasiswa.exam.index] --> show[mahasiswa.exam.show]
    show --> t1{Belum mulai?}
    t1 -->|Ya| notStart[View not-started]
    t1 -->|Tidak| t2{Waktu selesai?}
    t2 -->|Ya| ended[View ended / result]
    t2 -->|Tidak| startExam[mahasiswa.exam.start]
    startExam --> take[mahasiswa.exam.take]
    take --> viol{Pelanggaran threshold?}
    viol -->|Ya| term[Terminated → dashboard]
    viol -->|Tidak| submit[mahasiswa.exam.submit]
    submit --> result[mahasiswa.exam.result]
```

**Process:** `mahasiswa.exam.log-violation` pada tab switch, copy, blur, fullscreen exit.

**Off-page:** [X-05b](../cross/X-05b.md) deteksi mahasiswa, [X-05](../cross/X-05.md) swimlane.
