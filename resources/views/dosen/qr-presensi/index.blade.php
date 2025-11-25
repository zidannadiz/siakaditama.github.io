@extends('layouts.app')

@section('title', 'QR Code Presensi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">QR Code Presensi</h1>
        <p class="text-gray-600 mt-1">Generate QR code untuk presensi mahasiswa</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(request('jadwal_id') && $qrSession)
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
            <p class="font-semibold">QR Code aktif ditemukan!</p>
            <p class="text-sm mt-1">QR Code untuk pertemuan {{ $qrSession->pertemuan }} masih aktif hingga {{ $qrSession->expires_at->format('H:i') }}</p>
            <a href="{{ route('dosen.qr-presensi.show', ['jadwal_id' => request('jadwal_id'), 'token' => $qrSession->token]) }}" 
               class="mt-2 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                Lihat QR Code
            </a>
        </div>
    @endif

    <!-- Form Generate QR Code -->
    @if($selectedJadwal ?? null)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Generate QR Code</h2>
            <form action="{{ route('dosen.qr-presensi.generate', $selectedJadwal->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mata Kuliah</label>
                        <p class="text-gray-900 font-medium">{{ $selectedJadwal->mataKuliah->nama_mk ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">{{ $selectedJadwal->mataKuliah->kode_mk ?? 'N/A' }} • {{ $selectedJadwal->semester->nama_semester ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label for="pertemuan" class="block text-sm font-medium text-gray-700 mb-2">Pertemuan <span class="text-red-500">*</span></label>
                        <input type="number" 
                               id="pertemuan" 
                               name="pertemuan" 
                               value="{{ old('pertemuan', ($pertemuan_terakhir ?? 0) + 1) }}" 
                               min="1" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('pertemuan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" 
                               id="tanggal" 
                               name="tanggal" 
                               value="{{ old('tanggal', date('Y-m-d')) }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('tanggal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Durasi Valid (menit)</label>
                        <input type="number" 
                               id="duration_minutes" 
                               name="duration_minutes" 
                               value="{{ old('duration_minutes', 30) }}" 
                               min="5" 
                               max="120"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Durasi QR code valid (default: 30 menit, maksimal: 120 menit)</p>
                        @error('duration_minutes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Generate QR Code
                        </button>
                        <a href="{{ route('dosen.qr-presensi.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <!-- Daftar Jadwal -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Pilih Jadwal Kuliah</h2>
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
                            <p class="text-sm text-gray-600 mt-2">Mahasiswa: {{ $jadwal->terisi }}/{{ $jadwal->kuota }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('dosen.qr-presensi.index', ['jadwal_id' => $jadwal->id]) }}" 
                           class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center text-sm font-medium">
                            Generate QR Code
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <p class="mt-4 text-gray-500">Tidak ada kelas yang diampu</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
