# 🧪 Metode Testing untuk SIAKAD
## Sistem Informasi Akademik dengan Metodologi SDLC Waterfall

---

## 📋 **PENDAHULUAN**

Dalam metodologi SDLC Waterfall, tahap pengujian (Testing) merupakan tahap yang sangat penting setelah tahap Implementasi. Testing dilakukan untuk memastikan bahwa sistem yang dikembangkan sesuai dengan kebutuhan, bebas dari error, dan siap digunakan.

Dokumen ini menjelaskan metode testing yang cocok untuk proyek SIAKAD yang menggunakan metodologi SDLC Waterfall.

---

## 🎯 **TAHAPAN TESTING DALAM SDLC WATERFALL**

Dalam SDLC Waterfall, testing dilakukan secara sistematis dan berurutan sesuai dengan tahapan berikut:

```
┌─────────────────────────────────────────┐
│  1. UNIT TESTING                        │
│     (Testing komponen individual)       │
└──────────────┬──────────────────────────┘
               ↓
┌─────────────────────────────────────────┐
│  2. INTEGRATION TESTING                 │
│     (Testing integrasi antar komponen)  │
└──────────────┬──────────────────────────┘
               ↓
┌─────────────────────────────────────────┐
│  3. SYSTEM TESTING                      │
│     (Testing sistem secara keseluruhan) │
└──────────────┬──────────────────────────┘
               ↓
┌─────────────────────────────────────────┐
│  4. USER ACCEPTANCE TESTING (UAT)       │
│     (Testing oleh end user)             │
└─────────────────────────────────────────┘
```

---

## 1️⃣ **UNIT TESTING**

### **Definisi:**
Unit Testing adalah pengujian yang dilakukan pada unit terkecil dari sistem (fungsi, method, atau class) secara terpisah untuk memastikan setiap unit berfungsi dengan benar.

### **Tujuan:**
- Memastikan setiap fungsi/method bekerja sesuai yang diharapkan
- Mendeteksi error di level kode paling awal
- Memudahkan debugging karena error terdeteksi di unit yang spesifik

### **Cakupan untuk SIAKAD:**

#### **A. Testing Model (Database Layer)**
```php
// Contoh: Testing Model Mahasiswa
- Test create mahasiswa baru
- Test update data mahasiswa
- Test delete mahasiswa
- Test relasi dengan Program Studi
- Test validasi data (NIM harus unique, email format, dll)
- Test query methods (find, where, get, dll)
```

**Tools yang Digunakan:**
- PHPUnit (Laravel built-in testing framework)
- Database testing dengan SQLite in-memory

**Contoh Test Case:**
```php
// tests/Unit/Models/MahasiswaTest.php
public function test_can_create_mahasiswa()
{
    $mahasiswa = Mahasiswa::create([
        'nim' => '2024001',
        'nama' => 'Test Mahasiswa',
        'prodi_id' => 1,
        // ... data lainnya
    ]);
    
    $this->assertDatabaseHas('mahasiswas', [
        'nim' => '2024001'
    ]);
}

public function test_nim_must_be_unique()
{
    Mahasiswa::create(['nim' => '2024001', ...]);
    
    $this->expectException(\Illuminate\Database\QueryException::class);
    Mahasiswa::create(['nim' => '2024001', ...]);
}
```

#### **B. Testing Controller (Business Logic)**
```php
// Contoh: Testing Controller
- Test login berhasil
- Test login dengan password salah
- Test akses route berdasarkan role
- Test CRUD operations
- Test validasi input
- Test response format
```

**Contoh Test Case:**
```php
// tests/Unit/Controllers/AuthControllerTest.php
public function test_login_success()
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password')
    ]);
    
    $response = $this->post('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password'
    ]);
    
    $response->assertStatus(200)
             ->assertJsonStructure(['success', 'token', 'user']);
}

public function test_login_failed_wrong_password()
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password')
    ]);
    
    $response = $this->post('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword'
    ]);
    
    $response->assertStatus(401);
}
```

