@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Mahasiswa</h1>
        <p class="text-gray-600 mt-1">Selamat datang, {{ $mahasiswa->nama }}</p>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm font-medium text-gray-600">NIM</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $mahasiswa->nim }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm font-medium text-gray-600">Program Studi</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $mahasiswa->prodi->nama_prodi }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <p class="text-sm font-medium text-gray-600">Total SKS</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $total_sks }} SKS</p>
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
                                <p class="text-sm text-gray-600 mt-1">{{ $jadwal->dosen->nama }} • {{ $jadwal->ruangan ?? 'TBA' }} • {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- KRS Semester Ini -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">KRS {{ $semester_aktif->nama_semester ?? 'Semester Aktif' }}</h2>
            <a href="{{ route('mahasiswa.krs.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Lihat Semua →
            </a>
        </div>
        <div class="p-6">
            @if($krs_semester_ini->count() > 0)
                <div class="space-y-3">
                    @foreach($krs_semester_ini->take(5) as $krs)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $krs->jadwalKuliah->mataKuliah->nama_mk }}</p>
                                <p class="text-sm text-gray-600">{{ $krs->jadwalKuliah->mataKuliah->sks }} SKS • {{ $krs->jadwalKuliah->dosen->nama }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($krs->status === 'disetujui') bg-green-100 text-green-800
                                @elseif($krs->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($krs->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada KRS untuk semester ini</p>
            @endif
        </div>
    </div>

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
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        @if($pengumuman->is_pinned)
                                            <span class="px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Pinned</span>
                                        @endif
                                        <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded">{{ ucfirst($pengumuman->kategori) }}</span>
                                    </div>
                                    <p class="font-medium text-gray-900 mt-2">{{ $pengumuman->judul }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($pengumuman->isi, 150) }}</p>
                                    <div class="flex items-center justify-between mt-2">
                                        <p class="text-xs text-gray-500">
                                            @if($pengumuman->published_at)
                                                @if($pengumuman->published_at->isFuture())
                                                    Akan muncul: {{ $pengumuman->published_at->format('d M Y H:i') }}
                                                @else
                                                    Dipublikasikan: {{ $pengumuman->published_at->format('d M Y H:i') }}
                                                @endif
                                            @else
                                                Dipublikasikan langsung
                                            @endif
                                        </p>
                                    </div>
                                </div>
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
@endsection

