# Panduan Template KRS/KHS

## Deskripsi Fitur
Fitur ini memungkinkan admin untuk upload template Word untuk KRS (Kartu Rencana Studi) dan KHS (Kartu Hasil Studi). Template Word tersebut akan otomatis terisi dengan data mahasiswa saat di-generate.

## Cara Menggunakan

### 1. Upload Template (Admin)
1. Login sebagai admin
2. Buka menu **Template KRS/KHS** di sidebar
3. Klik **+ Upload Template Baru**
4. Isi form:
   - **Jenis Template**: Pilih KRS atau KHS
   - **Nama Template**: Nama template (contoh: Template KRS 2025)
   - **File Template**: Upload file Word (.doc atau .docx)
   - **Deskripsi**: Deskripsi template (opsional)
   - **Status Aktif**: Centang untuk mengaktifkan template
5. Klik **Upload Template**

### 2. Placeholder untuk Template Word

Placeholder adalah **teks khusus** yang Anda tulis di template Word yang akan **otomatis diganti** dengan data mahasiswa saat generate.

**Contoh:**
- Di template Word Anda tulis: `NIM: {NIM}`
- Saat generate, akan menjadi: `NIM: 22.01.00.010`

#### Placeholder Umum (Untuk KRS dan KHS)
- `{NIM}` - Nomor Induk Mahasiswa (contoh: 22.01.00.010)
- `{NAMA}` - Nama Mahasiswa (contoh: Muhamad Zidan)
- `{PROGRAM_STUDI}` - Nama Program Studi (contoh: Sistem Informasi)
- `{TANGGAL_CETAK}` - Tanggal cetak (format: 24 November 2025)
- `{TAHUN_AKADEMIK}` - Tahun akademik (contoh: 2025/2026)

#### Placeholder Khusus KRS
- `{NO}` - Nomor urut (untuk row di tabel) - akan otomatis jadi 1, 2, 3, dst
- `{KODE_MK}` - Kode Mata Kuliah (contoh: 212)
- `{NAMA_MK}` - Nama Mata Kuliah (contoh: Metodologi Penelitian)
- `{SKS}` - Jumlah SKS (contoh: 2)
- `{SEMESTER}` - Semester mata kuliah (contoh: 5)

#### Placeholder Khusus KHS
- `{IP}` - Indeks Prestasi (IP) semester (contoh: 4.00)
- `{TOTAL_SKS}` - Total SKS semester (contoh: 18)
- `{SEMESTER}` - Nama semester (contoh: Semester Ganjil 2025/2026)
- `{NO}` - Nomor urut (untuk row di tabel)
- `{KODE_MK}` - Kode Mata Kuliah
- `{NAMA_MK}` - Nama Mata Kuliah
- `{SKS}` - Jumlah SKS
- `{NILAI}` - Nilai akhir (contoh: 90.00)
- `{HURUF}` - Huruf mutu (contoh: A)
- `{BOBOT}` - Bobot nilai (contoh: 4.00)
- `{NILAI_X_SKS}` - Nilai x SKS (contoh: 8.00)
- `{DOSEN}` - Nama dosen (contoh: M. Oktoda N.)

### 3. Contoh Template Word

#### Cara Mudah: Download Contoh Template

Saya sudah membuat contoh template yang bisa Anda gunakan:
- **Template KRS**: Buka di browser: `http://localhost/template-examples/Template_KRS_Contoh.html`
- **Template KHS**: Buka di browser: `http://localhost/template-examples/Template_KHS_Contoh.html`

**Cara menggunakan contoh template:**
1. Buka file HTML di browser (atau klik link di atas)
2. Klik kanan pada halaman → "Save As" → Simpan sebagai `.html`
3. Buka file HTML tersebut dengan Microsoft Word
4. Word akan otomatis mengkonversi HTML ke format Word
5. Edit template sesuai kebutuhan (logo, header, dll)
6. Pastikan placeholder `{NIM}`, `{NAMA}`, dll masih ada
7. Simpan sebagai `.docx`
8. Upload template tersebut di halaman admin

#### Membuat Template Manual di Word

#### Contoh Template KRS:

**Cara membuat:**
1. Buat dokumen Word baru
2. Copy paste struktur di bawah ke Word
3. Simpan sebagai .docx

