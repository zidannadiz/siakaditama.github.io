@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Dosen</h1>
        <p class="text-gray-600 mt-1">Selamat datang, {{ $dosen->nama }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Kelas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $total_kelas }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Nilai Belum Input</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $nilai_belum_input }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Jadwal Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $jadwal_hari_ini->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Hari Ini -->
    @if($jadwal_hari_ini->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Jadwal Hari Ini</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($jadwal_hari_ini as $jadwal)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $jadwal->mataKuliah->nama_mk }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $jadwal->ruangan ?? 'TBA' }} • {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</p>
                            </div>
                            <a href="{{ route('dosen.nilai.index', ['jadwal_id' => $jadwal->id]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                Input Nilai
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Pengumuman -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Pengumuman</h2>
        </div>
        <div class="p-6">
            @if($pengumuman_terbaru->count() > 0)
                <div class="space-y-4">
                    @foreach($pengumuman_terbaru as $pengumuman)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="font-medium text-gray-900">{{ $pengumuman->judul }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($pengumuman->isi, 150) }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $pengumuman->published_at->format('d M Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada pengumuman</p>
            @endif
        </div>
    </div>
</div>
@endsection