#### **C. Testing Service/Helper Classes**
```php
// Contoh: Testing Service untuk perhitungan IPK
- Test perhitungan nilai akhir
- Test konversi nilai ke huruf mutu
- Test perhitungan IPK semester
- Test perhitungan IPK kumulatif
- Test edge cases (nilai 0, nilai 100, dll)
```

**Contoh Test Case:**
```php
// tests/Unit/Services/IPKCalculatorTest.php
public function test_calculate_nilai_akhir()
{
    $calculator = new IPKCalculator();
    
    $nilaiAkhir = $calculator->calculateNilaiAkhir(
        nilaiTugas: 80,
        nilaiUTS: 75,
        nilaiUAS: 85,
        bobotTugas: 0.3,
        bobotUTS: 0.3,
        bobotUAS: 0.4
    );
    
    $this->assertEquals(80.5, $nilaiAkhir);
}

public function test_convert_to_letter_grade()
{
    $calculator = new IPKCalculator();
    
    $this->assertEquals('A', $calculator->convertToLetterGrade(90));
    $this->assertEquals('B', $calculator->convertToLetterGrade(80));
    $this->assertEquals('C', $calculator->convertToLetterGrade(70));
}
```

### **Tools & Framework:**
- **PHPUnit:** Laravel's built-in testing framework
- **Laravel Testing:** `php artisan test`
- **Coverage:** `php artisan test --coverage`

### **Best Practices:**
1. Test setiap fungsi/method secara independen
2. Gunakan test data yang jelas dan terstruktur
3. Test positive cases dan negative cases
4. Test edge cases (boundary values)
5. Maintain test coverage minimal 70%

---

## 2️⃣ **INTEGRATION TESTING**

### **Definisi:**
Integration Testing adalah pengujian yang dilakukan untuk memastikan integrasi antar komponen sistem berfungsi dengan baik. Testing dilakukan pada interaksi antara Model, Controller, dan Database.

### **Tujuan:**
- Memastikan komponen-komponen sistem dapat bekerja sama dengan baik
- Mendeteksi error pada integrasi antar modul
- Memastikan data flow antar komponen berjalan dengan benar

### **Cakupan untuk SIAKAD:**

#### **A. Testing Database Integration**
```php
// Testing integrasi Model dengan Database
- Test CRUD operations dengan database real
- Test relasi antar tabel (foreign key)
- Test database transactions
- Test database migrations
- Test database constraints
```

**Contoh Test Case:**
```php
// tests/Integration/Database/MahasiswaIntegrationTest.php
public function test_mahasiswa_with_prodi_relationship()
{
    $prodi = Prodi::create(['nama' => 'Teknik Informatika', ...]);
    $mahasiswa = Mahasiswa::create([
        'nim' => '2024001',
        'nama' => 'Test',
        'prodi_id' => $prodi->id
    ]);
    
    $this->assertEquals($prodi->id, $mahasiswa->prodi->id);
    $this->assertCount(1, $prodi->mahasiswas);
}
```

#### **B. Testing API Integration**
```php
// Testing integrasi API dengan Database
- Test API endpoint dengan database
- Test authentication flow
- Test authorization berdasarkan role
- Test data flow dari request ke response
```

**Contoh Test Case:**
```php
// tests/Integration/API/KRSIntegrationTest.php
public function test_create_krs_via_api()
{
    $user = User::factory()->create(['role' => 'mahasiswa']);
    $mahasiswa = Mahasiswa::factory()->create(['user_id' => $user->id]);
    $jadwal = JadwalKuliah::factory()->create();
    
    $response = $this->actingAs($user)
                     ->postJson('/api/mahasiswa/krs', [
                         'jadwal_kuliah_id' => $jadwal->id
                     ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('krs', [
        'mahasiswa_id' => $mahasiswa->id,
        'jadwal_kuliah_id' => $jadwal->id
    ]);
}
```

#### **C. Testing Payment Gateway Integration**
```php
// Testing integrasi dengan Xendit
- Test create payment request
- Test webhook handling
- Test payment status update
- Test error handling dari Xendit
```

