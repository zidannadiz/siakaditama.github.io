# Fitur yang Belum Ada untuk Menyempurnakan SIAKAD

Berdasarkan analisis sistem SIAKAD Anda, berikut adalah fitur-fitur yang masih belum ada dan direkomendasikan untuk ditambahkan, diurutkan berdasarkan prioritas:

## 🔴 Prioritas Tinggi (Penting untuk Produksi)

### 1. **Forgot Password / Reset Password via Email** ⚠️
**Status:** ❌ Belum Ada  
**Prioritas:** 🔴 Sangat Tinggi  
**Deskripsi:**
- Fitur reset password via email untuk semua role (admin, dosen, mahasiswa)
- Link reset password yang dikirim via email
- Token reset password dengan expiry time
- Form reset password yang aman

**Implementasi:**
- Laravel sudah punya built-in password reset (tinggal diaktifkan)
- Membutuhkan routes, views, dan email template
- Sudah ada migration `password_reset_tokens`

**Dampak:** Sangat penting untuk user experience dan keamanan

---

### 2. **System Settings / Konfigurasi Sistem** ⚙️
**Status:** ✅ **SUDAH ADA**  
**Prioritas:** ~~🔴 Tinggi~~ (Sudah Terealisasi)  
**Deskripsi:**
- ✅ Halaman pengaturan sistem untuk admin
- ✅ Konfigurasi bobot penilaian (Tugas: 30%, UTS: 30%, UAS: 40%)
- ✅ Konfigurasi huruf mutu (A, A-, B+, B, dll)
- ✅ Pengaturan semester aktif
- ✅ Pengaturan aplikasi (nama sistem, logo, favicon)

**Catatan:** Fitur ini sudah lengkap dengan tab-tab: Semester Aktif, Bobot Penilaian, Huruf Mutu & Bobot, dan Informasi Aplikasi.

---

### 3. **Peringatan Akademik (Academic Warning)** 📊
**Status:** ❌ Belum Ada  
**Prioritas:** 🔴 Tinggi  
**Deskripsi:**
- Sistem peringatan otomatis untuk mahasiswa dengan IPK rendah
- Kategori peringatan:
  - Peringatan 1: IPK < 2.00
  - Peringatan 2: IPK < 1.50
  - Peringatan 3: IPK < 1.00 (Drop Out Warning)
- Notifikasi otomatis ke mahasiswa dan admin
- History peringatan per semester

**Manfaat:**
- Monitoring performa akademik mahasiswa
- Early warning system untuk mencegah drop out
- Data untuk evaluasi akademik

**Implementasi:**
- Tabel `academic_warnings`
- Job/Command untuk auto-check dan generate peringatan
- Notifikasi email ke mahasiswa

---

### 4. **Absensi Dosen (Lecturer Attendance)** 👨‍🏫
**Status:** ❌ Belum Ada  
**Prioritas:** 🟡 Sedang  
**Deskripsi:**
- Tracking kehadiran dosen saat mengajar
- Absensi dosen per pertemuan/jadwal kuliah
- Status: Hadir, Tidak Hadir, Izin, Sakit
- Laporan absensi dosen per bulan/semester
- Notifikasi jika dosen tidak hadir

**Manfaat:**
- Monitoring kehadiran dosen
- Data untuk evaluasi kinerja dosen
- Transparansi sistem presensi

**Implementasi:**
- Tabel `dosen_absensi` atau tambah kolom di jadwal kuliah
- Controller untuk input absensi (admin atau otomatis)
- Dashboard dan laporan untuk admin

---

## 🟡 Prioritas Sedang (Menambah Value)

### 5. **Konsultasi Akademik (Academic Advising)** 💬
**Status:** ❌ Belum Ada  
**Prioritas:** 🟡 Sedang  
**Deskripsi:**
- Sistem konsultasi antara mahasiswa dengan dosen pembimbing akademik (PA)
- Mahasiswa bisa request konsultasi
- Dosen PA bisa approve/reject jadwal konsultasi
- Tracking history konsultasi
- Notifikasi untuk jadwal konsultasi

