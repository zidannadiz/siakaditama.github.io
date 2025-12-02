# Fitur System Settings / Konfigurasi Sistem

Dokumen ini menjelaskan semua fitur yang akan diimplementasikan dalam System Settings untuk SIAKAD.

---

## 📋 Daftar Fitur System Settings

### 1. **Konfigurasi Bobot Penilaian** 📊

Admin bisa mengatur bobot penilaian untuk menghitung nilai akhir mahasiswa.

#### Fitur:
- ✅ Set bobot Tugas (dalam persen)
- ✅ Set bobot UTS (dalam persen)
- ✅ Set bobot UAS (dalam persen)
- ✅ Validasi total bobot = 100%
- ✅ Preview perhitungan contoh
- ✅ Riwayat perubahan bobot (audit log)
- ✅ Konfirmasi sebelum menyimpan

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Konfigurasi Bobot Penilaian                        │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Tugas:  [30] %                                      │
│ UTS:    [30] %                                      │
│ UAS:    [40] %                                      │
│ ─────────────────                                   │
│ Total:  100% ✓                                      │
│                                                     │
│ [Contoh Perhitungan]                                │
│ Tugas: 80 × 30% = 24                                │
│ UTS:   75 × 30% = 22.5                              │
│ UAS:   85 × 40% = 34                                │
│ ─────────────────                                   │
│ Nilai Akhir: 80.5                                   │
│                                                     │
│ ⚠️ Catatan: Perubahan akan berlaku untuk           │
│   perhitungan nilai baru. Nilai yang sudah ada      │
│   tidak akan berubah.                               │
│                                                     │
│ [Batal]  [Simpan Konfigurasi]                      │
└─────────────────────────────────────────────────────┘
```

---

### 2. **Konfigurasi Huruf Mutu & Bobot** 🎓

Admin bisa mengatur range nilai untuk setiap huruf mutu beserta bobotnya.

#### Fitur:
- ✅ Tambah/Edit/Hapus huruf mutu
- ✅ Set range nilai (min & max) untuk setiap huruf mutu
- ✅ Set bobot nilai untuk setiap huruf mutu
- ✅ Urutan huruf mutu (drag & drop)
- ✅ Validasi tidak ada overlap range
- ✅ Preview konversi contoh
- ✅ Riwayat perubahan huruf mutu

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Konfigurasi Huruf Mutu & Bobot                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│ [Tambah Huruf Mutu Baru]                           │
│                                                     │
│ ┌──────────────────────────────────────────────┐   │
│ │ Huruf Mutu: A                                │   │
│ │ Range: [85] - [100]                          │   │
│ │ Bobot: [4.00]                                │   │
│ │ [Edit] [Hapus]                               │   │
│ └──────────────────────────────────────────────┘   │
│                                                     │
│ ┌──────────────────────────────────────────────┐   │
│ │ Huruf Mutu: A-                               │   │
│ │ Range: [80] - [84]                           │   │
│ │ Bobot: [3.75]                                │   │
│ │ [Edit] [Hapus]                               │   │
│ └──────────────────────────────────────────────┘   │
│                                                     │
│ ... (B+, B, B-, C+, C, C-, D, E)                   │
│                                                     │
│ [Preview Konversi]                                  │
│ Nilai 87 → Huruf Mutu: A (4.00)                    │
│ Nilai 82 → Huruf Mutu: A- (3.75)                   │
│                                                     │
│ [Simpan Konfigurasi]                                │
└─────────────────────────────────────────────────────┘
```

---

### 3. **Pengaturan Semester Aktif** 📅

Admin bisa mengatur semester aktif dengan mudah.