**Contoh Test Case:**
```php
// tests/Integration/Payment/XenditIntegrationTest.php
public function test_xendit_webhook_updates_payment_status()
{
    $payment = Payment::factory()->create(['status' => 'pending']);
    
    $webhookData = [
        'id' => $payment->xendit_id,
        'status' => 'PAID'
    ];
    
    $response = $this->postJson('/api/webhook/xendit', $webhookData);
    
    $response->assertStatus(200);
    $this->assertEquals('paid', $payment->fresh()->status);
}
```

#### **D. Testing QR Code Presensi Integration**
```php
// Testing integrasi QR Code dengan Presensi
- Test generate QR Code
- Test scan QR Code
- Test validasi token
- Test update presensi setelah scan
```

### **Tools & Framework:**
- **PHPUnit dengan Database Testing**
- **Laravel HTTP Testing:** `$this->postJson()`, `$this->getJson()`
- **Laravel Sanctum Testing:** `$this->actingAs($user)`
- **Mock/Stub untuk External Services:** Mock Xendit API

### **Best Practices:**
1. Test dengan database real (bukan mock) untuk integration testing
2. Test end-to-end flow dari request sampai response
3. Test error handling pada integrasi
4. Test dengan berbagai skenario data
5. Clean up test data setelah testing

---

## 3️⃣ **SYSTEM TESTING**

### **Definisi:**
System Testing adalah pengujian yang dilakukan pada sistem secara keseluruhan untuk memastikan sistem memenuhi requirement dan berfungsi dengan baik dalam kondisi normal maupun ekstrem.

### **Tujuan:**
- Memastikan sistem berfungsi sesuai dengan requirement
- Memastikan performa sistem memadai
- Memastikan keamanan sistem
- Memastikan sistem dapat menangani beban yang diharapkan

### **Cakupan untuk SIAKAD:**

#### **A. Functional Testing**
```php
// Testing fungsi-fungsi utama sistem
- Test semua fitur sesuai requirement
- Test workflow lengkap (end-to-end)
- Test dengan berbagai role (Admin, Dosen, Mahasiswa)
- Test dengan berbagai skenario
```

**Test Scenarios:**

**1. Workflow KRS:**
```
1. Mahasiswa login
2. Mahasiswa buka menu KRS
3. Mahasiswa pilih mata kuliah
4. Mahasiswa submit KRS
5. Admin approve KRS
6. Mahasiswa terima notifikasi
7. Mahasiswa lihat KRS yang sudah approved
```

**2. Workflow Input Nilai:**
```
1. Dosen login
2. Dosen buka menu Input Nilai
3. Dosen pilih jadwal kuliah
4. Dosen input nilai (Tugas, UTS, UAS)
5. Sistem hitung nilai akhir dan IPK otomatis
6. Mahasiswa terima notifikasi nilai baru
7. Mahasiswa lihat nilai di KHS
```

**3. Workflow Presensi QR Code:**
```
1. Dosen login
2. Dosen generate QR Code untuk pertemuan
3. Mahasiswa scan QR Code
4. Sistem validasi dan simpan presensi
5. Dosen lihat statistik presensi
```

#### **B. Performance Testing**
```php
// Testing performa sistem
- Test response time API
- Test load time halaman web
- Test dengan data volume besar
- Test concurrent users
- Test database query performance
```

**Tools:**
- **Apache Bench (ab):** Load testing
- **Laravel Telescope:** Performance monitoring
- **Laravel Debugbar:** Query analysis
- **New Relic / Datadog:** Application performance monitoring

**Test Cases:**
```bash
# Load testing dengan Apache Bench
ab -n 1000 -c 10 http://localhost:8000/api/dashboard

# Test response time
# Target: < 200ms untuk API endpoint
# Target: < 2s untuk halaman web
```

#### **C. Security Testing**
```php
// Testing keamanan sistem
- Test SQL Injection
- Test XSS (Cross-Site Scripting)
- Test CSRF Protection
- Test Authentication & Authorization
- Test Session Management
- Test Input Validation
```