**Implementasi:**
- Tabel `academic_consultations`
- Relationship dosen PA dengan mahasiswa (1 dosen : banyak mahasiswa)
- CRUD untuk konsultasi
- Calendar integration

---

### 6. **Kurikulum & Rencana Studi Otomatis** 📚
**Status:** ❌ Belum Ada  
**Prioritas:** 🟡 Sedang  
**Deskripsi:**
- Master data kurikulum per program studi
- Rencana studi per semester (S1, S2, S3, dst) dengan mata kuliah wajib/prasyarat
- Auto-suggest mata kuliah saat mahasiswa ambil KRS
- Validasi prasyarat otomatis (misalnya: harus lulus Kalkulus 1 sebelum ambil Kalkulus 2)
- Tracking progress mahasiswa terhadap kurikulum

**Manfaat:**
- Panduan jelas untuk mahasiswa
- Mencegah kesalahan ambil mata kuliah
- Monitoring progress akademik

**Implementasi:**
- Tabel `kurikulums`, `rencana_studis`, `mata_kuliah_prasyarats`
- Service untuk validasi prasyarat
- Auto-suggest di form KRS

---

### 7. **Transkrip Akademik Resmi dengan Digital Signature** 🎓
**Status:** ⚠️ Sebagian Ada (transkrip ada, tapi belum resmi)  
**Prioritas:** 🟡 Sedang  
**Deskripsi:**
- Transkrip akademik dengan cap/stempel digital
- QR code untuk verifikasi keaslian transkrip
- Watermark dengan logo institusi
- Format PDF resmi sesuai standar
- Download transkrip yang sudah dicap

**Implementasi:**
- Update view transkrip dengan digital signature
- QR code generator untuk verifikasi
- Template PDF yang lebih formal

---

### 8. **Dashboard Analytics & Statistik Lanjutan** 📈
**Status:** ⚠️ Basic ada, perlu diperkaya  
**Prioritas:** 🟡 Sedang  
**Deskripsi:**
- Grafik dan chart interaktif di dashboard admin
- Statistik:
  - Jumlah mahasiswa per prodi (pie chart)
  - Trend nilai per semester (line chart)
  - Statistik presensi per bulan (bar chart)
  - Distribusi IPK mahasiswa (histogram)
  - Tingkat kelulusan per mata kuliah
- Export statistik ke Excel/PDF

**Implementasi:**
- Library chart.js atau Chart.js
- Query agregasi untuk statistik
- View dashboard dengan chart

---

### 9. **Batch Operation untuk Admin** 📦
**Status:** ❌ Belum Ada  
**Prioritas:** 🟡 Sedang  
**Deskripsi:**
- Import/Export data via Excel untuk:
  - Import mahasiswa baru (batch)
  - Import nilai (batch)
  - Import jadwal kuliah (batch)
  - Export data ke Excel
- Template Excel untuk import
- Validasi data sebelum import

**Manfaat:**
- Efisiensi untuk input data besar
- Menghemat waktu admin

**Implementasi:**
- Package Laravel Excel (Maatwebsite\Excel)
- Controller untuk import/export
- Template Excel

---

## 🟢 Prioritas Rendah (Nice to Have)

### 10. **Sistem Perpustakaan Digital** 📖
**Status:** ❌ Belum Ada  
**Prioritas:** 🟢 Rendah  
**Deskripsi:**
- Katalog buku/jurnal digital
- Peminjaman buku online
- Tracking peminjaman dan pengembalian
- History peminjaman per user
- Notifikasi pengembalian

---

### 11. **Sistem Komplain/Saran** 💭
**Status:** ❌ Belum Ada  
**Prioritas:** 🟢 Rendah  
**Deskripsi:**
- Form komplain/saran untuk mahasiswa
- Kategori komplain (Akademik, Administrasi, Fasilitas, dll)
- Tracking status komplain (Open, In Progress, Resolved, Closed)
- Admin bisa menanggapi komplain
- Notifikasi untuk update status

