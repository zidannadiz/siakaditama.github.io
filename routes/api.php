<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\Mahasiswa\KRSController as MahasiswaKRSController;
use App\Http\Controllers\Api\Mahasiswa\KHSController;
use App\Http\Controllers\Api\Mahasiswa\PresensiController as MahasiswaPresensiController;
use App\Http\Controllers\Api\Dosen\NilaiController as DosenNilaiController;
use App\Http\Controllers\Api\Dosen\PresensiController as DosenPresensiController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Admin\MahasiswaController as AdminMahasiswaController;
use App\Http\Controllers\Api\Admin\DosenController as AdminDosenController;
use App\Http\Controllers\Api\Admin\ProdiController;
use App\Http\Controllers\Api\Admin\MataKuliahController;
use App\Http\Controllers\Api\Admin\JadwalKuliahController;
use App\Http\Controllers\Api\Admin\SemesterController;
use App\Http\Controllers\Api\Admin\PengumumanController;
use App\Http\Controllers\Api\Admin\KRSController as AdminKRSController;
use App\Http\Controllers\Api\QnA\QuestionController as QnAQuestionController;
use App\Http\Controllers\Api\Forum\ForumController as ApiForumController;
use App\Http\Controllers\Api\Chat\ChatController;
use App\Http\Controllers\Api\Payment\PaymentController as ApiPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes with rate limiting
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // 5 attempts per minute
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1'); // 3 attempts per minute

// Protected routes with rate limiting
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () { // 60 requests per minute
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
    });

    // Notifikasi
    Route::prefix('notifikasi')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index']);
        Route::post('/{id}/read', [NotifikasiController::class, 'markAsRead']);
        Route::post('/read-all', [NotifikasiController::class, 'markAllAsRead']);
        Route::get('/unread-count', [NotifikasiController::class, 'getUnreadCount']);
        Route::get('/recent', [NotifikasiController::class, 'getRecent']);
    });

    // Q&A routes (untuk semua role)
    Route::prefix('qna')->group(function () {
        Route::get('/', [QnAQuestionController::class, 'index']);
        Route::post('/', [QnAQuestionController::class, 'store']);
        Route::get('/{question}', [QnAQuestionController::class, 'show']);
        Route::put('/{question}', [QnAQuestionController::class, 'update']);
        Route::delete('/{question}', [QnAQuestionController::class, 'destroy']);
        Route::post('/{question}/answer', [QnAQuestionController::class, 'answer']);
        Route::post('/{question}/best-answer/{answer}', [QnAQuestionController::class, 'markBestAnswer']);
    });

    // Forum routes (untuk semua role)
    Route::prefix('forum')->group(function () {
        Route::get('/', [ApiForumController::class, 'index']);
        Route::post('/', [ApiForumController::class, 'store']);
        Route::get('/{forumTopic}', [ApiForumController::class, 'show']);
        Route::put('/{forumTopic}', [ApiForumController::class, 'update']);
        Route::delete('/{forumTopic}', [ApiForumController::class, 'destroy']);
        Route::post('/{forumTopic}/reply', [ApiForumController::class, 'reply']);
    });

    // Chat routes (untuk semua role)
    Route::prefix('chat')->group(function () {
        Route::get('/unread/count', [ChatController::class, 'unreadCount']);
        Route::get('/users', [ChatController::class, 'getUsers']);
        Route::get('/', [ChatController::class, 'index']);
        Route::post('/', [ChatController::class, 'store']);
        Route::get('/{conversation}', [ChatController::class, 'show']);
        Route::post('/{conversation}/message', [ChatController::class, 'sendMessage']);
        Route::post('/{conversation}/read', [ChatController::class, 'markAsRead']);
    });

    // Payment routes (untuk semua role)
    Route::prefix('payment')->group(function () {
        Route::get('/banks', [ApiPaymentController::class, 'getBanks']);
        Route::get('/', [ApiPaymentController::class, 'index']);
        Route::post('/', [ApiPaymentController::class, 'store']);
        Route::get('/{payment}', [ApiPaymentController::class, 'show']);
        Route::post('/{payment}/cancel', [ApiPaymentController::class, 'cancel']);
        Route::get('/{payment}/check-status', [ApiPaymentController::class, 'checkStatus']);
    });

    // Mahasiswa routes
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::prefix('krs')->group(function () {
            Route::get('/', [MahasiswaKRSController::class, 'index']);
            Route::get('/create', [MahasiswaKRSController::class, 'create']);
            Route::post('/', [MahasiswaKRSController::class, 'store']);
            Route::delete('/{krs}', [MahasiswaKRSController::class, 'destroy']);
        });

        Route::prefix('khs')->group(function () {
            Route::get('/', [KHSController::class, 'index']);
            Route::get('/{semester_id?}', [KHSController::class, 'show']);
        });

        Route::prefix('presensi')->group(function () {
            Route::get('/', [MahasiswaPresensiController::class, 'index']);
            Route::get('/{jadwal_id}', [MahasiswaPresensiController::class, 'show']);
        });
    });

    // Dosen routes
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::prefix('nilai')->group(function () {
            Route::get('/', [DosenNilaiController::class, 'index']);
            Route::get('/create/{jadwal_id}', [DosenNilaiController::class, 'create']);
            Route::post('/{jadwal_id}', [DosenNilaiController::class, 'store']);
            Route::get('/{nilai}/edit', [DosenNilaiController::class, 'edit']);
            Route::put('/{nilai}', [DosenNilaiController::class, 'update']);
        });

        Route::prefix('presensi')->group(function () {
            Route::get('/', [DosenPresensiController::class, 'index']);
            Route::get('/create/{jadwal_id}', [DosenPresensiController::class, 'create']);
            Route::post('/{jadwal_id}', [DosenPresensiController::class, 'store']);
            Route::get('/{jadwal_id}', [DosenPresensiController::class, 'show']);
        });
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('api.admin.')->group(function () {
        Route::apiResource('prodi', ProdiController::class);
        Route::apiResource('mahasiswa', AdminMahasiswaController::class);
        Route::apiResource('dosen', AdminDosenController::class);
        Route::apiResource('mata-kuliah', MataKuliahController::class);
        Route::apiResource('jadwal-kuliah', JadwalKuliahController::class);
        Route::apiResource('semester', SemesterController::class);
        Route::apiResource('pengumuman', PengumumanController::class);

        Route::prefix('krs')->group(function () {
            Route::get('/', [AdminKRSController::class, 'index']);
            Route::post('/{krs}/approve', [AdminKRSController::class, 'approve']);
            Route::post('/{krs}/reject', [AdminKRSController::class, 'reject']);
        });
    });
});