**Test Cases:**

**1. SQL Injection Test:**
```php
// tests/System/Security/SQLInjectionTest.php
public function test_sql_injection_protection()
{
    $maliciousInput = "'; DROP TABLE users; --";
    
    $response = $this->postJson('/api/login', [
        'email' => $maliciousInput,
        'password' => 'password'
    ]);
    
    // Should not execute SQL injection
    $this->assertDatabaseHas('users', ['id' => 1]); // Users table still exists
}
```

**2. XSS Test:**
```php
public function test_xss_protection()
{
    $xssPayload = "<script>alert('XSS')</script>";
    
    $response = $this->postJson('/api/pengumuman', [
        'judul' => $xssPayload,
        'isi' => 'Test'
    ]);
    
    // Should escape HTML tags
    $this->assertStringNotContainsString('<script>', $response->json()['data']['judul']);
}
```

**3. CSRF Test:**
```php
public function test_csrf_protection()
{
    $response = $this->post('/admin/mahasiswa', [
        'nama' => 'Test'
    ]); // Without CSRF token
    
    $response->assertStatus(419); // CSRF token mismatch
}
```

**4. Authorization Test:**
```php
public function test_mahasiswa_cannot_access_admin_route()
{
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    
    $response = $this->actingAs($mahasiswa)
                     ->get('/admin/mahasiswa');
    
    $response->assertStatus(403); // Forbidden
}
```

#### **D. Usability Testing**
```php
// Testing kemudahan penggunaan
- Test navigasi menu
- Test form validation messages
- Test error messages yang jelas
- Test responsive design
- Test accessibility
```

#### **E. Compatibility Testing**
```php
// Testing kompatibilitas
- Test di berbagai browser (Chrome, Firefox, Safari, Edge)
- Test di berbagai device (Desktop, Tablet, Mobile)
- Test di berbagai OS (Windows, macOS, Linux)
- Test dengan berbagai screen resolution
```

### **Tools & Framework:**
- **PHPUnit untuk Functional Testing**
- **Laravel Dusk:** Browser testing (E2E)
- **Selenium:** Automated browser testing
- **Postman/Insomnia:** API testing
- **Apache Bench / JMeter:** Load testing

### **Best Practices:**
1. Test dengan data real atau data yang mirip dengan production
2. Test dengan berbagai skenario (normal, edge cases, error cases)
3. Test dengan berbagai role dan permission
4. Dokumentasikan semua test cases
5. Monitor performa dan resource usage

---

## 4️⃣ **USER ACCEPTANCE TESTING (UAT)**

### **Definisi:**
User Acceptance Testing (UAT) adalah pengujian yang dilakukan oleh end user (Admin, Dosen, Mahasiswa) untuk memastikan sistem sesuai dengan kebutuhan mereka dan siap digunakan di production.

### **Tujuan:**
- Memastikan sistem memenuhi kebutuhan user
- Memastikan user dapat menggunakan sistem dengan mudah
- Mendapat feedback dari user untuk perbaikan
- Memastikan sistem siap untuk production

### **Cakupan untuk SIAKAD:**

#### **A. UAT untuk Admin**
**Test Scenarios:**
1. **Master Data Management:**
   - Tambah/Edit/Hapus Program Studi
   - Tambah/Edit/Hapus Mahasiswa
   - Tambah/Edit/Hapus Dosen
   - Tambah/Edit/Hapus Mata Kuliah
   - Tambah/Edit/Hapus Jadwal Kuliah

2. **KRS Approval:**
   - Lihat daftar KRS pending
   - Approve KRS mahasiswa
   - Reject KRS dengan alasan
   - Lihat history KRS

3. **Payment Management:**
   - Lihat daftar pembayaran
   - Verify pembayaran
   - Lihat laporan pembayaran
   - Export laporan ke Excel/PDF

