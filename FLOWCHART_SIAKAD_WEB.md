# 📊 Flowchart Alur Sistem SIAKAD Web

## 🎯 **Legenda Shape & Koneksi**

### **Shape yang Digunakan:**

1. **Terminator (Start/End)** - `[Start]` / `[End]`

    - Bentuk: Oval/Ellipse
    - Warna: Hijau (Start), Merah (End)
    - Digunakan untuk: Titik awal dan akhir proses

2. **Process (Proses)** - `[Proses]`

    - Bentuk: Rectangle
    - Warna: Biru
    - Digunakan untuk: Aktivitas/tindakan yang dilakukan

3. **Decision (Keputusan)** - `{Keputusan?}`

    - Bentuk: Diamond
    - Warna: Kuning
    - Digunakan untuk: Kondisi/perbandingan/logika

4. **Input/Output** - `[Input/Output]`

    - Bentuk: Parallelogram
    - Warna: Cyan
    - Digunakan untuk: Input data atau output hasil

5. **Predefined Process** - `[Subroutine]`

    - Bentuk: Rectangle dengan garis ganda
    - Warna: Biru muda
    - Digunakan untuk: Proses yang sudah didefinisikan sebelumnya

6. **Document** - `[Dokumen]`

    - Bentuk: Rectangle dengan gelombang di bawah
    - Warna: Putih
    - Digunakan untuk: Dokumen/laporan

7. **Database** - `[Database]`
    - Bentuk: Cylinder
    - Warna: Abu-abu
    - Digunakan untuk: Penyimpanan data

### **Koneksi:**

-   **Garis Lurus** → : Alur normal
-   **Garis Putus-putus** - - - : Alur alternatif/opsional
-   **Panah dengan Label** → [Ya] / → [Tidak] : Kondisi dari decision

---

## 🔐 **1. ALUR AUTHENTICATION (Login/Logout)**

```
[Start]
    ↓
[User Buka Halaman Login]
    ↓
[Input Email & Password]
    ↓
{Validasi Input?}
    ↓ [Tidak]
[Error: Field harus diisi]
    ↓
[Kembali ke Form]
    ↓
    ↓ [Ya]
[Kirim Request ke API /login]
    ↓
{Login Berhasil?}
    ↓ [Tidak]
[Error: Email/Password salah]
    ↓
[Kembali ke Form]
    ↓
    ↓ [Ya]
[Simpan Token & User Data]
    ↓
{Check Role User}
    ↓
    ├─→ [Admin] → [Redirect ke Dashboard Admin]
    ├─→ [Dosen] → [Redirect ke Dashboard Dosen]
    └─→ [Mahasiswa] → [Redirect ke Dashboard Mahasiswa]
    ↓
[End]

[User Klik Logout]
    ↓
{Konfirmasi Logout?}
    ↓ [Tidak]
[Cancel]
    ↓
    ↓ [Ya]
[Kirim Request ke API /logout]
    ↓
[Hapus Token & Session]
    ↓
[Redirect ke Halaman Login]
    ↓
[End]
```

---

## 👨‍💼 **2. ALUR ADMIN**

### **2.1 Dashboard Admin**

```
[Start]
    ↓
[Load Dashboard Data]
    ├─→ [Get Statistics]
    │   ├─→ Total Mahasiswa
    │   ├─→ Total Dosen
    │   ├─→ Total Prodi
    │   ├─→ Total Mata Kuliah
    │   └─→ KRS Pending
    ↓
[Display Dashboard]
    ├─→ [Statistics Cards]
    ├─→ [Menu Cards]
    └─→ [Recent Activities]
    ↓
[End]
```

### **2.2 CRUD Mahasiswa**

```
[Start]
    ↓
[Admin Buka Menu Mahasiswa]
    ↓
[Load List Mahasiswa]
    ├─→ [Get dari API /admin/mahasiswa]
    ├─→ [Filter & Search]
    └─→ [Pagination]
    ↓
[Display List Mahasiswa]
    ↓
{Action?}
    ↓
    ├─→ [Tambah] → [Form Tambah Mahasiswa]
    │   ├─→ [Input Data]
    │   ├─→ [Validasi]
    │   ├─→ [Submit ke API]
    │   └─→ [Refresh List]
    │
    ├─→ [Edit] → [Form Edit Mahasiswa]
    │   ├─→ [Load Data Existing]
    │   ├─→ [Edit Data]
    │   ├─→ [Validasi]
    │   ├─→ [Submit ke API]
    │   └─→ [Refresh List]
    │
    ├─→ [Hapus] → {Konfirmasi?}
    │   ├─→ [Ya] → [Delete via API] → [Refresh List]
    │   └─→ [Tidak] → [Cancel]
    │
    └─→ [Detail] → [Detail Mahasiswa]
        ├─→ [View Data]
        ├─→ [View KRS History]
        └─→ [View KHS History]
    ↓
[End]
```