#### Fitur:
- ✅ Pilih semester aktif dari dropdown
- ✅ Otomatis nonaktifkan semester sebelumnya
- ✅ Validasi hanya 1 semester aktif
- ✅ Notifikasi jika mengubah semester aktif
- ✅ Preview semester yang akan dinonaktifkan
- ✅ Riwayat perubahan semester aktif

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Pengaturan Semester Aktif                          │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Semester Aktif Saat Ini:                           │
│ Ganjil 2024/2025                                    │
│                                                     │
│ Pilih Semester Aktif Baru:                         │
│ [Dropdown: ▼]                                       │
│   ├─ Ganjil 2023/2024                              │
│   ├─ Genap 2023/2024                               │
│   ├─ Ganjil 2024/2025 ← Aktif                      │
│   ├─ Genap 2024/2025                               │
│   └─ Ganjil 2025/2026                              │
│                                                     │
│ ⚠️ Peringatan:                                     │
│   - Semester aktif saat ini akan dinonaktifkan     │
│   - Mahasiswa hanya bisa ambil KRS untuk           │
│     semester yang aktif                             │
│   - Pastikan semua data sudah lengkap              │
│                                                     │
│ [Batal]  [Simpan Semester Aktif]                   │
└─────────────────────────────────────────────────────┘
```

---

### 4. **Konfigurasi Informasi Aplikasi** 🏢

Admin bisa mengatur informasi umum aplikasi.

#### Fitur:
- ✅ Nama aplikasi
- ✅ Nama institusi/kampus
- ✅ Alamat lengkap
- ✅ Nomor telepon
- ✅ Email kontak
- ✅ Website
- ✅ Upload logo aplikasi
- ✅ Upload favicon
- ✅ Preview logo
- ✅ Validasi file upload (format, ukuran)

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Konfigurasi Informasi Aplikasi                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Nama Aplikasi:                                      │
│ [SIAKAD]                                            │
│                                                     │
│ Nama Institusi:                                     │
│ [Universitas XYZ]                                   │
│                                                     │
│ Alamat:                                             │
│ [Jalan Raya No. 123]                                │
│ [Kota, Provinsi 12345]                              │
│                                                     │
│ Nomor Telepon:                                      │
│ [0812-3456-7890]                                    │
│                                                     │
│ Email:                                              │
│ [info@kampus.ac.id]                                 │
│                                                     │
│ Website:                                            │
│ [https://www.kampus.ac.id]                          │
│                                                     │
│ Logo Aplikasi:                                      │
│ [📁 Pilih File] (Format: PNG, JPG. Max: 2MB)       │
│ [Preview Logo]                                      │
│                                                     │
│ Favicon:                                            │
│ [📁 Pilih File] (Format: ICO, PNG. Max: 500KB)     │
│                                                     │
│ [Simpan Konfigurasi]                                │
└─────────────────────────────────────────────────────┘
```

---

### 5. **Konfigurasi Email System** 📧

Admin bisa mengatur konfigurasi email untuk notifikasi.

#### Fitur:
- ✅ Email From Address
- ✅ Email From Name
- ✅ Email Reply To
- ✅ Template default
- ✅ Test send email
- ✅ Preview email template
- ✅ Link ke konfigurasi SMTP (.env)

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Konfigurasi Email System                           │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Email From Address:                                 │
│ [noreply@siakad.ac.id]                              │
│                                                     │
│ Email From Name:                                    │
│ [SIAKAD - Sistem Informasi Akademik]                │
│                                                     │
│ Email Reply To:                                     │
│ [admin@siakad.ac.id]                                │
│                                                     │
│ [Test Kirim Email]                                  │
│ Masukkan email untuk test: [test@example.com]       │
│                                                     │
│ [Kirim Test Email]                                  │
│                                                     │
│ 📝 Catatan:                                         │
│   - Untuk konfigurasi SMTP (host, port, dll),      │
│     edit file .env                                  │
│   - Lihat dokumentasi: EMAIL_NOTIFICATION_SETUP.md  │
│                                                     │
│ [Simpan Konfigurasi]                                │
└─────────────────────────────────────────────────────┘
```

---

### 6. **Konfigurasi Keamanan** 🔒

Admin bisa mengatur pengaturan keamanan sistem.

#### Fitur:
- ✅ Durasi session timeout (menit)
- ✅ Minimal panjang password
- ✅ Require strong password (opsional)
- ✅ Maksimal percobaan login gagal
- ✅ Durasi lockout setelah gagal login (menit)
- ✅ Enable/Disable remember me
- ✅ Enable/Disable 2FA (opsional, untuk masa depan)

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Konfigurasi Keamanan                               │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Durasi Session Timeout:                             │
│ [120] menit                                         │
│                                                     │
│ Minimal Panjang Password:                           │
│ [8] karakter                                        │
│                                                     │
│ Wajibkan Strong Password:                           │
│ [✓] Ya (harus ada huruf besar, kecil, angka)       │
│                                                     │
│ Maksimal Percobaan Login Gagal:                     │
│ [5] kali                                            │
│                                                     │
│ Durasi Lockout:                                     │
│ [15] menit                                          │
│                                                     │
│ Enable Remember Me:                                 │
│ [✓] Ya                                              │
│                                                     │
│ [Simpan Konfigurasi]                                │
└─────────────────────────────────────────────────────┘
```

---

### 7. **Konfigurasi KRS** 📚

Admin bisa mengatur pengaturan khusus untuk KRS.

