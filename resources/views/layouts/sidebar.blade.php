@php
    $role = auth()->user()->role;
    $currentRoute = request()->route()->getName();
    
    // Hitung total unread messages menggunakan method dari User model
    $unreadMessagesCount = 0;
    if (auth()->check()) {
        try {
            $unreadMessagesCount = auth()->user()->getUnreadMessagesCount();
        } catch (\Exception $e) {
            \Log::error('Error in sidebar unread count: ' . $e->getMessage());
            $unreadMessagesCount = 0;
        }
    }
    
    // Initialize notification counts
    $assignmentNotificationCount = 0;
    $examNotificationCount = 0;
    $paymentNotificationCount = 0;
    
    if (auth()->check()) {
        try {
            if ($role === 'mahasiswa') {
                $mahasiswa = \App\Models\Mahasiswa::where('user_id', auth()->id())->first();
                if ($mahasiswa) {
                    // Get all jadwal kuliah for this mahasiswa through KRS
                    $jadwalIds = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
                        ->where('status', 'approved')
                        ->pluck('jadwal_kuliah_id');
                    
                    // Assignment notifications: tugas baru (24 jam) atau deadline mendekat (3 hari)
                    $newAssignments = \App\Models\Assignment::whereIn('jadwal_kuliah_id', $jadwalIds)
                        ->where('status', 'published')
                        ->where('created_at', '>=', now()->subDay())
                        ->whereDoesntHave('submissions', function($query) use ($mahasiswa) {
                            $query->where('mahasiswa_id', $mahasiswa->id);
                        })
                        ->count();
                    
                    $upcomingDeadlines = \App\Models\Assignment::whereIn('jadwal_kuliah_id', $jadwalIds)
                        ->where('status', 'published')
                        ->whereBetween('deadline', [now(), now()->addDays(3)])
                        ->whereDoesntHave('submissions', function($query) use ($mahasiswa) {
                            $query->where('mahasiswa_id', $mahasiswa->id);
                        })
                        ->count();
                    
                    $assignmentNotificationCount = $newAssignments + $upcomingDeadlines;
                    
                    // Exam notifications: ujian baru (24 jam) atau ujian akan dimulai (24 jam)
                    $newExams = \App\Models\Exam::whereIn('jadwal_kuliah_id', $jadwalIds)
                        ->where('status', 'published')
                        ->where('created_at', '>=', now()->subDay())
                        ->whereDoesntHave('sessions', function($query) use ($mahasiswa) {
                            $query->where('mahasiswa_id', $mahasiswa->id);
                        })
                        ->count();
                    
                    $upcomingExams = \App\Models\Exam::whereIn('jadwal_kuliah_id', $jadwalIds)
                        ->where('status', 'published')
                        ->where(function($query) {
                            $query->whereNull('mulai')
                                  ->orWhere('mulai', '<=', now()->addDay());
                        })
                        ->where('selesai', '>', now())
                        ->whereDoesntHave('sessions', function($query) use ($mahasiswa) {
                            $query->where('mahasiswa_id', $mahasiswa->id);
                        })
                        ->count();
                    
                    // Hasil ujian tersedia (baru dinilai dalam 24 jam terakhir)
                    $completedExamsWithResults = \App\Models\ExamSession::where('mahasiswa_id', $mahasiswa->id)
                        ->whereIn('status', ['submitted', 'auto_submitted'])
                        ->whereNotNull('nilai')
                        ->whereHas('exam', function($query) {
                            $query->where('tampilkan_nilai', true);
                        })
                        ->where('updated_at', '>=', now()->subDay())
                        ->count();
                    
                    $examNotificationCount = $newExams + $upcomingExams + $completedExamsWithResults;
                    
                    // Payment notifications: pembayaran pending yang akan expired atau sudah expired
                    $pendingPayments = \App\Models\Payment::where('user_id', auth()->id())
                        ->where('status', 'pending')
                        ->where(function($query) {
                            // Akan expired dalam 6 jam atau sudah expired
                            $query->where('expired_at', '<=', now()->addHours(6))
                                  ->orWhere('expired_at', '<', now());
                        })
                        ->count();
                    
                    $paymentNotificationCount = $pendingPayments;
                }
            } elseif ($role === 'dosen') {
                $dosen = \App\Models\Dosen::where('user_id', auth()->id())->first();
                if ($dosen) {
                    // Assignment notifications: submissions yang belum dinilai dan tugas masih relevan
                    // Hanya hitung tugas yang deadline-nya belum terlalu lama (maksimal 30 hari setelah deadline)
                    $ungradedSubmissions = \App\Models\AssignmentSubmission::whereHas('assignment', function($query) use ($dosen) {
                            $query->where('dosen_id', $dosen->id)
                                  ->where('deadline', '>=', now()->subDays(30)); // Tugas yang deadline-nya tidak lebih dari 30 hari yang lalu
                        })
                        ->whereNotNull('submitted_at')
                        ->whereNull('nilai')
                        ->count();
                    
                    $assignmentNotificationCount = $ungradedSubmissions;
                    
                    // Exam notifications: jawaban essay yang belum dinilai dan session sudah selesai
                    // Hanya hitung untuk session yang sudah submitted/finished, bukan yang masih ongoing
                    $ungradedEssays = \App\Models\ExamAnswer::whereHas('examSession', function($query) {
                            $query->whereIn('status', ['submitted', 'auto_submitted', 'terminated'])
                                  ->whereNotNull('finished_at'); // Hanya session yang sudah selesai
                        })
                        ->whereHas('examSession.exam', function($query) use ($dosen) {
                            $query->where('dosen_id', $dosen->id);
                        })
                        ->whereHas('examQuestion', function($query) {
                            $query->where('tipe', 'essay');
                        })
                        ->whereNotNull('jawaban_essay')
                        ->whereNull('nilai')
                        ->count();
                    
                    $examNotificationCount = $ungradedEssays;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error calculating assignment/exam/payment notifications: ' . $e->getMessage());
            $assignmentNotificationCount = 0;
            $examNotificationCount = 0;
            $paymentNotificationCount = 0;
        }
        
        // Payment notifications error handling
        if (!isset($paymentNotificationCount)) {
            $paymentNotificationCount = 0;
        }
    }
@endphp

<aside class="w-64 bg-white border-r border-gray-200">
    <nav class="p-4 space-y-1">
        @if(in_array($role, ['admin_pt', 'admin_biak', 'admin_biku', 'kaprodi', 'admin_prodi']))
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
            
            @if($role === 'admin_pt')
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</p>
            </div>
            
            <a href="{{ route('admin.admin.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.admin') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Admin & Role</span>
            </a>
            @endif
            
            @if(in_array($role, ['admin_pt', 'admin_biak', 'kaprodi', 'admin_prodi']))
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master Data</p>
            </div>
            
            {{-- M. Semester & Tahun Ajaran: Admin PT & Admin BiAk --}}
            @if(in_array($role, ['admin_pt', 'admin_biak']))
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.tahun-ajaran') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Tahun Ajaran</span>
            </a>

            <a href="{{ route('admin.semester.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.semester') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Semester</span>
            </a>
            @endif

            {{-- M. Prodi: Admin PT, Admin BiAk, Kaprodi --}}
            @if(in_array($role, ['admin_pt', 'admin_biak', 'kaprodi']))
            <a href="{{ route('admin.prodi.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.prodi') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Program Studi</span>
            </a>
            @endif

            {{-- M. Mahasiswa: Admin PT, Admin BiAk, Kaprodi, Admin Prodi --}}
            <a href="{{ route('admin.mahasiswa.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.mahasiswa') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Mahasiswa</span>
            </a>
            
            {{-- M. Dosen: Admin PT, Admin BiAk, Kaprodi, Admin Prodi --}}
            <a href="{{ route('admin.dosen.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.dosen') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.255M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span>Dosen</span>
            </a>

            {{-- M. Mata Kuliah & Kurikulum: Admin PT, Kaprodi, Admin Prodi --}}
            @if(in_array($role, ['admin_pt', 'kaprodi', 'admin_prodi']))
            <a href="{{ route('admin.kurikulum.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.kurikulum') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Kurikulum</span>
            </a>
            
            <a href="{{ route('admin.mata-kuliah.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.mata-kuliah') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Mata Kuliah</span>
            </a>
            @endif

            {{-- D. Jadwal Kuliah: Admin PT, Admin Prodi --}}
            @if(in_array($role, ['admin_pt', 'admin_prodi']))
            <a href="{{ route('admin.jadwal-kuliah.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.jadwal-kuliah') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Jadwal Kuliah</span>
            </a>
            @endif

            @if(in_array($role, ['admin_pt', 'kaprodi', 'admin_prodi']))
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Akademik</p>
            </div>
            
            {{-- D. KRS: Admin PT, Kaprodi, Admin Prodi --}}
            <a href="{{ route('admin.krs.index') }}" class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.krs') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>KRS</span>
                </div>
            </a>

            {{-- D. Nilai (Rekap Nilai): Kaprodi, Admin PT --}}
            @if(in_array($role, ['admin_pt', 'kaprodi']))
            <a href="{{ route('admin.rekap-nilai.index') }}" class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.rekap-nilai') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Rekap Nilai</span>
                </div>
            </a>
            @endif

            {{-- D. KHS (Cetak KHS): Admin PT, Admin Prodi --}}
            @if(in_array($role, ['admin_pt', 'admin_prodi']))
            <a href="{{ route('admin.generate-krs-khs.index') }}?jenis=khs" class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.generate-krs-khs') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak KHS</span>
                </div>
            </a>
            @endif
            @endif
            @endif

            @if(in_array($role, ['admin_pt', 'admin_biak']))
            <a href="{{ route('admin.pengumuman.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.pengumuman') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
                <span>Pengumuman</span>
            </a>
            @endif

            @if($role === 'admin_biku')
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Keuangan</p>
            </div>

            <a href="{{ route('admin.payment.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.payment') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Pembayaran UKT/SPP</span>
            </a>

            <a href="{{ route('admin.bank.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.bank') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span>Rekening Bank</span>
            </a>
            @endif

            @if(in_array($role, ['admin_pt', 'admin_biak', 'admin_biku']))
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Laporan</p>
            </div>

            @if($role === 'admin_biku')
            <a href="{{ route('admin.laporan.pembayaran.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.laporan.pembayaran') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Laporan Pembayaran</span>
            </a>
            @endif

            @if(in_array($role, ['admin_pt', 'admin_biak']))
            <a href="{{ route('admin.laporan.akademik.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'admin.laporan.akademik') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Laporan Akademik</span>
            </a>
            @endif
            @endif
            
        @elseif($role === 'dosen')
            <a href="{{ route('dosen.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'dosen.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('dosen.jadwal.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'dosen.jadwal') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Jadwal Mengajar</span>
            </a>
            
            {{-- D. KRS: Dosen (View Only) --}}
            <a href="{{ route('dosen.krs.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'dosen.krs') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>KRS Mahasiswa</span>
            </a>
            
            <a href="{{ route('dosen.nilai.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'dosen.nilai') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Input Nilai</span>
            </a>
            
            <a href="{{ route('dosen.presensi.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'dosen.presensi') && !str_starts_with($currentRoute, 'dosen.qr-presensi') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Presensi</span>
            </a>
            
            {{-- QR Code Presensi - DINONAKTIFKAN --}}
            {{-- <a href="{{ route('dosen.qr-presensi.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'dosen.qr-presensi') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span>QR Code Presensi</span>
            </a> --}}
            
            <a href="{{ route('dosen.presensi-kelas.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'dosen.presensi-kelas') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Presensi Kelas</span>
            </a>
            
        @elseif($role === 'mahasiswa')
            <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'mahasiswa.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('mahasiswa.jadwal.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'mahasiswa.jadwal') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Jadwal Kuliah</span>
            </a>
            
            <a href="{{ route('mahasiswa.krs.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'mahasiswa.krs') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>KRS</span>
            </a>
            
            <a href="{{ route('mahasiswa.khs.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'mahasiswa.khs') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>KHS</span>
            </a>
            
            <a href="{{ route('mahasiswa.transcript.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'mahasiswa.transcript') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Transkrip Nilai</span>
            </a>
            
            {{-- HIDDEN mahasiswa.assignment --}}
            
            {{-- HIDDEN mahasiswa.exam --}}
            
            <a href="{{ route('mahasiswa.presensi.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'mahasiswa.presensi') && !str_starts_with($currentRoute, 'mahasiswa.qr-presensi') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Presensi</span>
            </a>
            
            {{-- QR Code Presensi - DINONAKTIFKAN --}}
            {{-- <a href="{{ route('mahasiswa.qr-presensi.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'mahasiswa.qr-presensi') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span>QR Code Presensi</span>
            </a> --}}
            
            <a href="{{ route('mahasiswa.presensi-kelas.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'mahasiswa.presensi-kelas') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Presensi Kelas</span>
            </a>
            
            {{-- HIDDEN payment.index --}}
            
            {{-- HIDDEN mahasiswa.kalender-akademik --}}
            
            {{-- HIDDEN mahasiswa.statistik-keaktifan --}}
        @endif

        {{-- <!-- Komunikasi (untuk semua role) - HIDDEN -->
        <div class="pt-4 mt-4 border-t border-gray-200">
            <p class="px-4 text-xs font-semibold text-gray-500text-gray-400 uppercase tracking-wider mb-1">Komunikasi</p>
            
            <a href="{{ route('notifikasi.index') }}" class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'notifikasi') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span>Notifikasi</span>
                </div>
            </a>
            
            <a href="{{ route('chat.index') }}" class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'chat') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>Pesan</span>
                </div>
            </a>
            
            <a href="{{ route('forum.index') }}" class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'forum') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                    </svg>
                    <span>Forum</span>
                </div>
            </a>
            
            <a href="{{ route('qna.index') }}" class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'qna') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Tanya Jawab</span>
                </div>
            </a>
        </div> --}}

        <!-- Profil (untuk semua role) -->
        <div class="pt-4 mt-4 border-t border-gray-200">
            <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ str_starts_with($currentRoute, 'profile') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Profil Saya</span>
            </a>
        </div>
    </nav>
</aside>

