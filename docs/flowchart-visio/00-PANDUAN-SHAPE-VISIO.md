# Panduan Shape Microsoft Visio

**Stencil:** More Shapes → **Basic Flowchart Shapes**

| Makna | Shape Visio | Contoh di SIAKAD |
|-------|-------------|------------------|
| Mulai / Selesai | **Terminator** (oval) | Login berhasil, logout |
| Langkah / aksi | **Process** (persegi) | `POST login`, `admin.krs.approve` |
| Ya / Tidak | **Decision** (belah ketupat) | Role valid? KRS disetujui? |
| File output | **Document** | Export PDF, download Word |
| Input form | **Data** (jajar genjang) | Filter laporan, form KRS |
| Database (opsional) | **Stored Data** (silinder) | Tabel `krs`, `payments` |
| Sub-diagram lain | **Predefined Process** | "Lihat ADM-09" |
| Halaman lain | **Off-page Connector** | DSN-04 → X-02 |
| Panah | **Dynamic Connector** | Label cabang: Ya / Tidak |

## Konvensi

- Alur utama: **atas → bawah**
- Setiap **Decision** = tepat **2** cabang berlabel
- Warna opsional: hijau sukses, merah error/403, kuning `pending`
- Di dalam shape Process, tulis: **Label singkat** + baris kedua `route: nama.route`

## Swimlane (alur lintas role)

Untuk X-01, X-02, X-04, X-05:

- Template: **Cross-Functional Flowchart**
- Lane: **Mahasiswa** | **Sistem** | **Admin** (atau **Dosen**)

## Checklist per halaman

- [ ] Terminator Mulai
- [ ] Semua Process punya label route
- [ ] Decision punya 2 cabang
- [ ] Terminator Selesai
- [ ] Off-page connector ke halaman terkait (jika ada)
