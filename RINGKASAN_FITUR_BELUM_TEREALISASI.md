# 📋 Ringkasan Fitur yang Belum Terealisasi di SIAKAD

**Terakhir Diperbarui:** 2025-01-11

---

## 🔴 **PRIORITAS TINGGI** (Penting untuk Produksi)

### 1. ❌ **Forgot Password / Reset Password via Email**
**Status:** Belum Ada  
**Prioritas:** 🔴 Sangat Tinggi  
**Dampak:** Sangat penting untuk user experience dan keamanan

**Deskripsi:**
- Fitur reset password via email untuk semua role (admin, dosen, mahasiswa)
- Link reset password yang dikirim via email
- Token reset password dengan expiry time
- Form reset password yang aman

**Yang Dibutuhkan:**
- Routes untuk password reset (Laravel sudah punya built-in)
- Views untuk form forgot password dan reset password
- Email template untuk link reset
- Migration `password_reset_tokens` sudah ada

**Tingkat Kesulitan:** ⚡⚡ (Mudah - Laravel built-in)

---

### 2. ❌ **Peringatan Akademik (Academic Warning)**
**Status:** Belum Ada  
**Prioritas:** 🔴 Tinggi  
**Dampak:** Monitoring performa mahasiswa dan early warning system

**Deskripsi:**
- Sistem peringatan otomatis untuk mahasiswa dengan IPK rendah
- Kategori peringatan:
  - **Peringatan 1:** IPK < 2.00
  - **Peringatan 2:** IPK < 1.50
  - **Peringatan 3:** IPK < 1.00 (Drop Out Warning)
- Notifikasi otomatis ke mahasiswa dan admin
- History peringatan per semester

**Yang Dibutuhkan:**
- Tabel `academic_warnings` (migration)
- Model `AcademicWarning`
- Job/Command untuk auto-check dan generate peringatan
- Notifikasi email ke mahasiswa
- Views untuk admin dan mahasiswa

**Tingkat Kesulitan:** ⚡⚡⚡ (Sedang)

---

### 3. ❌ **Absensi Dosen (Lecturer Attendance)**
**Status:** Belum Ada  
**Prioritas:** 🟡 Sedang-Tinggi  
**Dampak:** Monitoring kehadiran dosen dan transparansi

**Deskripsi:**
- Tracking kehadiran dosen saat mengajar
- Absensi dosen per pertemuan/jadwal kuliah
- Status: Hadir, Tidak Hadir, Izin, Sakit
- Laporan absensi dosen per bulan/semester
- Notifikasi jika dosen tidak hadir

**Catatan:** Saat ini hanya ada presensi mahasiswa, belum ada tracking untuk dosen.

**Yang Dibutuhkan:**
- Tabel `dosen_absensi` atau tambah kolom di jadwal kuliah/class_session
- Controller untuk input absensi (admin atau otomatis)
- Dashboard dan laporan untuk admin

**Tingkat Kesulitan:** ⚡⚡⚡ (Sedang)

---

## 🟡 **PRIORITAS SEDANG** (Menambah Value)

### 4. ❌ **Konsultasi Akademik (Academic Advising)**
**Status:** Belum Ada  
**Prioritas:** 🟡 Sedang  

**Deskripsi:**
- Sistem konsultasi antara mahasiswa dengan dosen pembimbing akademik (PA)
- Mahasiswa bisa request konsultasi
- Dosen PA bisa approve/reject jadwal konsultasi
- Tracking history konsultasi
- Notifikasi untuk jadwal konsultasi

**Yang Dibutuhkan:**
- Tabel `academic_consultations`
- Relationship dosen PA dengan mahasiswa (1 dosen : banyak mahasiswa)
- CRUD untuk konsultasi
- Calendar integration

**Tingkat Kesulitan:** ⚡⚡⚡⚡ (Agak Kompleks)

---

### 5. ❌ **Kurikulum & Rencana Studi Otomatis**
**Status:** Belum Ada  
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

