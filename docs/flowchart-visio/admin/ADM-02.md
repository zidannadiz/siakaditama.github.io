# ADM-02 — CRUD Admin Users

**Template:** [_TEMPLATE-CRUD.md](../_TEMPLATE-CRUD.md) — resource `admin.admin`

| Route | Method |
|-------|--------|
| `admin.admin.index` | GET |
| `admin.admin.create` | GET |
| `admin.admin.store` | POST |
| `admin.admin.edit` | GET |
| `admin.admin.update` | PUT |
| `admin.admin.destroy` | DELETE |

**Decision khusus (hapus):** `user_id == auth()->id()` → **Tidak** (blokir hapus diri sendiri).
