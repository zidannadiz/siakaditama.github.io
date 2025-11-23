@extends('layouts.app')

@section('title', 'Tambah KRS')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Tambah Mata Kuliah ke KRS</h1>
        <p class="text-gray-600 mt-1">{{ $semester_aktif->nama_semester ?? 'Semester Aktif' }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($jadwal_available->count() > 0)
            <form action="{{ route('mahasiswa.krs.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="jadwal_kuliah_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Mata Kuliah *</label>
                    <select id="jadwal_kuliah_id" name="jadwal_kuliah_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Mata Kuliah</option>
                        @foreach($jadwal_available as $jadwal)
                            <option value="{{ $jadwal->id }}">
                                {{ $jadwal->mataKuliah->kode_mk }} - {{ $jadwal->mataKuliah->nama_mk }} 
                                ({{ $jadwal->mataKuliah->sks }} SKS) - 
                                {{ $jadwal->dosen->nama }} - 
                                {{ $jadwal->hari }}, {{ date('H:i', strtotime($jadwal->jam_mulai)) }}-{{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                ({{ $jadwal->terisi }}/{{ $jadwal->kuota }})
                            </option>
                        @endforeach
                    </select>
                    @error('jadwal_kuliah_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('mahasiswa.krs.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Tambah ke KRS
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-4 text-gray-500">Tidak ada mata kuliah yang tersedia</p>
                <a href="{{ route('mahasiswa.krs.index') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Kembali
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