**Yang Dibutuhkan:**
- Tabel `kurikulums`, `rencana_studis`, `mata_kuliah_prasyarats`
- Service untuk validasi prasyarat
- Auto-suggest di form KRS
- Views untuk manajemen kurikulum

**Tingkat Kesulitan:** ⚡⚡⚡⚡ (Kompleks)

---

### 6. ⚠️ **Transkrip Akademik Resmi dengan Digital Signature**
**Status:** Sebagian Ada (transkrip ada, tapi belum resmi)  
**Prioritas:** 🟡 Sedang  

**Deskripsi:**
- Transkrip akademik dengan cap/stempel digital
- QR code untuk verifikasi keaslian transkrip
- Watermark dengan logo institusi
- Format PDF resmi sesuai standar
- Download transkrip yang sudah dicap

**Yang Dibutuhkan:**
- Update view transkrip dengan digital signature
- QR code generator untuk verifikasi
- Template PDF yang lebih formal

**Tingkat Kesulitan:** ⚡⚡⚡ (Sedang)

---

### 7. ⚠️ **Dashboard Analytics & Statistik Lanjutan**
**Status:** Basic ada, perlu diperkaya  
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

**Yang Dibutuhkan:**
- Library chart.js atau Chart.js
- Query agregasi untuk statistik
- Update view dashboard dengan chart

**Tingkat Kesulitan:** ⚡⚡⚡ (Sedang)

---

### 8. ⚠️ **Batch Operation untuk Admin (Import)**
**Status:** Export ada, Import belum  
**Prioritas:** 🟡 Sedang  

**Deskripsi:**
- Import data via Excel untuk:
  - Import mahasiswa baru (batch)
  - Import nilai (batch)
  - Import jadwal kuliah (batch)
- Template Excel untuk import
- Validasi data sebelum import

**Catatan:** Export ke Excel sudah ada untuk beberapa fitur (Laporan Akademik, dll). Yang belum adalah Import batch.

**Yang Dibutuhkan:**
- Package Laravel Excel (Maatwebsite\Excel) - sudah ada
- Controller untuk import
- Template Excel
- Validasi data

**Tingkat Kesulitan:** ⚡⚡⚡ (Sedang)

---

## 🟢 **PRIORITAS RENDAH** (Nice to Have)

### 9. ❌ **Sistem Perpustakaan Digital**
**Status:** Belum Ada  
**Prioritas:** 🟢 Rendah  

**Deskripsi:**
- Katalog buku/jurnal digital
- Peminjaman buku online
- Tracking peminjaman dan pengembalian
- History peminjaman per user
- Notifikasi pengembalian

---

### 10. ❌ **Sistem Komplain/Saran**
**Status:** Belum Ada  
**Prioritas:** 🟢 Rendah  

**Deskripsi:**
- Form komplain/saran untuk mahasiswa
- Kategori komplain (Akademik, Administrasi, Fasilitas, dll)
- Tracking status komplain (Open, In Progress, Resolved, Closed)
- Admin bisa menanggapi komplain
- Notifikasi untuk update status

---

### 11. ⚠️ **Pengumuman dengan Attachment**
**Status:** Sebagian Ada (pengumuman ada, belum support file)  
**Prioritas:** 🟢 Rendah  

**Deskripsi:**
- Upload file (PDF, DOC, dll) pada pengumuman
- Download attachment dari pengumuman
- Preview file langsung di browser

---

### 12. ⚠️ **Multi-Level Approval untuk KRS**
**Status:** Single approval (hanya admin)  
**Prioritas:** 🟢 Rendah  

**Deskripsi:**
- Approval bertingkat (misalnya: Dosen PA → Admin)
- History approval
- Komentar saat approve/reject

---

### 13. ❌ **Push Notifications untuk Mobile**
**Status:** Belum Ada  
**Prioritas:** 🟢 Rendah  

**Deskripsi:**
- Push notifications untuk aplikasi mobile
- Notifikasi real-time untuk:
  - Pengumuman baru
  - Nilai baru
  - KRS approved/rejected
  - Jadwal konsultasi

