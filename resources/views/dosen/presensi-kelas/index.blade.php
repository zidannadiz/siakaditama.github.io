@extends('layouts.app')

@section('title', 'Presensi Kelas')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Presensi Kelas</h1>
        <p class="text-gray-600 mt-1">Buka kelas untuk presensi online</p>
    </div>

    @if($activeClasses->count() > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-blue-900 mb-4">Kelas Aktif</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($activeClasses as $class)
                    <div class="bg-white rounded-lg p-4 border border-blue-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $class->jadwalKuliah->mataKuliah->nama_mk ?? 'N/A' }}</h3>
                                <p class="text-sm text-gray-600 mt-1">Pertemuan {{ $class->pertemuan }} • {{ $class->tanggal->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $class->jadwalKuliah->semester->nama_semester ?? 'N/A' }}</p>
                                <div class="mt-3">
                                    <p class="text-xs font-mono bg-gray-100 px-3 py-2 rounded-lg text-gray-800">{{ $class->kode_kelas }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Kode kelas untuk dibagikan</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('dosen.presensi-kelas.show', $class->id) }}" 
                               class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center text-sm font-medium">
                                Kelola Kelas
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Pilih Jadwal untuk Membuka Kelas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($jadwals as $jadwal)
                @php
                    $activeSession = $activeClasses->firstWhere('jadwal_kuliah_id', $jadwal->id);
                @endphp
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 hover:shadow-md transition-shadow">
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
                        @if($activeSession)
                            <a href="{{ route('dosen.presensi-kelas.show', $activeSession->id) }}" 
                               class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center text-sm font-medium">
                                Kelas Aktif
                            </a>
                        @else
                            <button onclick="openModal({{ $jadwal->id }}, '{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}')" 
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                Buka Kelas
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <p class="mt-4 text-gray-500">Tidak ada kelas yang diampu</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Buka Kelas -->
<div id="openClassModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Buka Kelas Baru</h3>
            <form id="openClassForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mata Kuliah</label>
                        <input type="text" id="modal-matkul" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label for="pertemuan" class="block text-sm font-medium text-gray-700 mb-1">Pertemuan <span class="text-red-500">*</span></label>
                        <input type="number" name="pertemuan" id="pertemuan" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="mt-6 flex space-x-3">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Buka Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(jadwalId, matkulName) {
    document.getElementById('modal-matkul').value = matkulName;
    document.getElementById('openClassForm').action = '{{ route("dosen.presensi-kelas.buka", ":id") }}'.replace(':id', jadwalId);
    document.getElementById('openClassModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('openClassModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('openClassModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection

