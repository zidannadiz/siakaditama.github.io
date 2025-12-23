# 📋 Fitur yang Belum Ada di Mobile App - Ringkasan

**Last Updated:** Desember 2024

---

## ✅ **Fitur yang Sudah Ada (28 fitur - 74%)**

### Core Features:

1. ✅ Authentication (Login/Logout)
2. ✅ Dashboard (Admin, Dosen, Mahasiswa)
3. ✅ Profile Management
4. ✅ Notifikasi

### Mahasiswa Features:

5. ✅ KRS Management
6. ✅ KHS View
7. ✅ Presensi (View)
8. ✅ Assignment/Tugas
9. ✅ Exam/Ujian
10. ✅ QR Code Presensi Scan

### Dosen Features:

11. ✅ Input Nilai
12. ✅ Input Presensi
13. ✅ Assignment Management
14. ✅ Exam Management
15. ✅ QR Code Presensi (Generate & Show)

### Admin Features:

16. ✅ CRUD Mahasiswa
17. ✅ CRUD Dosen
18. ✅ CRUD Prodi
19. ✅ CRUD Mata Kuliah
20. ✅ CRUD Semester
21. ✅ CRUD Jadwal Kuliah
22. ✅ KRS Approval
23. ✅ Pengumuman Management
24. ✅ Payment Management

### Fitur Umum:

25. ✅ Pengumuman (Public)
26. ✅ Chat
27. ✅ Payment (Public)
28. ✅ Forum
29. ✅ Q&A

---

## ❌ **Fitur yang Belum Ada (10 fitur - 26%)**

### 🔴 **Priority 1: Fitur Admin (Penting)**

#### 1. **Kalender Akademik**

-   [ ] List event akademik
-   [ ] Detail event
-   [ ] Filter by kategori/tanggal
-   [ ] Create/Edit/Delete event (Admin)

**API:** `/api/admin/kalender-akademik`

#### 2. **Laporan & Statistik**

-   [ ] Laporan Pembayaran (list, export Excel/PDF)
-   [ ] Laporan Akademik (list, export Excel/PDF)
-   [ ] Statistik Presensi (per mahasiswa, kelas, prodi)
-   [ ] Grafik & visualisasi

**API:**

-   `/api/admin/laporan/pembayaran`
-   `/api/admin/laporan/akademik`
-   `/api/admin/statistik-presensi`

#### 3. **System Settings**

-   [ ] Konfigurasi Bobot Penilaian
-   [ ] Konfigurasi Huruf Mutu
-   [ ] Pengaturan Semester Aktif
-   [ ] Konfigurasi Informasi Aplikasi

**API:** `/api/admin/system-settings`

#### 4. **Backup & Restore**

-   [ ] List backup
-   [ ] Create backup
-   [ ] Restore backup
-   [ ] Download backup

**API:** `/api/admin/backup/*`

#### 5. **Bank Management**

-   [ ] List bank
-   [ ] Edit bank
-   [ ] Toggle status bank

**API:** `/api/admin/bank/*`

#### 6. **Audit Log**

-   [ ] List aktivitas admin
-   [ ] Filter & search log
-   [ ] Detail log

**API:** `/api/admin/audit-log`

---

### 🟡 **Priority 2: Fitur Mahasiswa**

#### 7. **Export PDF**

-   [ ] Export KRS ke PDF
-   [ ] Export KHS ke PDF
-   [ ] Export Transkrip ke PDF

**API:**

-   `/api/mahasiswa/export/krs/{semester_id}`
-   `/api/mahasiswa/export/khs/{semester_id}`
-   `/api/mahasiswa/export/transcript`

---

### 🟢 **Priority 3: Fitur Tambahan (Opsional)**

#### 8. **Active Users (Admin)**

-   [ ] List user aktif
-   [ ] Real-time monitoring

**API:** `/api/admin/active-users`

---

## 📊 **Statistik**

-   **Total Fitur Web:** ~38 fitur
-   **Fitur Sudah Ada di Mobile:** 28 fitur (74%)
-   **Fitur Belum Ada:** 10 fitur (26%)
-   **Progress:** 74% Complete

---

## 🎯 **Rekomendasi Urutan Implementasi**

### **Minggu 1: Fitur Admin Penting**

1. Kalender Akademik
2. System Settings

### **Minggu 2: Laporan & Statistik**

3. Laporan Pembayaran
4. Laporan Akademik
5. Statistik Presensi

### **Minggu 3: Fitur Admin Tambahan**

6. Backup & Restore
7. Bank Management
8. Audit Log

### **Minggu 4: Fitur Mahasiswa**

9. Export PDF (KRS/KHS/Transkrip)

---

## 💡 **Catatan**

-   Sebagian besar fitur core sudah ada (74%)
-   Fitur yang belum sebagian besar untuk Admin
-   Export PDF penting untuk Mahasiswa
-   Kalender Akademik berguna untuk semua role

---

## 🚀 **Mulai dari Mana?**

**Rekomendasi:**

1. **Kalender Akademik** - Berguna untuk semua role
2. **Export PDF** - Penting untuk Mahasiswa
3. **System Settings** - Penting untuk Admin