### **2.3 KRS Approval**

```
[Start]
    ↓
[Admin Buka Menu KRS Approval]
    ↓
[Load List KRS Pending]
    ├─→ [Get dari API /admin/krs]
    ├─→ [Filter: status = pending]
    └─→ [Pagination]
    ↓
[Display List KRS]
    ↓
{Action?}
    ↓
    ├─→ [Approve] → {Konfirmasi?}
    │   ├─→ [Ya] → [POST /admin/krs/{id}/approve]
    │   │   ├─→ [Update Status KRS]
    │   │   ├─→ [Create Notifikasi untuk Mahasiswa]
    │   │   └─→ [Refresh List]
    │   └─→ [Tidak] → [Cancel]
    │
    └─→ [Reject] → [Form Reject]
        ├─→ [Input Alasan]
        ├─→ [POST /admin/krs/{id}/reject]
        ├─→ [Update Status KRS]
        ├─→ [Create Notifikasi untuk Mahasiswa]
        └─→ [Refresh List]
    ↓
[End]
```

### **2.4 Payment Management**

```
[Start]
    ↓
[Admin Buka Menu Payment]
    ↓
[Load List Payments]
    ├─→ [Get dari API /admin/payment]
    ├─→ [Filter: status, date, bank]
    └─→ [Pagination]
    ↓
[Display List Payments]
    ↓
{Action?}
    ↓
    ├─→ [Verify] → {Konfirmasi?}
    │   ├─→ [Ya] → [POST /admin/payment/{id}/verify]
    │   │   ├─→ [Update Status = paid]
    │   │   └─→ [Refresh List]
    │   └─→ [Tidak] → [Cancel]
    │
    ├─→ [Cancel] → {Konfirmasi?}
    │   ├─→ [Ya] → [POST /admin/payment/{id}/cancel]
    │   │   ├─→ [Update Status = cancelled]
    │   │   └─→ [Refresh List]
    │   └─→ [Tidak] → [Cancel]
    │
    └─→ [Detail] → [Detail Payment]
        ├─→ [View Payment Info]
        ├─→ [View Bukti Transfer]
        └─→ [View History]
    ↓
[End]
```

### **2.5 Backup & Restore**

```
[Start]
    ↓
[Admin Buka Menu Backup]
    ↓
[Load List Backups]
    ├─→ [Get dari API /admin/backup]
    └─→ [Sort by Date Desc]
    ↓
[Display List Backups]
    ↓
{Action?}
    ↓
    ├─→ [Create Backup] → {Konfirmasi?}
    │   ├─→ [Ya] → [POST /admin/backup]
    │   │   ├─→ [Execute Backup Process]
    │   │   │   ├─→ [SQLite: Create SQL Dump]
    │   │   │   └─→ [MySQL: Execute mysqldump]
    │   │   ├─→ [Save Backup File]
    │   │   ├─→ [Log Audit]
    │   │   └─→ [Refresh List]
    │   └─→ [Tidak] → [Cancel]
    │
    ├─→ [Restore] → {PERINGATAN!}
    │   ├─→ [Ya] → [POST /admin/backup/restore]
    │   │   ├─→ [Backup Current DB]
    │   │   ├─→ [Execute Restore Process]
    │   │   ├─→ [Log Audit]
    │   │   └─→ [Refresh List]
    │   └─→ [Tidak] → [Cancel]
    │
    └─→ [Delete] → {Konfirmasi?}
        ├─→ [Ya] → [DELETE /admin/backup/{filename}]
        │   ├─→ [Delete File]
        │   └─→ [Refresh List]
        └─→ [Tidak] → [Cancel]
    ↓
[End]
```

---

## 👨‍🏫 **3. ALUR DOSEN**