#### Fitur:
- ✅ Maksimal SKS per semester
- ✅ Minimal SKS per semester
- ✅ Periode buka KRS (tanggal mulai & selesai)
- ✅ Enable/Disable auto-approve KRS
- ✅ Require Dosen PA approval (opsional)
- ✅ Batas waktu pengambilan KRS (hari)

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Konfigurasi KRS                                    │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Maksimal SKS per Semester:                          │
│ [24] SKS                                            │
│                                                     │
│ Minimal SKS per Semester:                           │
│ [12] SKS                                            │
│                                                     │
│ Periode Buka KRS:                                   │
│ Mulai:  [2024-01-15]                                │
│ Selesai: [2024-02-15]                               │
│                                                     │
│ Auto-Approve KRS:                                   │
│ [ ] Ya (otomatis disetujui)                         │
│                                                     │
│ Require Dosen PA Approval:                          │
│ [ ] Ya (harus approval dosen PA dulu)               │
│                                                     │
│ Batas Waktu Pengambilan:                            │
│ [30] hari setelah semester dimulai                  │
│                                                     │
│ [Simpan Konfigurasi]                                │
└─────────────────────────────────────────────────────┘
```

---

### 8. **Konfigurasi Presensi** ✅

Admin bisa mengatur pengaturan untuk sistem presensi.

#### Fitur:
- ✅ Batas waktu presensi sebelum kuliah (menit)
- ✅ Batas waktu presensi setelah kuliah mulai (menit)
- ✅ Durasi QR code presensi (menit)
- ✅ Enable/Disable presensi manual
- ✅ Minimal presensi untuk lulus (%)
- ✅ Auto-absent jika tidak presensi (jam)

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Konfigurasi Presensi                               │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Batas Waktu Presensi Sebelum Kuliah:                │
│ [30] menit                                          │
│                                                     │
│ Batas Waktu Presensi Setelah Mulai:                 │
│ [15] menit                                          │
│                                                     │
│ Durasi QR Code Presensi:                            │
│ [30] menit                                          │
│                                                     │
│ Enable Presensi Manual:                             │
│ [✓] Ya (dosen bisa input manual)                    │
│                                                     │
│ Minimal Presensi untuk Lulus:                       │
│ [75] %                                              │
│                                                     │
│ Auto-Absent jika Tidak Presensi:                    │
│ [2] jam setelah jadwal selesai                      │
│                                                     │
│ [Simpan Konfigurasi]                                │
└─────────────────────────────────────────────────────┘
```

---

### 9. **Konfigurasi Notifikasi** 🔔

Admin bisa mengatur pengaturan notifikasi sistem.

#### Fitur:
- ✅ Enable/Disable email notification
- ✅ Enable/Disable in-app notification
- ✅ Notifikasi untuk KRS approved/rejected
- ✅ Notifikasi untuk nilai baru
- ✅ Notifikasi untuk pengumuman
- ✅ Notifikasi untuk presensi
- ✅ Template notifikasi (email & in-app)

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Konfigurasi Notifikasi                             │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Email Notification:                                 │
│ [✓] Aktifkan                                        │
│                                                     │
│ In-App Notification:                                │
│ [✓] Aktifkan                                        │
│                                                     │
│ Jenis Notifikasi:                                   │
│ [✓] KRS Approved                                    │
│ [✓] KRS Rejected                                    │
│ [✓] Nilai Baru                                      │
│ [✓] Pengumuman Baru                                 │
│ [✓] Peringatan Presensi                             │
│                                                     │
│ [Simpan Konfigurasi]                                │
└─────────────────────────────────────────────────────┘
```

---

### 10. **Export/Import Konfigurasi** 💾

Admin bisa export/import konfigurasi untuk backup atau migrasi.

#### Fitur:
- ✅ Export semua konfigurasi ke JSON
- ✅ Import konfigurasi dari JSON
- ✅ Preview konfigurasi sebelum import
- ✅ Validasi format JSON
- ✅ Backup otomatis sebelum import
- ✅ Riwayat import/export

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Export/Import Konfigurasi                          │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Export Konfigurasi:                                 │
│ [📥 Download Konfigurasi (JSON)]                    │
│                                                     │
│ Import Konfigurasi:                                 │
│ [📁 Pilih File JSON]                                │
│ [Preview]                                           │
│ [Import Konfigurasi]                                │
│                                                     │
│ ⚠️ Peringatan:                                     │
│   - Backup akan dibuat otomatis sebelum import     │
│   - Konfigurasi yang sudah ada akan di-overwrite   │
│                                                     │
│ [Lihat Riwayat Export/Import]                       │
└─────────────────────────────────────────────────────┘
```

---

### 11. **Riwayat Perubahan Konfigurasi** 📝

Admin bisa melihat riwayat semua perubahan konfigurasi.

