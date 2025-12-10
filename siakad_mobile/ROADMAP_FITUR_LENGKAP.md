# 🗺️ Roadmap Fitur Lengkap - Mobile App

## 📊 Status: Fitur yang Sudah Ada vs Belum Ada

### ✅ **Fitur yang Sudah Ada di Mobile (8 fitur)**

1. ✅ **Authentication** - Login/Logout dengan auto-login
2. ✅ **Dashboard** - Untuk semua role (Admin, Dosen, Mahasiswa)
3. ✅ **Profile** - View, Edit, Change Password
4. ✅ **KRS Management** - List, Add, Delete (Mahasiswa)
5. ✅ **KHS** - View nilai per semester (Mahasiswa)
6. ✅ **Input Nilai** - Tugas, UTS, UAS (Dosen)
7. ✅ **Input Presensi** - Hadir, Izin, Sakit, Alpa (Dosen)
8. ✅ **Notifikasi** - List, Mark as Read, Badge Count

---

## ❌ **Fitur yang Belum Ada di Mobile (20+ fitur)**

### **Priority 1: Fitur Umum (Semua Role)**

#### 1. **Pengumuman** 🔴 HIGH PRIORITY

-   [ ] List pengumuman
-   [ ] Detail pengumuman
-   [ ] Filter by kategori
-   [ ] Pinned pengumuman
-   [ ] Search pengumuman

**API:** `GET /api/pengumuman`, `GET /api/pengumuman/{id}`

#### 2. **Chat** 🔴 HIGH PRIORITY

-   [ ] List conversations
-   [ ] Chat detail dengan real-time messages
-   [ ] Send message
-   [ ] Unread count badge
-   [ ] Create new conversation

**API:** `GET /api/chat`, `POST /api/chat`, `GET /api/chat/{id}`, `POST /api/chat/{id}/message`

#### 3. **Forum** 🟡 MEDIUM PRIORITY

-   [ ] List forum topics
-   [ ] Detail forum dengan replies
-   [ ] Create new topic
-   [ ] Reply to topic
-   [ ] Like/unlike

**API:** `GET /api/forum`, `POST /api/forum`, `GET /api/forum/{id}`, `POST /api/forum/{id}/reply`

#### 4. **Q&A (Question & Answer)** 🟡 MEDIUM PRIORITY

-   [ ] List questions
-   [ ] Detail question dengan answers
-   [ ] Create question
-   [ ] Answer question
-   [ ] Mark best answer
-   [ ] Upvote/downvote

**API:** `GET /api/qna`, `POST /api/qna`, `GET /api/qna/{id}`, `POST /api/qna/{id}/answer`

#### 5. **Payment/Pembayaran** 🔴 HIGH PRIORITY

-   [ ] List tagihan pembayaran
-   [ ] Detail pembayaran
-   [ ] Create payment request
-   [ ] Payment status tracking
-   [ ] Payment history
-   [ ] Integrasi dengan Xendit

**API:** `GET /api/payment`, `POST /api/payment`, `GET /api/payment/{id}`

---

### **Priority 2: Fitur Mahasiswa**

#### 6. **Presensi Mahasiswa (View)** 🟡 MEDIUM PRIORITY

-   [ ] List presensi per jadwal
-   [ ] Statistik presensi
-   [ ] Filter by semester/jadwal
-   [ ] Detail presensi per pertemuan

**API:** `GET /api/mahasiswa/presensi`, `GET /api/mahasiswa/presensi/{jadwal_id}`

#### 7. **Presensi Kelas** 🟡 MEDIUM PRIORITY

-   [ ] List kelas aktif
-   [ ] Join kelas
-   [ ] History presensi kelas
-   [ ] Konfirmasi izin/sakit

**API:** `GET /api/mahasiswa/presensi-kelas`, `POST /api/mahasiswa/presensi-kelas/join`

#### 8. **Assignment/Tugas** 🔴 HIGH PRIORITY

-   [ ] List tugas
-   [ ] Detail tugas
-   [ ] Submit tugas
-   [ ] Update submission
-   [ ] Download file tugas

**API:** `GET /api/mahasiswa/assignment`, `GET /api/mahasiswa/assignment/{id}`, `POST /api/mahasiswa/assignment/{id}/submit`

#### 9. **Exam/Ujian** 🔴 HIGH PRIORITY

-   [ ] List ujian
-   [ ] Detail ujian
-   [ ] Start exam
-   [ ] Take exam (dengan timer)
-   [ ] Save answer
-   [ ] Submit exam
-   [ ] View result

**API:** `GET /api/mahasiswa/exam`, `GET /api/mahasiswa/exam/{id}`, `POST /api/mahasiswa/exam/{id}/start`, `GET /api/mahasiswa/exam/{id}/take/{session}`

#### 10. **Kalender Akademik** 🟢 LOW PRIORITY

-   [ ] View kalender dengan events
-   [ ] Filter by kategori
-   [ ] Detail event

**API:** `GET /api/mahasiswa/kalender-akademik`, `GET /api/mahasiswa/kalender-akademik/get-events`

#### 11. **Statistik Keaktifan** 🟢 LOW PRIORITY