### **3.1 Dashboard Dosen**

```
[Start]
    ↓
[Load Dashboard Data]
    ├─→ [Get Jadwal Mengajar]
    ├─→ [Get Presensi Today]
    └─→ [Get Assignment/Exam Pending]
    ↓
[Display Dashboard]
    ├─→ [Jadwal Hari Ini]
    ├─→ [Presensi Stats]
    └─→ [Menu Cards]
    ↓
[End]
```

### **3.2 Input Nilai**

```
[Start]
    ↓
[Dosen Buka Menu Input Nilai]
    ↓
[Load List Jadwal Mengajar]
    ├─→ [Get dari API /dosen/jadwal]
    └─→ [Filter: Semester Aktif]
    ↓
[Pilih Jadwal]
    ↓
[Load List Mahasiswa di Jadwal]
    ├─→ [Get dari API /dosen/jadwal/{id}/mahasiswa]
    └─→ [Get Existing Nilai]
    ↓
[Display Form Input Nilai]
    ├─→ [List Mahasiswa]
    ├─→ [Input Nilai Tugas]
    ├─→ [Input Nilai UTS]
    └─→ [Input Nilai UAS]
    ↓
[Submit Nilai]
    ↓
{Validasi?}
    ↓ [Tidak]
[Error: Nilai harus 0-100]
    ↓
    ↓ [Ya]
[POST /dosen/nilai]
    ├─→ [Calculate Nilai Akhir]
    ├─→ [Calculate Bobot]
    ├─→ [Update/Insert Nilai]
    └─→ [Create Notifikasi untuk Mahasiswa]
    ↓
[Success Message]
    ↓
[End]
```

### **3.3 Input Presensi**

```
[Start]
    ↓
[Dosen Buka Menu Input Presensi]
    ↓
[Load List Jadwal Mengajar]
    ↓
[Pilih Jadwal & Pertemuan]
    ↓
[Load List Mahasiswa di Jadwal]
    ↓
[Display Form Presensi]
    ├─→ [List Mahasiswa]
    └─→ [Status: Hadir/Izin/Sakit/Alpa]
    ↓
[Input Presensi per Mahasiswa]
    ↓
[Submit Presensi]
    ↓
[POST /dosen/presensi]
    ├─→ [Save Presensi]
    └─→ [Create Notifikasi untuk Mahasiswa]
    ↓
[Success Message]
    ↓
[End]
```

### **3.4 QR Code Presensi**

```
[Start]
    ↓
[Dosen Buka Menu QR Presensi]
    ↓
[Load List Jadwal Mengajar]
    ↓
[Pilih Jadwal]
    ↓
[Form Generate QR]
    ├─→ [Input Pertemuan]
    ├─→ [Input Tanggal]
    └─→ [Input Durasi (menit)]
    ↓
[Generate QR Code]
    ↓
[POST /dosen/qr-presensi/generate]
    ├─→ [Create QR Session]
    ├─→ [Generate Token]
    └─→ [Set Expiry Time]
    ↓
[Display QR Code]
    ├─→ [Show QR Image]
    ├─→ [Show Countdown Timer]
    └─→ [Button Stop Session]
    ↓
{Action?}
    ↓
    ├─→ [Stop Session] → [POST /dosen/qr-presensi/{token}/stop]
    │   └─→ [Update Session Status]
    │
    └─→ [Timer Expired] → [Auto Stop Session]
    ↓
[End]
```

---

## 👨‍🎓 **4. ALUR MAHASISWA**

### **4.1 Dashboard Mahasiswa**

```
[Start]
    ↓
[Load Dashboard Data]
    ├─→ [Get KRS Status]
    ├─→ [Get Presensi Stats]
    ├─→ [Get Assignment/Exam Pending]
    └─→ [Get Notifikasi Unread]
    ↓
[Display Dashboard]
    ├─→ [Quick Stats]
    ├─→ [Menu Cards]
    └─→ [Recent Notifications]
    ↓
[End]
```

### **4.2 KRS Management**

