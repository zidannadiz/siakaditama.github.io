# MHS-13 — Pembayaran

| Shape | Route |
|-------|-------|
| Process | `payment.index` |
| Process | `payment.create` |
| Process | `payment.store` |
| Process | `payment.show` |
| Process | `payment.cancel` |
| Process | `payment.xendit.webhook` (sistem) |
| Terminator | Selesai |

**Decisions:**
- Min amount 1000; bank aktif
- `payment.cancel` hanya jika `pending` dan owner
- Admin verify: `admin.payment.verify` atau `payment.verify` + middleware admin

**Off-page:** [X-04](../cross/X-04.md)