**Yang Dibutuhkan:**
- Firebase Cloud Messaging (FCM)
- Laravel notification system
- Integration dengan mobile app

---

### 14. ⚠️ **Activity Log per User**
**Status:** Audit log global sudah ada untuk admin  
**Prioritas:** 🟢 Rendah  

**Deskripsi:**
- History aktivitas per user (bukan hanya admin)
- User bisa melihat history aktivitas sendiri
- Filter activity log

---

### 15. ⚠️ **Integrasi Payment Gateway Lainnya**
**Status:** Xendit sudah ada  
**Prioritas:** 🟢 Rendah  

**Deskripsi:**
- Support multiple payment gateway (Midtrans, Doku, dll)
- Switch payment gateway per transaksi
- Payment gateway management

---

### 16. ❌ **Dark Mode**
**Status:** Belum Ada  
**Prioritas:** 🟢 Rendah  

**Deskripsi:**
- Toggle dark/light mode
- User preference untuk tema
- Auto-detect system preference

---

## ✅ **FITUR YANG SUDAH TEREALISASI**

1. ✅ **System Settings / Konfigurasi Sistem** - Lengkap dengan 4 tab (Semester Aktif, Bobot Penilaian, Huruf Mutu & Bobot, Informasi Aplikasi)
2. ✅ **KRS, KHS, Transkrip** - Semua sudah ada
3. ✅ **Input Nilai** - Lengkap dengan perhitungan otomatis
4. ✅ **Presensi Mahasiswa** - Dengan sistem presensi kelas real-time
5. ✅ **Tugas & Ujian Online** - Lengkap dengan anti-cheat
6. ✅ **Payment System** - Dengan Xendit integration
7. ✅ **Chat & Forum** - Sistem komunikasi sudah ada
8. ✅ **Q&A** - Sistem tanya jawab sudah ada
9. ✅ **Notifikasi** - Sistem notifikasi sudah ada
10. ✅ **Backup & Restore** - Sistem backup sudah ada
11. ✅ **Audit Log** - Logging aktivitas admin sudah ada
12. ✅ **Export Excel/PDF** - Untuk laporan akademik dan pembayaran

---

## 📊 **STATISTIK**

- **Total Fitur Belum Ada:** 16 fitur
- **Prioritas Tinggi:** 3 fitur
- **Prioritas Sedang:** 5 fitur
- **Prioritas Rendah:** 8 fitur
- **Fitur Sudah Terealisasi:** 12+ fitur utama

---

## 💡 **REKOMENDASI URUTAN IMPLEMENTASI**

### **Phase 1 (2-3 Minggu) - Essential:**
1. ✅ Forgot Password / Reset Password - **Paling Penting untuk UX**
2. ✅ Peringatan Akademik - **Monitoring performa mahasiswa**

### **Phase 2 (3-4 Minggu) - Monitoring:**
3. ✅ Absensi Dosen - **Monitoring kehadiran dosen**
4. ✅ Dashboard Analytics Lanjutan - **Visualisasi data**

### **Phase 3 (4-5 Minggu) - Enhancement:**
5. ✅ Konsultasi Akademik - **Fitur interaksi mahasiswa-dosen**
6. ✅ Kurikulum & Rencana Studi Otomatis - **Panduan akademik**
7. ✅ Batch Import - **Efisiensi input data**

### **Phase 4 (Sesuai Kebutuhan) - Optional:**
8. ✅ Transkrip Resmi dengan Digital Signature
9. ✅ Fitur-fitur lainnya sesuai prioritas institusi

---

## 📝 **CATATAN**

- Prioritas bisa disesuaikan dengan kebutuhan institusi
- Beberapa fitur mungkin sudah direncanakan atau sedang dalam pengembangan
- File `FITUR_YANG_BELUM_ADA.md` berisi detail lengkap untuk setiap fitur

---

**Dokumen ini dibuat untuk memberikan overview cepat tentang fitur-fitur yang masih belum terealisasi di sistem SIAKAD.**

