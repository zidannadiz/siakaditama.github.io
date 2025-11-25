@extends('layouts.app')

@section('title', 'Kelas Aktif')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $classSession->jadwalKuliah->mataKuliah->nama_mk ?? 'N/A' }}</h1>
            <p class="text-gray-600 mt-1">Pertemuan {{ $classSession->pertemuan }} • {{ $classSession->tanggal->format('d F Y') }}</p>
        </div>
        <div>
            @if($classSession->status === 'buka' && !$classSession->closed_at)
                <form method="POST" action="{{ route('dosen.presensi-kelas.tutup', $classSession->id) }}" id="tutup-kelas-form" class="inline">
                    @csrf
                    <button type="button" 
                            onclick="if(window.showConfirmModal){window.showConfirmModal('Konfirmasi Tutup Kelas', 'Yakin ingin menutup kelas? Semua presensi akan disimpan dan kelas tidak bisa diakses lagi.', document.getElementById('tutup-kelas-form'))}else{alert('Modal tidak tersedia');}" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                            style="cursor: pointer;">
                        Tutup Kelas
                    </button>
                </form>
            @else
                <span class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">Kelas Ditutup</span>
            @endif
        </div>
    </div>

    <!-- Kode Kelas -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90 mb-2">Kode Kelas</p>
                <p class="text-3xl font-mono font-bold">{{ $classSession->kode_kelas }}</p>
                <p class="text-sm opacity-75 mt-2">Bagikan kode ini kepada mahasiswa untuk bergabung</p>
            </div>
            <div>
                <button onclick="copyCode()" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Mahasiswa</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $krs_list->count() }}</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4">
            <p class="text-sm text-green-600">Hadir</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $attendances->where('status', 'hadir')->where('is_kicked', false)->count() }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200 p-4">
            <p class="text-sm text-yellow-600">Izin/Sakit</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $attendances->whereIn('status', ['izin', 'sakit'])->count() }}</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow-sm border border-red-200 p-4">
            <p class="text-sm text-red-600">Tidak Hadir</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ $krs_list->count() - $attendances->where('is_kicked', false)->count() }}</p>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Daftar Peserta</h2>
            @if($classSession->status === 'buka' && !$classSession->closed_at)
                <button onclick="location.reload()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Refresh</span>
                </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($krs_list as $krs)
                        @php
                            $attendance = $attendances->get($krs->mahasiswa_id);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance)
                                    @if($attendance->is_kicked)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Dikeluarkan</span>
                                    @elseif($attendance->status === 'hadir')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Hadir</span>
                                    @elseif($attendance->status === 'izin')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Izin</span>
                                    @elseif($attendance->status === 'sakit')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Sakit</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($attendance->status) }}</span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Belum Masuk</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($attendance && $attendance->waktu_masuk)
                                    {{ $attendance->waktu_masuk->format('H:i:s') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($attendance && $attendance->status === 'hadir' && !$attendance->is_kicked && $classSession->status === 'buka')
                                    <form method="POST" action="{{ route('dosen.presensi-kelas.kick', [$classSession->id, $krs->mahasiswa_id]) }}" id="kick-form-{{ $krs->mahasiswa_id }}" class="inline">
                                        @csrf
                                        <button type="button" 
                                                onclick="if(window.showConfirmModal){window.showConfirmModal('Konfirmasi Keluarkan Mahasiswa', 'Yakin ingin mengeluarkan {{ $krs->mahasiswa->nama }} dari kelas? Mahasiswa yang dikeluarkan akan dianggap tidak hadir.', document.getElementById('kick-form-{{ $krs->mahasiswa_id }}'))}else{alert('Modal tidak tersedia');}" 
                                                class="text-red-600 hover:text-red-900 font-medium"
                                                style="cursor: pointer;">
                                            Kick
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function copyCode() {
    const code = '{{ $classSession->kode_kelas }}';
    navigator.clipboard.writeText(code).then(() => {
        alert('Kode kelas berhasil disalin!');
    });
}
</script>
@endsection

