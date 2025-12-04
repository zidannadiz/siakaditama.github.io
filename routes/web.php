<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Forum\ForumController;
use App\Http\Controllers\QnA\QuestionController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Admin\TemplateKrsKhsController;
use App\Http\Controllers\KrsKhs\GenerateKrsKhsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes with rate limiting
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1'); // 5 attempts per minute
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

// Xendit Webhook (public, tanpa auth & CSRF)
Route::post('/payment/xendit/webhook', [\App\Http\Controllers\Payment\XenditWebhookController::class, 'handleCallback'])
    ->name('payment.xendit.webhook');

// Public route untuk scan QR code token (bisa diakses tanpa login) dengan rate limiting
Route::get('/qr-scan/{token}', [\App\Http\Controllers\Mahasiswa\QrCodePresensiController::class, 'publicScan'])
    ->middleware('throttle:10,1') // 10 attempts per minute per IP
    ->name('qr-presensi.public-scan');

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
    
    // Route untuk check session status (AJAX)
    Route::get('/session/check', function() {
        return response()->json([
            'valid' => true,
            'lifetime' => config('session.lifetime', 120), // dalam menit
        ]);
    })->name('session.check');

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
        
        // Payment management
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
            Route::get('/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/verify', [\App\Http\Controllers\Admin\PaymentController::class, 'verify'])->name('verify');
            Route::post('/{payment}/cancel', [\App\Http\Controllers\Admin\PaymentController::class, 'cancel'])->name('cancel');
            Route::get('/statistics', [\App\Http\Controllers\Admin\PaymentController::class, 'statistics'])->name('statistics');
        });
        
        // Bank management
        Route::prefix('bank')->name('bank.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BankController::class, 'index'])->name('index');
            Route::get('/{bank}/edit', [\App\Http\Controllers\Admin\BankController::class, 'edit'])->name('edit');
            Route::put('/{bank}', [\App\Http\Controllers\Admin\BankController::class, 'update'])->name('update');
            Route::post('/{bank}/toggle-status', [\App\Http\Controllers\Admin\BankController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Backup & Restore
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
            Route::post('/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
            Route::get('/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
            Route::delete('/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
        });
        
        // Audit Log
        Route::prefix('audit-log')->name('audit-log.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('index');
            Route::get('/{auditLog}', [\App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('show');
        });
        
        // System Settings
        Route::prefix('system-settings')->name('system-settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('index');
            Route::post('/semester', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateSemester'])->name('update-semester');
            Route::post('/grading', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateGrading'])->name('update-grading');
            Route::post('/letter-grades', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'storeLetterGrade'])->name('store-letter-grade');
            Route::put('/letter-grades/{letterGrade}', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateLetterGrade'])->name('update-letter-grade');
            Route::delete('/letter-grades/{letterGrade}', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'deleteLetterGrade'])->name('delete-letter-grade');
            Route::post('/app-info', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'updateAppInfo'])->name('update-app-info');
        });
        
        // Template KRS/KHS management
        Route::prefix('template-krs-khs')->name('template-krs-khs.')->group(function () {
            Route::get('/', [TemplateKrsKhsController::class, 'index'])->name('index');
            Route::get('/create', [TemplateKrsKhsController::class, 'create'])->name('create');
            Route::post('/', [TemplateKrsKhsController::class, 'store'])->name('store');
            Route::get('/{templateKrsKh}/edit', [TemplateKrsKhsController::class, 'edit'])->name('edit');
            Route::put('/{templateKrsKh}', [TemplateKrsKhsController::class, 'update'])->name('update');
            Route::delete('/{templateKrsKh}', [TemplateKrsKhsController::class, 'destroy'])->name('destroy');
            Route::post('/{templateKrsKh}/toggle-status', [TemplateKrsKhsController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{templateKrsKh}/download', [TemplateKrsKhsController::class, 'download'])->name('download');
        });
        
        // Generate KRS/KHS (for admin)
        Route::prefix('generate-krs-khs')->name('generate-krs-khs.')->group(function () {
            Route::get('/', [GenerateKrsKhsController::class, 'showForm'])->name('index');
            Route::post('/generate', [GenerateKrsKhsController::class, 'generate'])->name('generate');
        });
        
        // Laporan
        Route::prefix('laporan')->name('laporan.')->group(function () {
            // Laporan Pembayaran
            Route::get('/pembayaran', [\App\Http\Controllers\Admin\LaporanPembayaranController::class, 'index'])->name('pembayaran.index');
            Route::get('/pembayaran/export-excel', [\App\Http\Controllers\Admin\LaporanPembayaranController::class, 'exportExcel'])->name('pembayaran.export-excel');
            Route::get('/pembayaran/export-pdf', [\App\Http\Controllers\Admin\LaporanPembayaranController::class, 'exportPdf'])->name('pembayaran.export-pdf');
            
            // Laporan Akademik
            Route::get('/akademik', [\App\Http\Controllers\Admin\LaporanAkademikController::class, 'index'])->name('akademik.index');
            Route::get('/akademik/export-excel', [\App\Http\Controllers\Admin\LaporanAkademikController::class, 'exportExcel'])->name('akademik.export-excel');
            Route::get('/akademik/export-pdf', [\App\Http\Controllers\Admin\LaporanAkademikController::class, 'exportPdf'])->name('akademik.export-pdf');
            Route::get('/akademik/presensi', [\App\Http\Controllers\Admin\LaporanAkademikController::class, 'statistikPresensi'])->name('akademik.presensi');
        });
        
        // Statistik Presensi
        Route::get('/statistik-presensi', [\App\Http\Controllers\Admin\StatistikPresensiController::class, 'index'])->name('statistik-presensi.index');
        Route::get('/statistik-presensi-per-prodi', [\App\Http\Controllers\Admin\StatistikPresensiPerProdiController::class, 'index'])->name('statistik-presensi-per-prodi.index');
        
        // Kalender Akademik
        Route::get('/kalender-akademik/get-events', [\App\Http\Controllers\Admin\KalenderAkademikController::class, 'getEvents'])->name('kalender-akademik.get-events');
        Route::resource('kalender-akademik', \App\Http\Controllers\Admin\KalenderAkademikController::class);
        // Active Users
        Route::get('/active-users', [\App\Http\Controllers\Admin\ActiveUsersController::class, 'index'])->name('active-users.index');
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
        Route::get('/presensi/{jadwal_id}/edit/{pertemuan}', [PresensiController::class, 'edit'])->name('presensi.edit');
        Route::put('/presensi/{jadwal_id}/{pertemuan}', [PresensiController::class, 'update'])->name('presensi.update');
        
        // QR Code Presensi - DINONAKTIFKAN
        // Route::prefix('qr-presensi')->name('qr-presensi.')->group(function () {
        //     Route::get('/', [\App\Http\Controllers\Dosen\QrCodePresensiController::class, 'index'])->name('index');
        //     Route::post('/generate/{jadwal_id}', [\App\Http\Controllers\Dosen\QrCodePresensiController::class, 'generate'])->name('generate');
        //     Route::get('/show/{jadwal_id}/{token}', [\App\Http\Controllers\Dosen\QrCodePresensiController::class, 'show'])->name('show');
        //     Route::post('/stop/{token}', [\App\Http\Controllers\Dosen\QrCodePresensiController::class, 'stop'])->name('stop');
        //     Route::get('/status/{token}', [\App\Http\Controllers\Dosen\QrCodePresensiController::class, 'status'])->name('status');
        // });
        
        // Presensi Kelas
        Route::prefix('presensi-kelas')->name('presensi-kelas.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Dosen\PresensiKelasController::class, 'index'])->name('index');
            Route::post('/buka/{jadwal_id}', [\App\Http\Controllers\Dosen\PresensiKelasController::class, 'bukaKelas'])->name('buka');
            Route::get('/kelas/{class_session_id}', [\App\Http\Controllers\Dosen\PresensiKelasController::class, 'showKelas'])->name('show');
            Route::post('/tutup/{class_session_id}', [\App\Http\Controllers\Dosen\PresensiKelasController::class, 'tutupKelas'])->name('tutup');
            Route::post('/kick/{class_session_id}/{mahasiswa_id}', [\App\Http\Controllers\Dosen\PresensiKelasController::class, 'kickMahasiswa'])->name('kick');
            Route::post('/update-status/{class_attendance_id}', [\App\Http\Controllers\Dosen\PresensiKelasController::class, 'updateStatus'])->name('update-status');
            Route::get('/peserta/{class_session_id}', [\App\Http\Controllers\Dosen\PresensiKelasController::class, 'getPeserta'])->name('peserta');
        });
        
        // Statistik Presensi
        Route::get('/statistik-presensi', [\App\Http\Controllers\Dosen\StatistikPresensiController::class, 'index'])->name('statistik-presensi.index');
        Route::get('/statistik-presensi-per-prodi', [\App\Http\Controllers\Dosen\StatistikPresensiPerProdiController::class, 'index'])->name('statistik-presensi-per-prodi.index');
        
        // Kalender Akademik
        Route::get('/kalender-akademik', [\App\Http\Controllers\Dosen\KalenderAkademikController::class, 'index'])->name('kalender-akademik.index');
        Route::get('/kalender-akademik/get-events', [\App\Http\Controllers\Dosen\KalenderAkademikController::class, 'getEvents'])->name('kalender-akademik.get-events');
        
        // Tugas (Assignment)
        Route::resource('assignment', \App\Http\Controllers\Dosen\AssignmentController::class);
        Route::post('/assignment/{assignment}/grade-submission/{submission_id}', [\App\Http\Controllers\Dosen\AssignmentController::class, 'gradeSubmission'])->name('assignment.grade-submission');
        
        // Ujian (Exam)
        Route::resource('exam', \App\Http\Controllers\Dosen\ExamController::class);
        Route::post('/exam/{exam}/add-question', [\App\Http\Controllers\Dosen\ExamController::class, 'addQuestion'])->name('exam.add-question');
        Route::post('/exam/{exam}/generate-questions', [\App\Http\Controllers\Dosen\ExamController::class, 'generateQuestions'])->name('exam.generate-questions');
        Route::put('/exam/{exam}/question/{question}', [\App\Http\Controllers\Dosen\ExamController::class, 'updateQuestion'])->name('exam.update-question');
        Route::delete('/exam/{exam}/question/{question}', [\App\Http\Controllers\Dosen\ExamController::class, 'deleteQuestion'])->name('exam.delete-question');
        Route::get('/exam/{exam}/results', [\App\Http\Controllers\Dosen\ExamController::class, 'results'])->name('exam.results');
        Route::get('/exam/{exam}/grade/{session}', [\App\Http\Controllers\Dosen\ExamController::class, 'showGradeSession'])->name('exam.grade-session');
        Route::post('/exam/{exam}/grade/{session}', [\App\Http\Controllers\Dosen\ExamController::class, 'gradeSession'])->name('exam.grade-session.store');
        
        // Violation Rules & Detection
        Route::get('/exam/{exam}/violation-rules', [\App\Http\Controllers\Dosen\ExamController::class, 'showViolationRules'])->name('exam.violation-rules');
        Route::put('/exam/{exam}/violation-rules', [\App\Http\Controllers\Dosen\ExamController::class, 'updateViolationRules'])->name('exam.violation-rules.update');
        Route::get('/exam/{exam}/violations', [\App\Http\Controllers\Dosen\ExamController::class, 'violations'])->name('exam.violations');
        Route::get('/exam/{exam}/violations/{session}', [\App\Http\Controllers\Dosen\ExamController::class, 'showViolationDetail'])->name('exam.violation-detail');
        Route::get('/exam-violations', [\App\Http\Controllers\Dosen\ExamController::class, 'allViolations'])->name('exam.all-violations');
        
        // Ongoing and Finished Exams
        Route::get('/exam-ongoing', [\App\Http\Controllers\Dosen\ExamController::class, 'ongoing'])->name('exam.ongoing');
        Route::get('/exam-finished', [\App\Http\Controllers\Dosen\ExamController::class, 'finished'])->name('exam.finished');
        Route::get('/exam/{exam}/active-students', [\App\Http\Controllers\Dosen\ExamController::class, 'activeStudents'])->name('exam.active-students');
    });

    // Mahasiswa routes
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', MahasiswaDashboardController::class)->name('dashboard');
        Route::resource('krs', KRSController::class)->except(['show', 'update']);
        Route::get('/khs', [KHSController::class, 'index'])->name('khs.index');
        Route::get('/presensi', [MahasiswaPresensiController::class, 'index'])->name('presensi.index');
        
        // QR Code Presensi - DINONAKTIFKAN
        // Route::prefix('qr-presensi')->name('qr-presensi.')->group(function () {
        //     Route::get('/', [\App\Http\Controllers\Mahasiswa\QrCodePresensiController::class, 'index'])->name('index');
        //     Route::post('/scan', [\App\Http\Controllers\Mahasiswa\QrCodePresensiController::class, 'scan'])->name('scan');
        //     Route::get('/history', [\App\Http\Controllers\Mahasiswa\QrCodePresensiController::class, 'history'])->name('history');
        // });
        
        // Presensi Kelas
        Route::prefix('presensi-kelas')->name('presensi-kelas.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mahasiswa\PresensiKelasController::class, 'index'])->name('index');
            Route::post('/join', [\App\Http\Controllers\Mahasiswa\PresensiKelasController::class, 'joinKelas'])->name('join');
            Route::get('/history', [\App\Http\Controllers\Mahasiswa\PresensiKelasController::class, 'history'])->name('history');
            Route::post('/konfirmasi-izin/{class_attendance_id}', [\App\Http\Controllers\Mahasiswa\PresensiKelasController::class, 'konfirmasiIzin'])->name('konfirmasi-izin');
            Route::post('/konfirmasi-sakit/{class_attendance_id}', [\App\Http\Controllers\Mahasiswa\PresensiKelasController::class, 'konfirmasiSakit'])->name('konfirmasi-sakit');
        });
        
        Route::get('/export/krs/{semester_id?}', [ExportController::class, 'exportKRS'])->name('export.krs');
        Route::get('/export/khs/{semester_id?}', [ExportController::class, 'exportKHS'])->name('export.khs');
        Route::get('/transcript', [\App\Http\Controllers\Mahasiswa\TranscriptController::class, 'index'])->name('transcript.index');
        Route::get('/transcript/download', [\App\Http\Controllers\Mahasiswa\TranscriptController::class, 'download'])->name('transcript.download');
        
        // Generate KRS/KHS
        Route::prefix('generate-krs-khs')->name('generate-krs-khs.')->group(function () {
            Route::get('/', [GenerateKrsKhsController::class, 'showForm'])->name('index');
            Route::post('/generate', [GenerateKrsKhsController::class, 'generate'])->name('generate');
        });
        
        // Statistik Keaktifan
        Route::get('/statistik-keaktifan', [\App\Http\Controllers\Mahasiswa\StatistikKeaktifanController::class, 'index'])->name('statistik-keaktifan.index');
        
        // Kalender Akademik
        Route::get('/kalender-akademik', [\App\Http\Controllers\Mahasiswa\KalenderAkademikController::class, 'index'])->name('kalender-akademik.index');
        Route::get('/kalender-akademik/get-events', [\App\Http\Controllers\Mahasiswa\KalenderAkademikController::class, 'getEvents'])->name('kalender-akademik.get-events');
        
        // Tugas (Assignment)
        Route::get('/assignment', [\App\Http\Controllers\Mahasiswa\AssignmentController::class, 'index'])->name('assignment.index');
        Route::get('/assignment/{assignment}', [\App\Http\Controllers\Mahasiswa\AssignmentController::class, 'show'])->name('assignment.show');
        Route::post('/assignment/{assignment}/submit', [\App\Http\Controllers\Mahasiswa\AssignmentController::class, 'submit'])->name('assignment.submit');
        Route::put('/assignment/{assignment}/submission/{submission}', [\App\Http\Controllers\Mahasiswa\AssignmentController::class, 'updateSubmission'])->name('assignment.update-submission');
        Route::get('/assignment/{assignment}/download', [\App\Http\Controllers\Mahasiswa\AssignmentController::class, 'downloadFile'])->name('assignment.download');
        
        // Ujian (Exam)
        Route::get('/exam', [\App\Http\Controllers\Mahasiswa\ExamController::class, 'index'])->name('exam.index');
        Route::get('/exam/{exam}', [\App\Http\Controllers\Mahasiswa\ExamController::class, 'show'])->name('exam.show');
        Route::post('/exam/{exam}/start', [\App\Http\Controllers\Mahasiswa\ExamController::class, 'start'])->name('exam.start');
        Route::get('/exam/{exam}/take/{session}', [\App\Http\Controllers\Mahasiswa\ExamController::class, 'take'])->name('exam.take');
        Route::post('/exam/{exam}/save-answer', [\App\Http\Controllers\Mahasiswa\ExamController::class, 'saveAnswer'])->name('exam.save-answer');
        Route::post('/exam/{exam}/submit', [\App\Http\Controllers\Mahasiswa\ExamController::class, 'submit'])->name('exam.submit');
        Route::post('/exam/{exam}/log-violation', [\App\Http\Controllers\Mahasiswa\ExamController::class, 'logViolation'])->name('exam.log-violation');
        Route::get('/exam/{exam}/result/{session}', [\App\Http\Controllers\Mahasiswa\ExamController::class, 'result'])->name('exam.result');
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

    // Chat routes (untuk semua role)
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ConversationController::class, 'index'])->name('index');
        Route::get('/create', [ConversationController::class, 'create'])->name('create');
        Route::post('/', [ConversationController::class, 'store'])->name('store');
        Route::get('/{conversation}', [ConversationController::class, 'show'])->name('show');
        Route::post('/{conversation}/message', [ConversationController::class, 'sendMessage'])->name('message');
    });

    // Forum routes (untuk semua role)
    Route::prefix('forum')->name('forum.')->group(function () {
        Route::get('/', [ForumController::class, 'index'])->name('index');
        Route::get('/create', [ForumController::class, 'create'])->name('create');
        Route::post('/', [ForumController::class, 'store'])->name('store');
        Route::get('/{forumTopic}', [ForumController::class, 'show'])->name('show');
        Route::post('/{forumTopic}/reply', [ForumController::class, 'reply'])->name('reply');
    });

    // Q&A routes (untuk semua role)
    Route::prefix('qna')->name('qna.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::get('/create', [QuestionController::class, 'create'])->name('create');
        Route::post('/', [QuestionController::class, 'store'])->name('store');
        Route::get('/{question}', [QuestionController::class, 'show'])->name('show');
        Route::post('/{question}/answer', [QuestionController::class, 'answer'])->name('answer');
        Route::post('/{question}/best-answer/{answer}', [QuestionController::class, 'markBestAnswer'])->name('best-answer');
    });

    // Payment routes (untuk semua role)
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        Route::post('/{payment}/verify', [PaymentController::class, 'verify'])->name('verify')->middleware('role:admin');
    });
});
