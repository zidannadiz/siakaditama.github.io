# 📋 Fitur yang Belum Ada di Mobile App

## ✅ **Fitur yang Sudah Ada (8 fitur)**

1. ✅ Authentication (Login/Logout)
2. ✅ Dashboard (Admin, Dosen, Mahasiswa)
3. ✅ Profile (View, Edit, Change Password)
4. ✅ KRS Management (Mahasiswa)
5. ✅ KHS (Mahasiswa)
6. ✅ Input Nilai (Dosen)
7. ✅ Input Presensi (Dosen)
8. ✅ Notifikasi

---

## ❌ **Fitur yang Belum Ada (30+ fitur)**

### **🔴 Priority 1: Fitur Umum (Semua Role)**

#### 1. **Pengumuman** - HIGH PRIORITY

-   [ ] List pengumuman dengan pinned di atas
-   [ ] Detail pengumuman
-   [ ] Filter by kategori (umum, akademik, beasiswa, kegiatan)
-   [ ] Filter by target (semua, mahasiswa, dosen)
-   [ ] Search pengumuman

**Status API:** ⚠️ Hanya ada di `/api/admin/pengumuman` (perlu endpoint public)

#### 2. **Chat** - HIGH PRIORITY

-   [ ] List conversations
-   [ ] Chat detail dengan messages
-   [ ] Send message
-   [ ] Unread count badge
-   [ ] Create new conversation
-   [ ] Real-time updates (polling atau WebSocket)

**Status API:** ✅ Sudah ada di `/api/chat`

#### 3. **Payment/Pembayaran** - HIGH PRIORITY

-   [ ] List tagihan pembayaran
-   [ ] Detail pembayaran
-   [ ] Create payment request
-   [ ] Payment status tracking
-   [ ] Payment history
-   [ ] Integrasi Xendit (redirect ke web)

**Status API:** ✅ Sudah ada di `/api/payment`

---

### **🟡 Priority 2: Fitur Mahasiswa**

#### 4. **Presensi Mahasiswa (View)** - MEDIUM

-   [ ] List presensi per jadwal
-   [ ] Statistik presensi
-   [ ] Filter by semester/jadwal
-   [ ] Detail presensi per pertemuan

**Status API:** ✅ Sudah ada di `/api/mahasiswa/presensi`

#### 5. **Assignment/Tugas** - HIGH PRIORITY

-   [ ] List tugas
-   [ ] Detail tugas
-   [ ] Submit tugas (dengan file upload)
-   [ ] Update submission
-   [ ] Download file tugas
-   [ ] View grade

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 6. **Exam/Ujian** - HIGH PRIORITY

-   [ ] List ujian
-   [ ] Detail ujian
-   [ ] Start exam
-   [ ] Take exam (dengan timer)
-   [ ] Save answer
-   [ ] Submit exam
-   [ ] View result

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 7. **Presensi Kelas** - MEDIUM

-   [ ] List kelas aktif
-   [ ] Join kelas
-   [ ] History presensi kelas
-   [ ] Konfirmasi izin/sakit

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 8. **Kalender Akademik** - LOW

-   [ ] View kalender dengan events
-   [ ] Filter by kategori

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 9. **Statistik Keaktifan** - LOW

-   [ ] View statistik presensi
-   [ ] View statistik nilai
-   [ ] Grafik keaktifan

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 10. **Export KRS/KHS** - LOW

-   [ ] Export KRS ke PDF
-   [ ] Export KHS ke PDF
-   [ ] Download file

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 11. **Transcript** - LOW

-   [ ] View transcript
-   [ ] Download transcript PDF

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

---

### **🟡 Priority 3: Fitur Dosen**

#### 12. **Presensi Detail & Edit** - MEDIUM

-   [ ] View presensi per jadwal
-   [ ] Statistik presensi
-   [ ] Edit presensi per pertemuan

**Status API:** ✅ Sudah ada di `/api/dosen/presensi/{jadwal_id}`

#### 13. **Presensi Kelas (Dosen)** - MEDIUM

-   [ ] List kelas aktif
-   [ ] Buka kelas
-   [ ] Tutup kelas
-   [ ] View peserta
-   [ ] Update status presensi

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 14. **Assignment Management** - HIGH PRIORITY

-   [ ] List assignment
-   [ ] Create assignment
-   [ ] Edit assignment
-   [ ] View submissions
-   [ ] Grade submission

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 15. **Exam Management** - HIGH PRIORITY

-   [ ] List exam
-   [ ] Create exam
-   [ ] Edit exam
-   [ ] Add questions
-   [ ] View results
-   [ ] Grade exam
-   [ ] View violations

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 16. **Statistik Presensi (Dosen)** - LOW

-   [ ] View statistik presensi per kelas
-   [ ] Grafik presensi

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

#### 17. **Kalender Akademik (Dosen)** - LOW

-   [ ] View kalender
-   [ ] Events

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

---

### **🔴 Priority 4: Fitur Admin**

#### 18. **CRUD Mahasiswa** - HIGH PRIORITY

-   [ ] List mahasiswa
-   [ ] Create mahasiswa
-   [ ] Edit mahasiswa
-   [ ] Delete mahasiswa
-   [ ] Import/Export