```
[Start]
    ↓
[Mahasiswa Buka Menu KRS]
    ↓
[Load List KRS]
    ├─→ [Get dari API /mahasiswa/krs]
    └─→ [Filter: Semester Aktif]
    ↓
[Display List KRS]
    ↓
{Action?}
    ↓
    ├─→ [Tambah KRS] → [Form Tambah KRS]
    │   ├─→ [Load List Mata Kuliah Available]
    │   ├─→ [Filter: Prodi, Semester]
    │   ├─→ [Pilih Mata Kuliah]
    │   ├─→ [Check Prasyarat]
    │   ├─→ [Check SKS Limit]
    │   ├─→ [Check Jadwal Conflict]
    │   ├─→ [Validasi]
    │   ├─→ [POST /mahasiswa/krs]
    │   │   ├─→ [Create KRS]
    │   │   ├─→ [Status: Pending]
    │   │   └─→ [Create Notifikasi untuk Admin]
    │   └─→ [Refresh List]
    │
    └─→ [Hapus KRS] → {Konfirmasi?}
        ├─→ [Ya] → [DELETE /mahasiswa/krs/{id}]
        │   ├─→ [Check: Status = Pending?]
        │   ├─→ [Delete KRS]
        │   └─→ [Refresh List]
        └─→ [Tidak] → [Cancel]
    ↓
[End]
```

### **4.3 KHS (Kartu Hasil Studi)**

```
[Start]
    ↓
[Mahasiswa Buka Menu KHS]
    ↓
[Load List Semester]
    ├─→ [Get dari API /mahasiswa/khs]
    └─→ [Sort by Semester]
    ↓
[Pilih Semester]
    ↓
[Load KHS Data]
    ├─→ [Get Nilai per Mata Kuliah]
    ├─→ [Calculate IPK Semester]
    └─→ [Calculate IPK Kumulatif]
    ↓
[Display KHS]
    ├─→ [List Nilai]
    ├─→ [IPK Semester]
    ├─→ [IPK Kumulatif]
    ├─→ [Total SKS]
    └─→ [Button Export PDF]
    ↓
{Export PDF?}
    ↓ [Ya]
[GET /mahasiswa/khs/{semester_id}/export]
    └─→ [Download PDF]
    ↓
[End]
```

### **4.4 Assignment/Tugas**

```
[Start]
    ↓
[Mahasiswa Buka Menu Assignment]
    ↓
[Load List Assignment]
    ├─→ [Get dari API /mahasiswa/assignment]
    └─→ [Filter: Status, Deadline]
    ↓
[Display List Assignment]
    ↓
[Pilih Assignment]
    ↓
[Load Detail Assignment]
    ├─→ [Get dari API /mahasiswa/assignment/{id}]
    ├─→ [Get Submission Status]
    └─→ [Get File Tugas]
    ↓
[Display Detail Assignment]
    ├─→ [Info Tugas]
    ├─→ [File Tugas]
    ├─→ [Deadline]
    └─→ [Form Submit]
    ↓
{Submit Tugas?}
    ↓ [Ya]
[Form Submit Tugas]
    ├─→ [Pilih File]
    ├─→ [Upload File]
    └─→ [POST /mahasiswa/assignment/{id}/submit]
        ├─→ [Save Submission]
        ├─→ [Upload File]
        └─→ [Create Notifikasi untuk Dosen]
    ↓
[Success Message]
    ↓
[End]
```

### **4.5 Scan QR Presensi**

```
[Start]
    ↓
[Mahasiswa Buka Menu Scan QR Presensi]
    ↓
[Request Camera Permission]
    ↓
{Permission Granted?}
    ↓ [Tidak]
[Error: Camera permission required]
    ↓
    ↓ [Ya]
[Initialize Camera]
    ↓
[Display QR Scanner]
    ↓
[Scan QR Code]
    ↓
{QR Valid?}
    ↓ [Tidak]
[Error: QR Code tidak valid]
    ↓
    ↓ [Ya]
[POST /mahasiswa/qr-presensi/scan]
    ├─→ [Validate QR Token]
    ├─→ [Check: Already Scanned?]
    ├─→ [Check: Expired?]
    ├─→ [Check: Jadwal Match?]
    ├─→ [Save Presensi]
    └─→ [Create Notifikasi untuk Dosen]
    ↓
{Success?}
    ↓ [Tidak]
[Error Message]
    ↓
    ↓ [Ya]
[Success Message]
    ↓
[End]
```

---

## 📊 **5. ALUR LAPORAN & STATISTIK (Admin)**

### **5.1 Laporan Pembayaran**