**Struktur Template KRS:**
```
KARTU RENCANA STUDI (KRS)
================================

NIM            : {NIM}
Nama           : {NAMA}
Program Studi  : {PROGRAM_STUDI}
Tahun Akademik : {TAHUN_AKADEMIK}
Tanggal Cetak  : {TANGGAL_CETAK}

MATA KULIAH YANG DIPILIH:
┌────┬──────────┬──────────────────────────┬─────┬──────────┐
│ No │ Kode MK  │ Nama Mata Kuliah         │ SKS │ Semester │
├────┼──────────┼──────────────────────────┼─────┼──────────┤
│{NO}│{KODE_MK} │{NAMA_MK}                 │{SKS}│{SEMESTER}│
└────┴──────────┴──────────────────────────┴─────┴──────────┘
```

**Catatan Penting untuk KRS:**
- Buat **satu baris tabel** dengan placeholder `{NO}`, `{KODE_MK}`, `{NAMA_MK}`, `{SKS}`, `{SEMESTER}`
- Sistem akan **otomatis menggandakan baris** sesuai jumlah mata kuliah
- Pastikan placeholder berada dalam **satu sel tabel** yang sama

**Contoh di Word:**
- Buka Word → Insert → Table
- Buat tabel dengan 5 kolom (No, Kode MK, Nama MK, SKS, Semester)
- Di baris pertama (header), tulis: No | Kode MK | Nama MK | SKS | Semester
- Di baris kedua (data), tulis: `{NO} | {KODE_MK} | {NAMA_MK} | {SKS} | {SEMESTER}`
- Baris kedua ini yang akan di-clone otomatis

#### Contoh Template KHS:

**Struktur Template KHS:**
```
KARTU HASIL STUDI (KHS)
================================

NIM            : {NIM}
Nama           : {NAMA}
Program Studi  : {PROGRAM_STUDI}
Semester       : {SEMESTER}
Tahun Akademik : {TAHUN_AKADEMIK}
Tanggal Cetak  : {TANGGAL_CETAK}

IP Semester    : {IP}
Total SKS      : {TOTAL_SKS}

DAFTAR NILAI:
┌────┬──────────┬──────────────────────────┬─────┬────────┬───────┬───────┬─────────────┬──────────────────┐
│ No │ Kode MK  │ Nama Mata Kuliah         │ SKS │ Nilai  │ Huruf │ Bobot │ Nilai x SKS │ Dosen            │
├────┼──────────┼──────────────────────────┼─────┼────────┼───────┼───────┼─────────────┼──────────────────┤
│{NO}│{KODE_MK} │{NAMA_MK}                 │{SKS}│{NILAI} │{HURUF}│{BOBOT}│{NILAI_X_SKS}│{DOSEN}           │
└────┴──────────┴──────────────────────────┴─────┴────────┴───────┴───────┴─────────────┴──────────────────┘
```

**Catatan Penting untuk KHS:**
- Buat **satu baris tabel** dengan semua placeholder: `{NO}`, `{KODE_MK}`, `{NAMA_MK}`, `{SKS}`, `{NILAI}`, `{HURUF}`, `{BOBOT}`, `{NILAI_X_SKS}`, `{DOSEN}`
- Sistem akan **otomatis menggandakan baris** sesuai jumlah nilai

### 4. Langkah-Langkah Membuat Template di Microsoft Word

#### Untuk Template KRS:

1. **Buka Microsoft Word**
2. **Buat header dokumen:**
   ```
   KARTU RENCANA STUDI (KRS)
   ================================
   
   NIM            : {NIM}
   Nama           : {NAMA}
   Program Studi  : {PROGRAM_STUDI}
   Tahun Akademik : {TAHUN_AKADEMIK}
   Tanggal Cetak  : {TANGGAL_CETAK}
   ```

3. **Buat tabel:**
   - Insert → Table → Pilih 5 kolom
   - Baris 1 (Header):
     | No | Kode MK | Nama Mata Kuliah | SKS | Semester |
   - Baris 2 (Data - INI YANG PENTING):
     | `{NO}` | `{KODE_MK}` | `{NAMA_MK}` | `{SKS}` | `{SEMESTER}` |

4. **Simpan sebagai .docx**

#### Untuk Template KHS:

