# SYS-00 — Login, Logout, Reset Password

**Visio page:** `SYS-00`

## Shape mapping

| Shape | Label | Route / URL |
|-------|-------|-------------|
| Terminator | Mulai | `GET /` → redirect |
| Process | Halaman login | `login` → `GET /login` |
| Process | Submit login | `POST /login` (throttle 5/min) |
| Decision | Rate limit OK? | middleware `throttle:5,1` |
| Decision | Kredensial valid? | `LoginController@login` |
| Process | Redirect dashboard | `dashboard` → `GET /dashboard` |
| Process | Logout | `logout` → `POST /logout` |
| Process | Lupa password form | `password.request` |
| Process | Kirim email reset | `password.email` |
| Process | Form reset token | `password.reset` |
| Process | Simpan password baru | `password.update` |
| Terminator | Selesai | — |

```mermaid
flowchart TD
    start([Mulai]) --> root["GET / → redirect login"]
    root --> loginForm["login - showLoginForm"]
    loginForm --> postLogin["POST login"]
    postLogin --> throttle{Rate limit OK?}
    throttle -->|Tidak| block[Tolak throttle]
    throttle -->|Ya| valid{Kredensial valid?}
    valid -->|Tidak| loginForm
    valid -->|Ya| dash["dashboard"]
    dash --> logout["logout"]
    logout --> endNode([Selesai])
    loginForm --> forgot["password.request"]
    forgot --> email["password.email"]
    email --> resetForm["password.reset"]
    resetForm --> resetPost["password.update"]
    resetPost --> loginForm
    block --> loginForm
```

**Off-page:** Lanjut ke [SYS-01](SYS-01.md) setelah login sukses.