```
[Start]
    ↓
[Admin Buka Menu Laporan Pembayaran]
    ↓
[Load Data]
    ├─→ [Get dari API /admin/laporan/pembayaran]
    ├─→ [Apply Filters]
    │   ├─→ Status
    │   ├─→ Payment Type
    │   ├─→ Bank
    │   ├─→ Mahasiswa
    │   └─→ Date Range
    └─→ [Calculate Statistics]
    ↓
[Display Laporan]
    ├─→ [Statistics Cards]
    ├─→ [List Payments]
    └─→ [Action Buttons]
    ↓
{Action?}
    ↓
    ├─→ [Export Excel] → [GET /admin/laporan/pembayaran/export-excel]
    │   └─→ [Download Excel File]
    │
    └─→ [Export PDF] → [GET /admin/laporan/pembayaran/export-pdf]
        └─→ [Download PDF File]
    ↓
[End]
```

### **5.2 Laporan Akademik**

```
[Start]
    ↓
[Admin Buka Menu Laporan Akademik]
    ↓
[Load Data]
    ├─→ [Get dari API /admin/laporan/akademik]
    ├─→ [Apply Filters]
    │   ├─→ Prodi
    │   └─→ Semester
    └─→ [Calculate IPK per Mahasiswa]
    ↓
[Display Laporan]
    ├─→ [Statistics Cards]
    │   ├─→ Total Mahasiswa
    │   ├─→ Rata-rata IPK
    │   ├─→ Lulus
    │   └─→ Tidak Lulus
    ├─→ [List Mahasiswa dengan IPK]
    └─→ [Action Buttons]
    ↓
{Action?}
    ↓
    ├─→ [Export Excel] → [GET /admin/laporan/akademik/export-excel]
    │   └─→ [Download Excel File]
    │
    ├─→ [Export PDF] → [GET /admin/laporan/akademik/export-pdf]
    │   └─→ [Download PDF File]
    │
    └─→ [Statistik Presensi] → [Load Statistik Presensi]
    ↓
[End]
```

---

## 🔔 **6. ALUR NOTIFIKASI (Semua Role)**

```
[Start]
    ↓
[User Buka Menu Notifikasi]
    ↓
[Load List Notifikasi]
    ├─→ [Get dari API /notifikasi]
    ├─→ [Filter: Unread/All]
    └─→ [Sort by Date Desc]
    ↓
[Display List Notifikasi]
    ├─→ [Unread Badge]
    └─→ [List Items]
    ↓
{Action?}
    ↓
    ├─→ [Mark as Read] → [POST /notifikasi/{id}/read]
    │   └─→ [Update Status]
    │
    ├─→ [Mark All as Read] → [POST /notifikasi/read-all]
    │   └─→ [Update All Status]
    │
    └─→ [View Detail] → [Detail Notifikasi]
    ↓
[Refresh List]
    ↓
[End]
```

---

## 💬 **7. ALUR CHAT (Semua Role)**

```
[Start]
    ↓
[User Buka Menu Chat]
    ↓
[Load List Conversations]
    ├─→ [Get dari API /chat]
    └─→ [Sort by Last Message]
    ↓
[Display List Conversations]
    ├─→ [Unread Count Badge]
    └─→ [List Items]
    ↓
{Action?}
    ↓
    ├─→ [Pilih Conversation] → [Load Chat Detail]
    │   ├─→ [Get dari API /chat/{id}]
    │   ├─→ [Load Messages]
    │   └─→ [Display Chat]
    │   ↓
    │   [Send Message]
    │   ├─→ [Input Message]
    │   ├─→ [POST /chat/{id}/message]
    │   │   ├─→ [Save Message]
    │   │   └─→ [Create Notifikasi untuk Recipient]
    │   └─→ [Refresh Messages]
    │
    └─→ [New Conversation] → [Form New Chat]
        ├─→ [Pilih Recipient]
        ├─→ [POST /chat]
        └─→ [Redirect ke Chat Detail]
    ↓
[End]
```

---

## 💳 **8. ALUR PAYMENT (Mahasiswa)**