**Status API:** ✅ Sudah ada di `/api/admin/mahasiswa`

#### 19. **CRUD Dosen** - HIGH PRIORITY

-   [ ] List dosen
-   [ ] Create dosen
-   [ ] Edit dosen
-   [ ] Delete dosen

**Status API:** ✅ Sudah ada di `/api/admin/dosen`

#### 20. **CRUD Prodi** - HIGH PRIORITY

-   [ ] List prodi
-   [ ] Create prodi
-   [ ] Edit prodi
-   [ ] Delete prodi

**Status API:** ✅ Sudah ada di `/api/admin/prodi`

#### 21. **CRUD Mata Kuliah** - HIGH PRIORITY

-   [ ] List mata kuliah
-   [ ] Create mata kuliah
-   [ ] Edit mata kuliah
-   [ ] Delete mata kuliah

**Status API:** ✅ Sudah ada di `/api/admin/mata-kuliah`

#### 22. **CRUD Jadwal Kuliah** - HIGH PRIORITY

-   [ ] List jadwal
-   [ ] Create jadwal
-   [ ] Edit jadwal
-   [ ] Delete jadwal

**Status API:** ✅ Sudah ada di `/api/admin/jadwal-kuliah`

#### 23. **CRUD Semester** - HIGH PRIORITY

-   [ ] List semester
-   [ ] Create semester
-   [ ] Edit semester
-   [ ] Set semester aktif

**Status API:** ✅ Sudah ada di `/api/admin/semester`

#### 24. **KRS Approval** - HIGH PRIORITY

-   [ ] List KRS pending
-   [ ] Approve KRS
-   [ ] Reject KRS
-   [ ] Filter by status

**Status API:** ✅ Sudah ada di `/api/admin/krs`

#### 25. **Pengumuman Management** - MEDIUM

-   [ ] List pengumuman
-   [ ] Create pengumuman
-   [ ] Edit pengumuman
-   [ ] Delete pengumuman
-   [ ] Pin/unpin

**Status API:** ✅ Sudah ada di `/api/admin/pengumuman`

#### 26. **Payment Management** - MEDIUM

-   [ ] List payments
-   [ ] Verify payment
-   [ ] Cancel payment
-   [ ] Statistics

**Status API:** ⚠️ Perlu cek apakah ada API endpoint

---

### **🟢 Priority 5: Fitur Tambahan**

#### 27. **Forum** - MEDIUM

-   [ ] List forum topics
-   [ ] Detail forum dengan replies
-   [ ] Create new topic
-   [ ] Reply to topic
-   [ ] Like/unlike

**Status API:** ✅ Sudah ada di `/api/forum`

#### 28. **Q&A** - MEDIUM

-   [ ] List questions
-   [ ] Detail question dengan answers
-   [ ] Create question
-   [ ] Answer question
-   [ ] Mark best answer
-   [ ] Upvote/downvote

**Status API:** ✅ Sudah ada di `/api/qna`

---

## 🎯 Rekomendasi Urutan Implementasi

### **Minggu 1-2: Fitur Umum**

1. ✅ Pengumuman Screen
2. ✅ Chat Screen (basic)
3. ✅ Payment Screen (basic)

### **Minggu 3-4: Fitur Mahasiswa**

4. ✅ Presensi Mahasiswa (View)
5. ✅ Assignment/Tugas
6. ✅ Exam/Ujian

### **Minggu 5-6: Fitur Dosen**

7. ✅ Assignment Management
8. ✅ Exam Management
9. ✅ Presensi Detail & Edit

### **Minggu 7-10: Fitur Admin**

10. ✅ CRUD Mahasiswa
11. ✅ CRUD Dosen
12. ✅ CRUD Prodi, Mata Kuliah, Jadwal, Semester
13. ✅ KRS Approval

### **Minggu 11-12: Fitur Tambahan**

14. ✅ Forum
15. ✅ Q&A
16. ✅ Presensi Kelas
17. ✅ Kalender Akademik

---

## 📊 Statistik

-   **Total Fitur Web:** ~38 fitur
-   **Fitur Sudah Ada di Mobile:** 8 fitur (21%)
-   **Fitur Belum Ada:** 30+ fitur (79%)
-   **Estimasi Waktu:** 10-12 minggu untuk semua fitur

---

## 🚀 Mulai dari Mana?

**Rekomendasi: Mulai dengan Pengumuman Screen**

**Alasan:**

1. ✅ API sudah tersedia (meskipun di admin, bisa digunakan untuk read)
2. ✅ Fitur penting untuk semua user
3. ✅ Relatif mudah diimplementasikan
4. ✅ Bisa langsung digunakan

**Langkah:**

1. Buat endpoint public untuk pengumuman (opsional, bisa pakai admin endpoint untuk read)
2. Buat `lib/screens/pengumuman/pengumuman_list_screen.dart`
3. Buat `lib/screens/pengumuman/pengumuman_detail_screen.dart`
4. Tambahkan route di `main.dart`
5. Tambahkan menu di dashboard

---

**Total: ~30 fitur perlu diimplementasikan**