---

### 12. **Pengumuman dengan Attachment** 📎
**Status:** ⚠️ Sebagian Ada (pengumuman ada, belum support file)  
**Prioritas:** 🟢 Rendah  
**Deskripsi:**
- Upload file (PDF, DOC, dll) pada pengumuman
- Download attachment dari pengumuman
- Preview file langsung di browser

---

### 13. **Multi-Level Approval untuk KRS** ✅
**Status:** ⚠️ Single approval (hanya admin)  
**Prioritas:** 🟢 Rendah  
**Deskripsi:**
- Approval bertingkat (misalnya: Dosen PA → Admin)
- History approval
- Komentar saat approve/reject

---

### 14. **Push Notifications untuk Mobile** 📱
**Status:** ❌ Belum Ada  
**Prioritas:** 🟢 Rendah  
**Deskripsi:**
- Push notifications untuk aplikasi mobile
- Notifikasi real-time untuk:
  - Pengumuman baru
  - Nilai baru
  - KRS approved/rejected
  - Jadwal konsultasi

**Implementasi:**
- Firebase Cloud Messaging (FCM)
- Laravel notification system
- Integration dengan mobile app

---

### 15. **Activity Log per User** 📝
**Status:** ⚠️ Audit log sudah ada (global)  
**Prioritas:** 🟢 Rendah  
**Deskripsi:**
- History aktivitas per user (bukan hanya admin)
- User bisa melihat history aktivitas sendiri
- Filter activity log

**Note:** Sudah ada Audit Log untuk admin, ini untuk user biasa

---

### 16. **Integrasi Payment Gateway Lainnya** 💳
**Status:** ⚠️ Xendit sudah ada  
**Prioritas:** 🟢 Rendah  
**Deskripsi:**
- Support multiple payment gateway (Midtrans, Doku, dll)
- Switch payment gateway per transaksi
- Payment gateway management

---

### 17. **Dark Mode** 🌙
**Status:** ❌ Belum Ada  
**Prioritas:** 🟢 Rendah  
**Deskripsi:**
- Toggle dark/light mode
- User preference untuk tema
- Auto-detect system preference

---

## 📋 Summary

### Yang Paling Penting untuk Ditambahkan (Top 3):
1. **Forgot Password / Reset Password** - Sangat penting untuk UX ⚠️
2. ~~**System Settings**~~ - ✅ **SUDAH ADA**
3. **Peringatan Akademik** - Monitoring performa mahasiswa

### Yang Menambah Value Signifikan:
4. Absensi Dosen
5. Konsultasi Akademik
6. Kurikulum & Rencana Studi Otomatis
7. Dashboard Analytics Lanjutan

### Nice to Have:
8. Perpustakaan Digital
9. Sistem Komplain/Saran
10. Push Notifications
11. Dark Mode
12. dll...

---

## 💡 Rekomendasi Urutan Implementasi

### Phase 1 (Minggu 1-2) - Essential:
1. Forgot Password / Reset Password
2. ~~System Settings~~ - ✅ **SUDAH ADA**

### Phase 2 (Minggu 3-4) - Monitoring:
3. Peringatan Akademik
4. Absensi Dosen

### Phase 3 (Minggu 5-6) - Enhancement:
5. Konsultasi Akademik
6. Kurikulum & Rencana Studi Otomatis
7. Dashboard Analytics

### Phase 4 (Minggu 7+) - Optional:
8. Transkrip Resmi dengan Digital Signature
9. Batch Import/Export
10. Fitur-fitur lainnya...

---

**Catatan:** Prioritas bisa disesuaikan dengan kebutuhan institusi Anda. Jika ada fitur yang lebih urgent, bisa diprioritaskan terlebih dahulu.