-   [ ] View statistik presensi
-   [ ] View statistik nilai
-   [ ] Grafik keaktifan

**API:** `GET /api/mahasiswa/statistik-keaktifan`

#### 12. **Export KRS/KHS** 🟢 LOW PRIORITY

-   [ ] Export KRS ke PDF
-   [ ] Export KHS ke PDF
-   [ ] Download file

**API:** `GET /api/mahasiswa/export/krs/{semester_id}`, `GET /api/mahasiswa/export/khs/{semester_id}`

#### 13. **Transcript** 🟢 LOW PRIORITY

-   [ ] View transcript
-   [ ] Download transcript PDF

**API:** `GET /api/mahasiswa/transcript`, `GET /api/mahasiswa/transcript/download`

---

### **Priority 3: Fitur Dosen**

#### 14. **Presensi Detail (View)** 🟡 MEDIUM PRIORITY

-   [ ] View presensi per jadwal
-   [ ] Statistik presensi
-   [ ] Edit presensi

**API:** `GET /api/dosen/presensi/{jadwal_id}`, `PUT /api/dosen/presensi/{jadwal_id}/{pertemuan}`

#### 15. **Presensi Kelas (Dosen)** 🟡 MEDIUM PRIORITY

-   [ ] List kelas aktif
-   [ ] Buka kelas
-   [ ] Tutup kelas
-   [ ] View peserta
-   [ ] Update status presensi

**API:** `GET /api/dosen/presensi-kelas`, `POST /api/dosen/presensi-kelas/buka/{jadwal_id}`

#### 16. **Assignment Management (Dosen)** 🔴 HIGH PRIORITY

-   [ ] List assignment
-   [ ] Create assignment
-   [ ] Edit assignment
-   [ ] View submissions
-   [ ] Grade submission

**API:** `GET /api/dosen/assignment`, `POST /api/dosen/assignment`, `GET /api/dosen/assignment/{id}/submissions`

#### 17. **Exam Management (Dosen)** 🔴 HIGH PRIORITY

-   [ ] List exam
-   [ ] Create exam
-   [ ] Edit exam
-   [ ] Add questions
-   [ ] View results
-   [ ] Grade exam
-   [ ] View violations

**API:** `GET /api/dosen/exam`, `POST /api/dosen/exam`, `GET /api/dosen/exam/{id}/results`

#### 18. **Statistik Presensi (Dosen)** 🟢 LOW PRIORITY

-   [ ] View statistik presensi per kelas
-   [ ] Grafik presensi

**API:** `GET /api/dosen/statistik-presensi`

#### 19. **Kalender Akademik (Dosen)** 🟢 LOW PRIORITY

-   [ ] View kalender
-   [ ] Events

**API:** `GET /api/dosen/kalender-akademik`

---

### **Priority 4: Fitur Admin**

#### 20. **CRUD Mahasiswa** 🔴 HIGH PRIORITY

-   [ ] List mahasiswa
-   [ ] Create mahasiswa
-   [ ] Edit mahasiswa
-   [ ] Delete mahasiswa
-   [ ] Import/Export

**API:** `GET /api/admin/mahasiswa`, `POST /api/admin/mahasiswa`, `PUT /api/admin/mahasiswa/{id}`

#### 21. **CRUD Dosen** 🔴 HIGH PRIORITY

-   [ ] List dosen
-   [ ] Create dosen
-   [ ] Edit dosen
-   [ ] Delete dosen

**API:** `GET /api/admin/dosen`, `POST /api/admin/dosen`, `PUT /api/admin/dosen/{id}`

#### 22. **CRUD Prodi** 🔴 HIGH PRIORITY

-   [ ] List prodi
-   [ ] Create prodi
-   [ ] Edit prodi
-   [ ] Delete prodi

**API:** `GET /api/admin/prodi`, `POST /api/admin/prodi`, `PUT /api/admin/prodi/{id}`

#### 23. **CRUD Mata Kuliah** 🔴 HIGH PRIORITY

-   [ ] List mata kuliah
-   [ ] Create mata kuliah
-   [ ] Edit mata kuliah
-   [ ] Delete mata kuliah

**API:** `GET /api/admin/mata-kuliah`, `POST /api/admin/mata-kuliah`, `PUT /api/admin/mata-kuliah/{id}`

#### 24. **CRUD Jadwal Kuliah** 🔴 HIGH PRIORITY

-   [ ] List jadwal
-   [ ] Create jadwal
-   [ ] Edit jadwal
-   [ ] Delete jadwal

**API:** `GET /api/admin/jadwal-kuliah`, `POST /api/admin/jadwal-kuliah`, `PUT /api/admin/jadwal-kuliah/{id}`

#### 25. **CRUD Semester** 🔴 HIGH PRIORITY

-   [ ] List semester
-   [ ] Create semester
-   [ ] Edit semester
-   [ ] Set semester aktif

**API:** `GET /api/admin/semester`, `POST /api/admin/semester`, `PUT /api/admin/semester/{id}`

#### 26. **KRS Approval** 🔴 HIGH PRIORITY

