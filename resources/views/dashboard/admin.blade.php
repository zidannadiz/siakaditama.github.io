@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
        <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Mahasiswa Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_mahasiswa'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Dosen Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_dosen'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.255M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Mata Kuliah</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_mata_kuliah'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jadwal Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_jadwal'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">KRS Pending</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['krs_pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">KRS Disetujui</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['krs_approved'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata IPK</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($rata_rata_ipk->avg_ipk ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Grafik Mahasiswa per Prodi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Mahasiswa per Program Studi</h2>
            @if($mahasiswa_per_prodi && $mahasiswa_per_prodi->count() > 0)
                <canvas id="chartMahasiswaProdi" height="300"></canvas>
            @else
                <div class="flex items-center justify-center h-[300px] text-gray-500">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="font-medium text-gray-700">Belum ada data mahasiswa</p>
                        <p class="text-sm mt-1 text-gray-500">Grafik akan muncul setelah ada mahasiswa terdaftar</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Grafik KRS per Semester -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">KRS per Semester</h2>
            @if($krs_per_semester && $krs_per_semester->count() > 0)
                <canvas id="chartKRSSemester" height="300"></canvas>
            @else
                <div class="flex items-center justify-center h-[300px] text-gray-500">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="font-medium text-gray-700">Belum ada data KRS</p>
                        <p class="text-sm mt-1 text-gray-500">Grafik akan muncul setelah mahasiswa mengambil KRS</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Grafik Distribusi Nilai -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Distribusi Nilai (Huruf Mutu)</h2>
            @if($distribusi_nilai && $distribusi_nilai->count() > 0)
                <canvas id="chartDistribusiNilai" height="300"></canvas>
            @else
                <div class="flex items-center justify-center h-[300px] text-gray-500">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="font-medium text-gray-700">Belum ada data nilai</p>
                        <p class="text-sm mt-1 text-gray-500">Grafik akan muncul setelah dosen menginput nilai</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Grafik Status Presensi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Status Presensi</h2>
            @if($status_presensi && $status_presensi->count() > 0)
                <canvas id="chartStatusPresensi" height="300"></canvas>
            @else
                <div class="flex items-center justify-center h-[300px] text-gray-500">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <p class="font-medium text-gray-700">Belum ada data presensi</p>
                        <p class="text-sm mt-1 text-gray-500">Grafik akan muncul setelah dosen menginput presensi</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Grafik KRS per Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">KRS per Status</h2>
            @if($krs_per_status && $krs_per_status->count() > 0)
                <canvas id="chartKRSStatus" height="100"></canvas>
            @else
                <div class="flex items-center justify-center h-[200px] text-gray-500">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="font-medium text-gray-700">Belum ada data KRS</p>
                        <p class="text-sm mt-1 text-gray-500">Grafik akan muncul setelah mahasiswa mengambil KRS</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Pengumuman Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Pengumuman Terbaru</h2>
        </div>
        <div class="p-6">
            @if($pengumuman_terbaru->count() > 0)
                <div class="space-y-4">
                    @foreach($pengumuman_terbaru as $pengumuman)
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $pengumuman->judul }}</p>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($pengumuman->isi, 100) }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ $pengumuman->published_at ? $pengumuman->published_at->format('d M Y') : 'Belum dipublikasikan' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada pengumuman</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart Colors
        const colors = {
            primary: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444', '#06B6D4', '#F97316', '#EC4899'],
            background: ['rgba(59, 130, 246, 0.1)', 'rgba(139, 92, 246, 0.1)', 'rgba(16, 185, 129, 0.1)', 'rgba(245, 158, 11, 0.1)', 'rgba(239, 68, 68, 0.1)'],
        };

        // Grafik Mahasiswa per Prodi
        const ctxMahasiswaProdi = document.getElementById('chartMahasiswaProdi');
        if (ctxMahasiswaProdi && @json($mahasiswa_per_prodi && $mahasiswa_per_prodi->count() > 0)) {
            new Chart(ctxMahasiswaProdi.getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($mahasiswa_per_prodi->pluck('label')),
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: @json($mahasiswa_per_prodi->pluck('value')),
                backgroundColor: colors.primary[0],
                borderColor: colors.primary[0],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
            });
        }

        // Grafik KRS per Semester
        const ctxKRSSemester = document.getElementById('chartKRSSemester');
        if (ctxKRSSemester && @json($krs_per_semester && $krs_per_semester->count() > 0)) {
            new Chart(ctxKRSSemester.getContext('2d'), {
                type: 'line',
        data: {
            labels: @json($krs_per_semester->pluck('label')),
            datasets: [{
                label: 'Jumlah KRS',
                data: @json($krs_per_semester->pluck('value')),
                borderColor: colors.primary[1],
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
            });
        }

        // Grafik Distribusi Nilai
        const ctxDistribusiNilai = document.getElementById('chartDistribusiNilai');
        if (ctxDistribusiNilai && @json($distribusi_nilai && $distribusi_nilai->count() > 0)) {
            new Chart(ctxDistribusiNilai.getContext('2d'), {
                type: 'pie',
        data: {
            labels: @json($distribusi_nilai->pluck('label')),
            datasets: [{
                data: @json($distribusi_nilai->pluck('value')),
                backgroundColor: colors.primary,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
            });
        }

        // Grafik Status Presensi
        const ctxStatusPresensi = document.getElementById('chartStatusPresensi');
        if (ctxStatusPresensi && @json($status_presensi && $status_presensi->count() > 0)) {
            new Chart(ctxStatusPresensi.getContext('2d'), {
                type: 'doughnut',
        data: {
            labels: @json($status_presensi->pluck('label')),
            datasets: [{
                data: @json($status_presensi->pluck('value')),
                backgroundColor: [
                    colors.primary[2], // Hadir - Green
                    colors.primary[3], // Izin - Yellow
                    colors.primary[4], // Sakit - Orange
                    colors.primary[5], // Alpa - Red
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
            });
        }

        // Grafik KRS per Status
        const ctxKRSStatus = document.getElementById('chartKRSStatus');
        if (ctxKRSStatus && @json($krs_per_status && $krs_per_status->count() > 0)) {
            new Chart(ctxKRSStatus.getContext('2d'), {
                type: 'bar',
        data: {
            labels: @json($krs_per_status->pluck('label')),
            datasets: [{
                label: 'Jumlah KRS',
                data: @json($krs_per_status->pluck('value')),
                backgroundColor: [
                    colors.primary[3], // Pending - Yellow
                    colors.primary[2], // Disetujui - Green
                    colors.primary[5], // Ditolak - Red
                ],
                borderColor: [
                    colors.primary[3],
                    colors.primary[2],
                    colors.primary[5],
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
            }
        }
            });
        }
    });
</script>
@endpush
@endsection