1. **Buka Microsoft Word**
2. **Buat header dokumen:**
   ```
   KARTU HASIL STUDI (KHS)
   ================================
   
   NIM            : {NIM}
   Nama           : {NAMA}
   Program Studi  : {PROGRAM_STUDI}
   Semester       : {SEMESTER}
   Tahun Akademik : {TAHUN_AKADEMIK}
   Tanggal Cetak  : {TANGGAL_CETAK}
   
   IP Semester    : {IP}
   Total SKS      : {TOTAL_SKS}
   ```

3. **Buat tabel:**
   - Insert → Table → Pilih 9 kolom
   - Baris 1 (Header):
     | No | Kode MK | Nama MK | SKS | Nilai | Huruf | Bobot | Nilai x SKS | Dosen |
   - Baris 2 (Data - INI YANG PENTING):
     | `{NO}` | `{KODE_MK}` | `{NAMA_MK}` | `{SKS}` | `{NILAI}` | `{HURUF}` | `{BOBOT}` | `{NILAI_X_SKS}` | `{DOSEN}` |

4. **Simpan sebagai .docx**

### 5. Contoh Hasil Setelah Generate

#### Hasil Generate KRS:
```
KARTU RENCANA STUDI (KRS)
================================

NIM            : 22.01.00.010
Nama           : Muhamad Zidan
Program Studi  : Sistem Informasi
Tahun Akademik : 2025/2026
Tanggal Cetak  : 24 November 2025

MATA KULIAH YANG DIPILIH:
┌────┬──────────┬──────────────────────────┬─────┬──────────┐
│ No │ Kode MK  │ Nama Mata Kuliah         │ SKS │ Semester │
├────┼──────────┼──────────────────────────┼─────┼──────────┤
│ 1  │ 212      │ Metodologi Penelitian    │ 2   │ 5        │
│ 2  │ 301      │ Basis Data               │ 3   │ 5        │
│ 3  │ 302      │ Pemrograman Web          │ 3   │ 5        │
└────┴──────────┴──────────────────────────┴─────┴──────────┘
```

#### Hasil Generate KHS:
```
KARTU HASIL STUDI (KHS)
================================

NIM            : 22.01.00.010
Nama           : Muhamad Zidan
Program Studi  : Sistem Informasi
Semester       : Semester Ganjil 2025/2026
Tahun Akademik : 2025/2026
Tanggal Cetak  : 24 November 2025

IP Semester    : 4.00
Total SKS      : 2

DAFTAR NILAI:
┌────┬──────────┬──────────────────────────┬─────┬────────┬───────┬───────┬─────────────┬──────────────────┐
│ No │ Kode MK  │ Nama Mata Kuliah         │ SKS │ Nilai  │ Huruf │ Bobot │ Nilai x SKS │ Dosen            │
├────┼──────────┼──────────────────────────┼─────┼────────┼───────┼───────┼─────────────┼──────────────────┤
│ 1  │ 212      │ Metodologi Penelitian    │ 2   │ 90.00  │ A     │ 4.00  │ 8.00        │ M. Oktoda N.     │
└────┴──────────┴──────────────────────────┴─────┴────────┴───────┴───────┴─────────────┴──────────────────┘
```

### 6. Generate KRS/KHS
1. Buka menu **Generate KRS/KHS** (untuk admin) atau akses langsung (untuk mahasiswa)
2. Pilih jenis (KRS/KHS)
3. Pilih mahasiswa (jika admin)
4. Pilih semester (opsional, untuk KHS)
5. Klik **Generate**
6. File Word akan terdownload otomatis

## Lokasi File
- **Template**: `storage/app/templates/krs-khs/`
- **Generated File**: `storage/app/public/generated/`

## Catatan Penting
1. **Placeholder HARUS ditulis persis** seperti contoh (dengan kurung kurawal `{}`)
2. **Case sensitive**: `{NIM}` benar, `{nim}` salah
3. Untuk tabel, buat **HANYA SATU BARIS** dengan placeholder, sistem akan clone otomatis
4. Hanya satu template aktif per jenis (KRS/KHS) pada satu waktu
5. File template maksimal 5MB
6. Format file yang didukung: .doc dan .docx
7. Pastikan placeholder berada dalam **sel tabel yang benar** (tidak terpisah)

## Tips
- Buat template sesuai format resmi kampus Anda
- Pastikan semua placeholder ditulis dengan benar
- Test template dengan generate 1-2 mahasiswa dulu sebelum mengaktifkan
- Simpan backup template asli sebelum upload
