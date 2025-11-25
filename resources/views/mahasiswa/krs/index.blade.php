@extends('layouts.app')

@section('title', 'KRS')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kartu Rencana Studi (KRS)</h1>
            <p class="text-gray-600 mt-1">{{ $semester_aktif->nama_semester ?? 'Semester Aktif' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($semester_aktif && $krs_list->where('status', 'disetujui')->count() > 0)
                <a href="{{ route('mahasiswa.export.krs', $semester_aktif->id) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export PDF</span>
                </a>
            @endif
            <a href="{{ route('mahasiswa.krs.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                + Tambah Mata Kuliah
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total SKS: <span class="font-bold text-gray-900">{{ $total_sks }}</span></p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($krs_list as $krs)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex-1">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $krs->jadwalKuliah->mataKuliah->nama_mk }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $krs->jadwalKuliah->mataKuliah->kode_mk }} • 
                                    {{ $krs->jadwalKuliah->mataKuliah->sks }} SKS • 
                                    {{ $krs->jadwalKuliah->dosen->nama }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $krs->jadwalKuliah->hari }}, {{ date('H:i', strtotime($krs->jadwalKuliah->jam_mulai)) }} - {{ date('H:i', strtotime($krs->jadwalKuliah->jam_selesai)) }} • 
                                    {{ $krs->jadwalKuliah->ruangan ?? 'TBA' }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="px-3 py-1 text-sm font-medium rounded-full 
                                    @if($krs->status === 'disetujui') bg-green-100 text-green-800
                                    @elseif($krs->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($krs->status) }}
                                </span>
                                @if($krs->status === 'pending')
                                    <form action="{{ route('mahasiswa.krs.destroy', $krs) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus KRS ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium" style="cursor: pointer;">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-4 text-gray-500">Belum ada mata kuliah yang diambil</p>
                    <a href="{{ route('mahasiswa.krs.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Tambah Mata Kuliah
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