4. **System Settings:**
   - Set semester aktif
   - Konfigurasi bobot penilaian
   - Konfigurasi huruf mutu
   - Update informasi aplikasi

**Checklist UAT Admin:**
- [ ] Semua CRUD operations berfungsi dengan baik
- [ ] Approval/rejection KRS berjalan lancar
- [ ] Laporan dapat di-generate dan di-export
- [ ] System settings dapat dikonfigurasi
- [ ] Notifikasi muncul saat ada event penting
- [ ] Dashboard menampilkan statistik yang akurat

#### **B. UAT untuk Dosen**
**Test Scenarios:**
1. **Input Nilai:**
   - Input nilai Tugas, UTS, UAS
   - Sistem hitung nilai akhir otomatis
   - Sistem hitung IPK otomatis
   - Lihat daftar nilai yang sudah diinput

2. **Presensi:**
   - Generate QR Code untuk presensi
   - Input presensi manual
   - Lihat statistik presensi mahasiswa
   - Export laporan presensi

3. **Tugas & Ujian:**
   - Buat tugas baru
   - Buat ujian online
   - Lihat submission mahasiswa
   - Grade tugas dan ujian

4. **Jadwal:**
   - Lihat jadwal mengajar
   - Lihat daftar mahasiswa per kelas

**Checklist UAT Dosen:**
- [ ] Input nilai berjalan lancar dan akurat
- [ ] Perhitungan IPK otomatis benar
- [ ] QR Code presensi berfungsi dengan baik
- [ ] Tugas dan ujian dapat dibuat dan di-manage
- [ ] Jadwal mengajar mudah diakses
- [ ] Notifikasi muncul saat ada event penting

#### **C. UAT untuk Mahasiswa**
**Test Scenarios:**
1. **KRS:**
   - Ambil KRS untuk semester aktif
   - Lihat status KRS (pending/approved/rejected)
   - Lihat history KRS

2. **KHS & Transkrip:**
   - Lihat KHS per semester
   - Lihat transkrip akademik lengkap
   - Export KHS/Transkrip ke PDF

3. **Presensi:**
   - Scan QR Code presensi
   - Lihat statistik presensi sendiri
   - Lihat history presensi

4. **Tugas & Ujian:**
   - Lihat daftar tugas
   - Submit tugas
   - Take ujian online
   - Lihat hasil tugas dan ujian

5. **Payment:**
   - Buat tagihan pembayaran
   - Lihat status pembayaran
   - Lihat history pembayaran

**Checklist UAT Mahasiswa:**
- [ ] KRS dapat diambil dengan mudah
- [ ] KHS dan transkrip akurat dan mudah diakses
- [ ] Scan QR Code presensi berfungsi dengan baik
- [ ] Tugas dan ujian dapat di-submit dengan lancar
- [ ] Payment dapat dibuat dan di-track
- [ ] Notifikasi muncul saat ada event penting
- [ ] Dashboard informatif dan mudah dipahami

### **Metode UAT:**

#### **1. Alpha Testing (Internal)**
- Testing oleh tim development
- Testing dengan data test
- Fokus pada fungsionalitas dasar

#### **2. Beta Testing (External)**
- Testing oleh user sebenarnya (Admin, Dosen, Mahasiswa)
- Testing dengan data real atau data yang mirip real
- Fokus pada user experience dan kebutuhan user

#### **3. Pilot Testing**
- Testing dengan subset user dan data real
- Testing di environment yang mirip production
- Monitoring performa dan error

### **Tools & Framework:**
- **Manual Testing:** User melakukan testing secara manual
- **Test Script:** Script untuk memandu user dalam testing
- **Feedback Form:** Form untuk mengumpulkan feedback
- **Bug Tracking:** Sistem untuk tracking bug dan issue

### **Best Practices:**
1. Siapkan test data yang realistis
2. Buat test script yang jelas dan mudah diikuti
3. Sediakan environment yang mirip production
4. Dokumentasikan semua feedback dan issue
5. Follow up dengan user untuk klarifikasi
6. Prioritaskan issue berdasarkan severity