```
[Start]
    ↓
[Mahasiswa Buka Menu Payment]
    ↓
[Load List Payments]
    ├─→ [Get dari API /payment]
    └─→ [Filter: Status, Type]
    ↓
[Display List Payments]
    ↓
{Action?}
    ↓
    ├─→ [Create Payment] → [Form Create Payment]
    │   ├─→ [Pilih Payment Type]
    │   ├─→ [Input Amount]
    │   ├─→ [Pilih Bank]
    │   ├─→ [Validasi]
    │   ├─→ [POST /payment]
    │   │   ├─→ [Create Payment]
    │   │   ├─→ [Generate Invoice Number]
    │   │   ├─→ [Generate Virtual Account]
    │   │   └─→ [Create Notifikasi]
    │   └─→ [Redirect ke Detail Payment]
    │
    └─→ [Detail Payment] → [Detail Payment]
        ├─→ [View Payment Info]
        ├─→ [View Virtual Account]
        ├─→ [Button Check Status]
        └─→ [Button Cancel Payment]
    ↓
[End]
```

---

## 🔐 **9. ALUR SYSTEM SETTINGS (Admin)**

```
[Start]
    ↓
[Admin Buka Menu System Settings]
    ↓
[Load Settings]
    ├─→ [Get dari API /admin/system-settings]
    ├─→ [Active Semester]
    ├─→ [Grading Weights]
    ├─→ [Letter Grades]
    └─→ [App Info]
    ↓
[Display Settings dengan Tabs]
    ├─→ [Tab: Semester Aktif]
    ├─→ [Tab: Bobot Penilaian]
    ├─→ [Tab: Huruf Mutu]
    └─→ [Tab: Info Aplikasi]
    ↓
{Tab Selected?}
    ↓
    ├─→ [Semester Aktif] → [Form Pilih Semester]
    │   ├─→ [Pilih Semester]
    │   ├─→ [POST /admin/system-settings/semester]
    │   │   ├─→ [Update Active Semester]
    │   │   ├─→ [Deactivate Old Semester]
    │   │   └─→ [Log Audit]
    │   └─→ [Success Message]
    │
    ├─→ [Bobot Penilaian] → [Form Bobot]
    │   ├─→ [Input Bobot Tugas]
    │   ├─→ [Input Bobot UTS]
    │   ├─→ [Input Bobot UAS]
    │   ├─→ [Validasi: Total = 100%]
    │   ├─→ [POST /admin/system-settings/grading]
    │   │   ├─→ [Update Grading Weights]
    │   │   └─→ [Log Audit]
    │   └─→ [Success Message]
    │
    ├─→ [Huruf Mutu] → [List & CRUD]
    │   ├─→ [List Letter Grades]
    │   ├─→ [Tambah] → [Form Tambah]
    │   ├─→ [Edit] → [Form Edit]
    │   ├─→ [Hapus] → [Soft Delete]
    │   └─→ [POST/PUT/DELETE /admin/system-settings/letter-grades]
    │
    └─→ [Info Aplikasi] → [Form Info]
        ├─→ [Input: Name, Institution, Address, etc]
        ├─→ [POST /admin/system-settings/app-info]
        └─→ [Success Message]
    ↓
[End]
```

---

## 📝 **10. DIAGRAM ALUR UTAMA (Main Flow)**

```
                    [Start Application]
                            ↓
                    [Check Authentication]
                            ↓
                    {User Logged In?}
                            ↓ [Tidak]
                    [Redirect ke Login]
                            ↓
                    [User Login]
                            ↓
                    {Login Success?}
                            ↓ [Tidak]
                    [Show Error]
                            ↓
                            ↓ [Ya]
                    [Check User Role]
                            ↓
        ┌───────────────────┼───────────────────┐
        ↓                   ↓                   ↓
    [Admin]            [Dosen]          [Mahasiswa]
        ↓                   ↓                   ↓
[Admin Dashboard]  [Dosen Dashboard]  [Mahasiswa Dashboard]
        ↓                   ↓                   ↓
    [Menu]              [Menu]              [Menu]
        ↓                   ↓                   ↓
    [Features]          [Features]          [Features]
        ↓                   ↓                   ↓
    [Actions]           [Actions]           [Actions]
        ↓                   ↓                   ↓
    [API Calls]         [API Calls]         [API Calls]
        ↓                   ↓                   ↓
    [Database]         [Database]           [Database]
        ↓                   ↓                   ↓
    [Response]         [Response]           [Response]
        ↓                   ↓                   ↓
    [Update UI]        [Update UI]          [Update UI]
        ↓                   ↓                   ↓
        └───────────────────┼───────────────────┘
                            ↓
                    [User Logout]
                            ↓
                    [Clear Session]
                            ↓
                    [Redirect ke Login]
                            ↓
                        [End]
```

