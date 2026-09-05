# ADM-04 — CRUD Mahasiswa

**Template:** [_TEMPLATE-CRUD.md](../_TEMPLATE-CRUD.md) — resource `admin.mahasiswa`

| Route | Catatan |
|-------|---------|
| `admin.mahasiswa.store` | Transaksi: buat `User` (role mahasiswa) + `Mahasiswa` |
| `admin.mahasiswa.update` | Update kedua entitas |
| `admin.mahasiswa.destroy` | Hapus + audit log |

**Decision:** Validasi gagal → rollback transaksi → `back()` dengan error.