---

## 📊 **TESTING MATRIX & COVERAGE**

### **Testing Matrix untuk SIAKAD:**

| Modul | Unit Test | Integration Test | System Test | UAT |
|-------|-----------|------------------|-------------|-----|
| Authentication | ✅ | ✅ | ✅ | ✅ |
| Master Data (CRUD) | ✅ | ✅ | ✅ | ✅ |
| KRS | ✅ | ✅ | ✅ | ✅ |
| KHS & Transkrip | ✅ | ✅ | ✅ | ✅ |
| Input Nilai | ✅ | ✅ | ✅ | ✅ |
| Presensi QR Code | ✅ | ✅ | ✅ | ✅ |
| Tugas & Ujian | ✅ | ✅ | ✅ | ✅ |
| Payment (Xendit) | ✅ | ✅ | ✅ | ✅ |
| Chat & Forum | ✅ | ✅ | ✅ | ✅ |
| Pengumuman | ✅ | ✅ | ✅ | ✅ |
| Notifikasi | ✅ | ✅ | ✅ | ✅ |
| System Settings | ✅ | ✅ | ✅ | ✅ |
| Laporan | ✅ | ✅ | ✅ | ✅ |
| API | ✅ | ✅ | ✅ | ✅ |

### **Test Coverage Target:**
- **Unit Test Coverage:** Minimal 70%
- **Integration Test Coverage:** Minimal 80%
- **System Test Coverage:** 100% untuk fitur utama
- **UAT Coverage:** 100% untuk semua role

---

## 🛠️ **TOOLS & FRAMEWORK YANG DIGUNAKAN**

### **1. PHPUnit (Laravel Testing)**
```bash
# Install (sudah included di Laravel)
composer require --dev phpunit/phpunit

# Run tests
php artisan test
php artisan test --coverage
php artisan test --filter NamaTest
```

### **2. Laravel Dusk (Browser Testing)**
```bash
# Install
composer require --dev laravel/dusk
php artisan dusk:install

# Run
php artisan dusk
```

### **3. Postman / Insomnia (API Testing)**
- Manual API testing
- Collection untuk semua endpoint
- Automated testing dengan Newman (Postman CLI)

### **4. Apache Bench / JMeter (Load Testing)**
```bash
# Apache Bench
ab -n 1000 -c 10 http://localhost:8000/api/dashboard

# JMeter
# GUI tool untuk load testing yang lebih advanced
```

### **5. Laravel Telescope (Debugging & Monitoring)**
```bash
# Install
composer require laravel/telescope
php artisan telescope:install

# Access: http://localhost:8000/telescope
```

---

## 📝 **TEST PLAN & TEST CASES**

### **Test Plan Template:**

**1. Test Plan Overview:**
- Objective: Memastikan SIAKAD berfungsi dengan baik
- Scope: Semua modul dan fitur
- Approach: Unit → Integration → System → UAT
- Schedule: Sesuai timeline proyek

**2. Test Cases Template:**

```
Test Case ID: TC-001
Module: Authentication
Test Case: Login dengan email dan password valid
Precondition: User sudah terdaftar di sistem
Test Steps:
  1. Buka halaman login
  2. Input email yang valid
  3. Input password yang valid
  4. Klik tombol Login
Expected Result: User berhasil login dan di-redirect ke dashboard sesuai role
Actual Result: [Diisi saat testing]
Status: Pass/Fail
```

### **Contoh Test Cases untuk SIAKAD:**

#### **TC-001: Login Success**
- **Precondition:** User dengan email `admin@test.com` dan password `password` sudah terdaftar
- **Steps:**
  1. Buka `/login`
  2. Input email: `admin@test.com`
  3. Input password: `password`
  4. Klik "Login"
- **Expected:** Redirect ke dashboard admin, session terbuat
- **Priority:** High

#### **TC-002: Login Failed - Wrong Password**
- **Precondition:** User dengan email `admin@test.com` sudah terdaftar
- **Steps:**
  1. Buka `/login`
  2. Input email: `admin@test.com`
  3. Input password: `wrongpassword`
  4. Klik "Login"
