<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\DosenDashboardController;
use App\Http\Controllers\Dashboard\MahasiswaDashboardController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\JadwalKuliahController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\KRSController as AdminKRSController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Mahasiswa\KRSController;
use App\Http\Controllers\Mahasiswa\KHSController;
use App\Http\Controllers\Mahasiswa\ExportController;
use App\Http\Controllers\Dosen\NilaiController;
use App\Http\Controllers\Dosen\PresensiController;
use App\Http\Controllers\Mahasiswa\PresensiController as MahasiswaPresensiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        
        Route::resource('prodi', ProdiController::class);
        Route::resource('mahasiswa', MahasiswaController::class);
        Route::resource('dosen', DosenController::class);
        Route::resource('mata-kuliah', MataKuliahController::class);
        Route::resource('jadwal-kuliah', JadwalKuliahController::class);
        Route::resource('semester', SemesterController::class);
        Route::resource('pengumuman', PengumumanController::class);
        Route::resource('admin', AdminController::class)->except(['show']);
        
        Route::get('/krs', [AdminKRSController::class, 'index'])->name('krs.index');
        Route::post('/krs/{krs}/approve', [AdminKRSController::class, 'approve'])->name('krs.approve');
        Route::post('/krs/{krs}/reject', [AdminKRSController::class, 'reject'])->name('krs.reject');
    });

    // Dosen routes
    Route::middleware(['role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', DosenDashboardController::class)->name('dashboard');
        Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
        Route::get('/nilai/create/{jadwal_id}', [NilaiController::class, 'create'])->name('nilai.create');
        Route::post('/nilai/{jadwal_id}', [NilaiController::class, 'store'])->name('nilai.store');
        Route::get('/nilai/{nilai}/edit', [NilaiController::class, 'edit'])->name('nilai.edit');
        Route::put('/nilai/{nilai}', [NilaiController::class, 'update'])->name('nilai.update');
        
        Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
        Route::get('/presensi/create/{jadwal_id}', [PresensiController::class, 'create'])->name('presensi.create');
        Route::post('/presensi/{jadwal_id}', [PresensiController::class, 'store'])->name('presensi.store');
        Route::get('/presensi/{jadwal_id}', [PresensiController::class, 'show'])->name('presensi.show');
    });

    // Mahasiswa routes
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', MahasiswaDashboardController::class)->name('dashboard');
        Route::resource('krs', KRSController::class)->except(['show', 'update']);
        Route::get('/khs', [KHSController::class, 'index'])->name('khs.index');
        Route::get('/presensi', [MahasiswaPresensiController::class, 'index'])->name('presensi.index');
        Route::get('/export/krs/{semester_id?}', [ExportController::class, 'exportKRS'])->name('export.krs');
        Route::get('/export/khs/{semester_id?}', [ExportController::class, 'exportKHS'])->name('export.khs');
    });

    // Notifikasi routes (untuk semua role)
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('read-all');
        Route::get('/unread-count', [NotifikasiController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent', [NotifikasiController::class, 'getRecent'])->name('recent');
    });

    // Profile routes (untuk semua role)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });
});