#### Fitur:
- ✅ List semua perubahan konfigurasi
- ✅ Detail perubahan (old value vs new value)
- ✅ User yang mengubah
- ✅ Waktu perubahan
- ✅ Filter by kategori
- ✅ Search perubahan

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Riwayat Perubahan Konfigurasi                      │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Filter: [Semua Kategori ▼]  Search: [_____]        │
│                                                     │
│ ┌──────────────────────────────────────────────┐   │
│ │ Kategori: Bobot Penilaian                    │   │
│ │ Perubahan: Tugas: 25% → 30%                  │   │
│ │ Oleh: Admin (admin@siakad.ac.id)             │   │
│ │ Waktu: 15 Jan 2024, 10:30 WIB                │   │
│ │ [Detail]                                      │   │
│ └──────────────────────────────────────────────┘   │
│                                                     │
│ ┌──────────────────────────────────────────────┐   │
│ │ Kategori: Semester Aktif                     │   │
│ │ Perubahan: Ganjil 2023/2024 → Ganjil 2024/2025│ │
│ │ Oleh: Admin (admin@siakad.ac.id)             │   │
│ │ Waktu: 1 Jan 2024, 08:00 WIB                 │   │
│ │ [Detail]                                      │   │
│ └──────────────────────────────────────────────┘   │
│                                                     │
│ [← Previous]  [1] [2] [3]  [Next →]                │
└─────────────────────────────────────────────────────┘
```

---

### 12. **Reset ke Default** 🔄

Admin bisa reset konfigurasi ke nilai default.

#### Fitur:
- ✅ Reset semua konfigurasi ke default
- ✅ Reset per kategori konfigurasi
- ✅ Preview nilai default sebelum reset
- ✅ Konfirmasi sebelum reset
- ✅ Backup otomatis sebelum reset

#### Contoh Interface:
```
┌─────────────────────────────────────────────────────┐
│ Reset Konfigurasi ke Default                       │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Pilih kategori yang akan di-reset:                 │
│                                                     │
│ [ ] Bobot Penilaian                                 │
│ [ ] Huruf Mutu                                      │
│ [ ] Semester Aktif                                  │
│ [ ] Informasi Aplikasi                              │
│ [ ] Email System                                    │
│ [ ] Keamanan                                        │
│ [ ] KRS                                             │
│ [ ] Presensi                                        │
│ [ ] Notifikasi                                      │
│                                                     │
│ [✓] Reset Semua                                     │
│                                                     │
│ [Preview Nilai Default]                             │
│                                                     │
│ ⚠️ Peringatan:                                     │
│   - Backup akan dibuat otomatis                    │
│   - Konfigurasi yang dipilih akan direset          │
│   - Aksi ini tidak dapat dibatalkan                │
│                                                     │
│ [Batal]  [Reset Konfigurasi]                       │
└─────────────────────────────────────────────────────┘
```

---

## 📊 Summary Fitur

| No | Fitur | Status | Prioritas |
|----|-------|--------|-----------|
| 1 | Konfigurasi Bobot Penilaian | ✅ | Tinggi |
| 2 | Konfigurasi Huruf Mutu & Bobot | ✅ | Tinggi |
| 3 | Pengaturan Semester Aktif | ✅ | Tinggi |
| 4 | Konfigurasi Informasi Aplikasi | ✅ | Sedang |
| 5 | Konfigurasi Email System | ✅ | Sedang |
| 6 | Konfigurasi Keamanan | ✅ | Sedang |
| 7 | Konfigurasi KRS | ✅ | Sedang |
| 8 | Konfigurasi Presensi | ✅ | Sedang |
| 9 | Konfigurasi Notifikasi | ✅ | Rendah |
| 10 | Export/Import Konfigurasi | ✅ | Rendah |
| 11 | Riwayat Perubahan Konfigurasi | ✅ | Rendah |
| 12 | Reset ke Default | ✅ | Rendah |

---

## 🎯 Implementasi Bertahap

### Phase 1 (Prioritas Tinggi):
1. Konfigurasi Bobot Penilaian
2. Konfigurasi Huruf Mutu & Bobot
3. Pengaturan Semester Aktif

### Phase 2 (Prioritas Sedang):
4. Konfigurasi Informasi Aplikasi
5. Konfigurasi Email System
6. Konfigurasi Keamanan
7. Konfigurasi KRS
8. Konfigurasi Presensi

### Phase 3 (Prioritas Rendah):
9. Konfigurasi Notifikasi
10. Export/Import Konfigurasi
11. Riwayat Perubahan Konfigurasi
12. Reset ke Default

---

## 🔧 Teknologi yang Akan Digunakan

- **Backend**: Laravel Controller & Service
- **Database**: Tabel `system_settings` untuk menyimpan konfigurasi
- **Frontend**: Blade Templates dengan Tailwind CSS
- **Validation**: Laravel Form Request Validation
- **Audit Log**: Integrasi dengan AuditLogService yang sudah ada

---

**Total Fitur: 12 kategori konfigurasi** dengan berbagai sub-fitur di setiap kategori.

Setiap fitur akan memiliki:
- ✅ Interface yang user-friendly
- ✅ Validasi input
- ✅ Konfirmasi untuk perubahan penting
- ✅ Audit log untuk tracking perubahan
- ✅ Preview sebelum menyimpan
- ✅ Dokumentasi yang jelas