- **Expected:** Error message "Email atau password salah", tetap di halaman login
- **Priority:** High

#### **TC-003: Create KRS**
- **Precondition:** 
  - Mahasiswa sudah login
  - Semester aktif sudah di-set
  - Jadwal kuliah tersedia
- **Steps:**
  1. Buka menu KRS
  2. Klik "Tambah KRS"
  3. Pilih mata kuliah
  4. Klik "Submit"
- **Expected:** KRS berhasil dibuat dengan status "Pending", notifikasi ke admin
- **Priority:** High

---

## 🎯 **IMPLEMENTASI TESTING DALAM PROYEK SIAKAD**

### **Struktur Folder Testing:**

```
tests/
├── Unit/
│   ├── Models/
│   │   ├── MahasiswaTest.php
│   │   ├── DosenTest.php
│   │   ├── KRSTest.php
│   │   └── NilaiTest.php
│   ├── Controllers/
│   │   ├── AuthControllerTest.php
│   │   ├── AdminControllerTest.php
│   │   └── MahasiswaControllerTest.php
│   └── Services/
│       ├── IPKCalculatorTest.php
│       └── QRCodeGeneratorTest.php
├── Integration/
│   ├── API/
│   │   ├── AuthAPITest.php
│   │   ├── KRSAPITest.php
│   │   └── PaymentAPITest.php
│   ├── Database/
│   │   ├── MahasiswaIntegrationTest.php
│   │   └── KRSIntegrationTest.php
│   └── Payment/
│       └── XenditIntegrationTest.php
├── Feature/
│   ├── AuthenticationTest.php
│   ├── KRSWorkflowTest.php
│   ├── InputNilaiWorkflowTest.php
│   └── PresensiQRTest.php
└── Browser/
    └── DuskTest.php
```

### **Contoh Implementasi:**

#### **1. Unit Test - Model**
```php
// tests/Unit/Models/MahasiswaTest.php
<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MahasiswaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_mahasiswa()
    {
        $prodi = Prodi::factory()->create();
        
        $mahasiswa = Mahasiswa::create([
            'nim' => '2024001',
            'nama' => 'Test Mahasiswa',
            'prodi_id' => $prodi->id,
            'email' => 'test@example.com',
            'tanggal_lahir' => '2000-01-01',
        ]);

        $this->assertDatabaseHas('mahasiswas', [
            'nim' => '2024001',
            'nama' => 'Test Mahasiswa'
        ]);
    }

    public function test_nim_must_be_unique()
    {
        $prodi = Prodi::factory()->create();
        
        Mahasiswa::create([
            'nim' => '2024001',
            'nama' => 'Mahasiswa 1',
            'prodi_id' => $prodi->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Mahasiswa::create([
            'nim' => '2024001',
            'nama' => 'Mahasiswa 2',
            'prodi_id' => $prodi->id,
        ]);
    }

    public function test_mahasiswa_belongs_to_prodi()
    {
        $prodi = Prodi::factory()->create(['nama' => 'Teknik Informatika']);
        $mahasiswa = Mahasiswa::factory()->create(['prodi_id' => $prodi->id]);

        $this->assertEquals('Teknik Informatika', $mahasiswa->prodi->nama);
    }
}
```

#### **2. Integration Test - API**
```php
// tests/Integration/API/KRSAPITest.php
<?php

namespace Tests\Integration\API;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\JadwalKuliah;
use App\Models\KRS;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class KRSAPITest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_create_krs()
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $mahasiswa = Mahasiswa::factory()->create(['user_id' => $user->id]);
        $jadwal = JadwalKuliah::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/mahasiswa/krs', [
                             'jadwal_kuliah_id' => $jadwal->id
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'status', 'jadwal_kuliah_id']
                 ]);

        $this->assertDatabaseHas('krs', [
            'mahasiswa_id' => $mahasiswa->id,
            'jadwal_kuliah_id' => $jadwal->id,
            'status' => 'pending'
        ]);
    }

    public function test_admin_can_approve_krs()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mahasiswa = Mahasiswa::factory()->create();
        $krs = KRS::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson("/api/admin/krs/{$krs->id}/approve");

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('krs', [
            'id' => $krs->id,
            'status' => 'disetujui'
        ]);
    }
}
```

