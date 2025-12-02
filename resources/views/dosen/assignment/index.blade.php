@extends('layouts.app')

@section('title', 'Tugas')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Tugas</h1>
        <p class="text-gray-600 mt-1">Pilih kelas untuk mengelola tugas</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($jadwals as $jadwal)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $jadwal->mataKuliah->kode_mk ?? 'N/A' }} • {{ $jadwal->mataKuliah->sks ?? 0 }} SKS</p>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ $jadwal->hari }}, {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $jadwal->ruangan ?? 'TBA' }} • {{ $jadwal->semester->nama_semester ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <a href="{{ route('dosen.assignment.index', ['jadwal_id' => $jadwal->id]) }}" 
                       class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center text-sm font-medium">
                        Lihat Tugas
                    </a>
                    <a href="{{ route('dosen.assignment.create', ['jadwal_id' => $jadwal->id]) }}" 
                       class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center text-sm font-medium">
                        + Buat Tugas
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-4 text-gray-500">Tidak ada kelas yang diampu</p>
            </div>
        @endforelse
    </div>

    @if(request('jadwal_id') && $assignments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Daftar Tugas</h2>
                <a href="{{ route('dosen.assignment.create', ['jadwal_id' => request('jadwal_id')]) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                    + Buat Tugas Baru
                </a>
            </div>
            <div class="space-y-4">
                @foreach($assignments as $assignment)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $assignment->judul }}</h3>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($assignment->status === 'published') bg-green-100 text-green-800
                                        @elseif($assignment->status === 'draft') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </div>
                                @if($assignment->deskripsi)
                                    <p class="text-sm text-gray-600 mt-2">{{ Str::limit($assignment->deskripsi, 100) }}</p>
                                @endif
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                    <span>Deadline: {{ $assignment->deadline->format('d M Y, H:i') }}</span>
                                    <span>Bobot: {{ $assignment->bobot }}%</span>
                                    <span>Submissions: {{ $assignment->submissions->count() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('dosen.assignment.show', $assignment) }}" 
                                   class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    Lihat
                                </a>
                                <a href="{{ route('dosen.assignment.edit', $assignment) }}" 
                                   class="px-3 py-1 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('dosen.assignment.destroy', $assignment) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus tugas ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(request('jadwal_id'))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-4 text-gray-500">Belum ada tugas untuk kelas ini</p>
            <a href="{{ route('dosen.assignment.create', ['jadwal_id' => request('jadwal_id')]) }}" 
               class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Buat Tugas Pertama
            </a>
        </div>
    @endif
</div>
@endsection