---

## 🎨 **11. CONTOH FLOWCHART DETAIL (KRS Approval Process)**

```
                    [Start]
                        ↓
        [Admin Buka Menu KRS Approval]
                        ↓
        [Load List KRS Pending]
                        ↓
        [Display List KRS]
                        ↓
        [Admin Pilih KRS]
                        ↓
        [Load Detail KRS]
            ├─→ [Info Mahasiswa]
            ├─→ [List Mata Kuliah]
            ├─→ [Total SKS]
            └─→ [Status]
                        ↓
        {Action?}
            ├─→ [Approve]
            │       ↓
            │   {Konfirmasi?}
            │       ├─→ [Ya]
            │       │       ↓
            │       │   [POST /admin/krs/{id}/approve]
            │       │       ↓
            │       │   [Update KRS Status = 'disetujui']
            │       │       ↓
            │       │   [Create Notifikasi untuk Mahasiswa]
            │       │       ↓
            │       │   [Log Audit]
            │       │       ↓
            │       │   [Success Message]
            │       │       ↓
            │       │   [Refresh List]
            │       │
            │       └─→ [Tidak] → [Cancel]
            │
            └─→ [Reject]
                    ↓
                [Form Reject]
                    ↓
                [Input Alasan]
                    ↓
                {Konfirmasi?}
                    ├─→ [Ya]
                    │       ↓
                    │   [POST /admin/krs/{id}/reject]
                    │       ↓
                    │   [Update KRS Status = 'ditolak']
                    │       ↓
                    │   [Save Alasan]
                    │       ↓
                    │   [Create Notifikasi untuk Mahasiswa]
                    │       ↓
                    │   [Log Audit]
                    │       ↓
                    │   [Success Message]
                    │       ↓
                    │   [Refresh List]
                    │
                    └─→ [Tidak] → [Cancel]
                        ↓
                    [End]
```

---

## 📋 **12. LEGENDA KONEKSI DETAIL**

### **Tipe Koneksi:**

1. **Sequential Flow** (Alur Berurutan)

    ```
    [A] → [B] → [C]
    ```

    - Proses berjalan secara berurutan
    - Tidak ada kondisi/percabangan

2. **Decision Flow** (Alur dengan Keputusan)

    ```
    [A] → {Condition?} → [Ya] → [B]
                     └→ [Tidak] → [C]
    ```

    - Ada kondisi yang harus dievaluasi
    - Hasil menentukan alur selanjutnya

3. **Parallel Flow** (Alur Paralel)

    ```
    [A] → [B] ──→ [C]
         └─→ [D] ──→ [E]
    ```

    - Beberapa proses berjalan bersamaan
    - Tidak saling bergantung

4. **Loop Flow** (Alur Perulangan)

    ```
    [A] → [B] → {Condition?} → [Ya] → [A]
                     └→ [Tidak] → [C]
    ```

    - Proses diulang sampai kondisi terpenuhi

5. **Merge Flow** (Alur Penggabungan)
    ```
    [A] → [C]
    [B] → [C]
    ```
    - Beberapa alur bergabung menjadi satu

---

## 🎯 **KESIMPULAN**

Flowchart ini menggambarkan alur lengkap sistem SIAKAD web dengan:

-   ✅ **10+ Alur Utama** (Authentication, Admin, Dosen, Mahasiswa, dll)
-   ✅ **30+ Fitur Detail** (CRUD, Approval, Input, dll)
-   ✅ **Shape Standar** (Terminator, Process, Decision, dll)
-   ✅ **Koneksi Jelas** (Sequential, Decision, Parallel, Loop, Merge)

Semua alur mengikuti pola yang sama:

1. **Start** → User Action
2. **Load Data** → API Call
3. **Display** → Show UI
4. **User Action** → Process
5. **API Call** → Database
6. **Response** → Update UI
7. **End** → Complete

---

**Dokumen ini dapat digunakan sebagai referensi untuk:**

-   Dokumentasi sistem
-   Onboarding developer baru
-   Testing & QA
-   Maintenance & debugging