-   [ ] List KRS pending
-   [ ] Approve KRS
-   [ ] Reject KRS
-   [ ] Filter by status

**API:** `GET /api/admin/krs`, `POST /api/admin/krs/{id}/approve`, `POST /api/admin/krs/{id}/reject`

#### 27. **Pengumuman Management** 🟡 MEDIUM PRIORITY

-   [ ] List pengumuman
-   [ ] Create pengumuman
-   [ ] Edit pengumuman
-   [ ] Delete pengumuman
-   [ ] Pin/unpin

**API:** `GET /api/admin/pengumuman`, `POST /api/admin/pengumuman`, `PUT /api/admin/pengumuman/{id}`

#### 28. **Payment Management** 🟡 MEDIUM PRIORITY

-   [ ] List payments
-   [ ] Verify payment
-   [ ] Cancel payment
-   [ ] Statistics

**API:** `GET /api/admin/payment`, `POST /api/admin/payment/{id}/verify`

#### 29. **System Settings** 🟢 LOW PRIORITY

-   [ ] View settings
-   [ ] Update settings
-   [ ] Semester aktif
-   [ ] Bobot penilaian
-   [ ] Huruf mutu

**API:** `GET /api/admin/system-settings`, `PUT /api/admin/system-settings`

#### 30. **Laporan** 🟢 LOW PRIORITY

-   [ ] Laporan akademik
-   [ ] Laporan pembayaran
-   [ ] Export Excel/PDF

**API:** `GET /api/admin/laporan/akademik`, `GET /api/admin/laporan/pembayaran`

---

## 🎯 Rekomendasi Urutan Implementasi

### **Phase 1: Fitur Umum (Minggu 1-2)**

1. ✅ Pengumuman Screen
2. ✅ Chat Screen (basic)
3. ✅ Payment Screen (basic)

### **Phase 2: Fitur Mahasiswa (Minggu 3-4)**

4. ✅ Presensi Mahasiswa (View)
5. ✅ Assignment/Tugas
6. ✅ Exam/Ujian

### **Phase 3: Fitur Dosen (Minggu 5-6)**

7. ✅ Assignment Management
8. ✅ Exam Management
9. ✅ Presensi Detail & Edit

### **Phase 4: Fitur Admin (Minggu 7-10)**

10. ✅ CRUD Mahasiswa
11. ✅ CRUD Dosen
12. ✅ CRUD Prodi, Mata Kuliah, Jadwal, Semester
13. ✅ KRS Approval

### **Phase 5: Fitur Tambahan (Minggu 11-12)**

14. ✅ Forum
15. ✅ Q&A
16. ✅ Presensi Kelas
17. ✅ Kalender Akademik

---

## 📝 Detail Implementasi

### **1. Pengumuman Screen** (Priority 1)

**File yang perlu dibuat:**

-   `lib/screens/pengumuman/pengumuman_list_screen.dart`
-   `lib/screens/pengumuman/pengumuman_detail_screen.dart`

**API Endpoints:**

-   `GET /api/pengumuman` - List pengumuman
-   `GET /api/pengumuman/{id}` - Detail pengumuman

**Fitur:**

-   List dengan pinned di atas
-   Filter by kategori
-   Search
-   Detail dengan full content

---

### **2. Chat Screen** (Priority 1)

**File yang perlu dibuat:**

-   `lib/screens/chat/conversation_list_screen.dart`
-   `lib/screens/chat/chat_detail_screen.dart`

**API Endpoints:**

-   `GET /api/chat` - List conversations
-   `POST /api/chat` - Create conversation
-   `GET /api/chat/{id}` - Get messages
-   `POST /api/chat/{id}/message` - Send message

**Fitur:**

-   List conversations
-   Real-time chat (mungkin perlu WebSocket atau polling)
-   Unread count
-   Send message

---

### **3. Payment Screen** (Priority 1)

**File yang perlu dibuat:**

-   `lib/screens/payment/payment_list_screen.dart`
-   `lib/screens/payment/payment_detail_screen.dart`
-   `lib/screens/payment/payment_create_screen.dart`

**API Endpoints:**

-   `GET /api/payment` - List payments
-   `POST /api/payment` - Create payment
-   `GET /api/payment/{id}` - Detail payment
-   `GET /api/payment/banks` - List banks

**Fitur:**

-   List tagihan
-   Create payment
-   Payment status tracking
-   Integrasi dengan Xendit (redirect ke web)

---

## 🚀 Mulai dari Mana?

**Rekomendasi: Mulai dengan Pengumuman Screen**

**Alasan:**

1. ✅ API sudah tersedia
2. ✅ Relatif mudah diimplementasikan
3. ✅ Fitur penting untuk semua user
4. ✅ Bisa langsung digunakan

**Langkah:**

1. Buat `lib/screens/pengumuman/pengumuman_list_screen.dart`
2. Buat `lib/screens/pengumuman/pengumuman_detail_screen.dart`
3. Tambahkan route di `main.dart`
4. Tambahkan menu di dashboard

---

**Total Fitur yang Perlu Diimplementasikan: ~30 fitur**

**Estimasi Waktu: 10-12 minggu untuk semua fitur**