#### **3. Feature Test - Workflow**
```php
// tests/Feature/KRSWorkflowTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\JadwalKuliah;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KRSWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_krs_workflow()
    {
        // 1. Setup
        $mahasiswaUser = User::factory()->create(['role' => 'mahasiswa']);
        $mahasiswa = Mahasiswa::factory()->create(['user_id' => $mahasiswaUser->id]);
        $adminUser = User::factory()->create(['role' => 'admin']);
        $jadwal = JadwalKuliah::factory()->create();

        // 2. Mahasiswa create KRS
        $response = $this->actingAs($mahasiswaUser)
                         ->post('/mahasiswa/krs', [
                             'jadwal_kuliah_id' => $jadwal->id
                         ]);
        
        $response->assertRedirect();
        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)->first();
        $this->assertEquals('pending', $krs->status);

        // 3. Admin approve KRS
        $response = $this->actingAs($adminUser)
                         ->post("/admin/krs/{$krs->id}/approve");
        
        $response->assertRedirect();
        $this->assertEquals('disetujui', $krs->fresh()->status);

        // 4. Mahasiswa lihat KRS yang sudah approved
        $response = $this->actingAs($mahasiswaUser)
                         ->get('/mahasiswa/krs');
        
        $response->assertSee('disetujui');
    }
}
```

---

## 📋 **TESTING CHECKLIST**

### **Pre-Testing:**
- [ ] Test environment sudah disiapkan
- [ ] Test data sudah disiapkan
- [ ] Test users sudah dibuat (Admin, Dosen, Mahasiswa)
- [ ] Test tools sudah di-install dan dikonfigurasi
- [ ] Test plan sudah dibuat dan disetujui

### **During Testing:**
- [ ] Unit tests sudah dijalankan (coverage minimal 70%)
- [ ] Integration tests sudah dijalankan
- [ ] System tests sudah dijalankan
- [ ] UAT sudah dilakukan oleh semua role
- [ ] Semua bug sudah di-document dan di-track
- [ ] Test results sudah di-document

### **Post-Testing:**
- [ ] Test report sudah dibuat
- [ ] Bug report sudah dibuat
- [ ] Test coverage report sudah dibuat
- [ ] Feedback dari UAT sudah dikumpulkan
- [ ] Re-testing untuk bug yang sudah diperbaiki
- [ ] Sign-off dari semua stakeholder

---

## 🎓 **KESIMPULAN**

Untuk proyek SIAKAD yang menggunakan metodologi SDLC Waterfall, metode testing yang cocok adalah:

1. **Unit Testing:** Testing komponen individual (Model, Controller, Service)
2. **Integration Testing:** Testing integrasi antar komponen (Database, API, External Services)
3. **System Testing:** Testing sistem secara keseluruhan (Functional, Performance, Security, Usability)
4. **User Acceptance Testing (UAT):** Testing oleh end user untuk memastikan sistem sesuai kebutuhan

**Urutan Testing:**
```
Unit Testing → Integration Testing → System Testing → UAT
```

**Tools yang Direkomendasikan:**
- PHPUnit untuk Unit & Integration Testing
- Laravel Dusk untuk Browser Testing
- Postman/Insomnia untuk API Testing
- Apache Bench/JMeter untuk Load Testing
- Laravel Telescope untuk Monitoring

**Target Coverage:**
- Unit Test: Minimal 70%
- Integration Test: Minimal 80%
- System Test: 100% untuk fitur utama
- UAT: 100% untuk semua role

Dengan mengikuti metode testing ini, sistem SIAKAD akan teruji dengan baik dan siap untuk production.

---

**Selamat testing! 🚀**


