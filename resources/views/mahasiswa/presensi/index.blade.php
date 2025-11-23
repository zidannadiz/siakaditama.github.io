@extends('layouts.app')

@section('title', 'Presensi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Presensi</h1>
        <p class="text-gray-600 mt-1">Lihat presensi Anda per mata kuliah</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($krs_list as $krs)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $krs->jadwalKuliah->mataKuliah->nama_mk }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $krs->jadwalKuliah->mataKuliah->kode_mk }} • {{ $krs->jadwalKuliah->mataKuliah->sks }} SKS</p>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ $krs->jadwalKuliah->hari }}, {{ date('H:i', strtotime($krs->jadwalKuliah->jam_mulai)) }} - {{ date('H:i', strtotime($krs->jadwalKuliah->jam_selesai)) }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $krs->jadwalKuliah->ruangan ?? 'TBA' }}</p>
                        <p class="text-sm text-gray-500">{{ $krs->jadwalKuliah->dosen->nama }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('mahasiswa.presensi.index', ['jadwal_id' => $krs->jadwal_kuliah_id]) }}" 
                       class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center text-sm font-medium">
                        Lihat Presensi
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <p class="mt-4 text-gray-500">Tidak ada mata kuliah yang diambil</p>
            </div>
        @endforelse
    </div>

    @if(request('jadwal_id') && $presensis->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Detail Presensi</h2>
                @if($statistik)
                    <div class="flex space-x-4 text-sm">
                        <div class="text-center">
                            <p class="text-gray-500">Hadir</p>
                            <p class="text-lg font-semibold text-green-600">{{ $statistik['hadir'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500">Izin</p>
                            <p class="text-lg font-semibold text-yellow-600">{{ $statistik['izin'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500">Sakit</p>
                            <p class="text-lg font-semibold text-orange-600">{{ $statistik['sakit'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500">Alpa</p>
                            <p class="text-lg font-semibold text-red-600">{{ $statistik['alpa'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500">Total</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $statistik['total'] }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertemuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($presensis as $presensi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Pertemuan {{ $presensi->pertemuan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $presensi->tanggal->format('d F Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $presensi->status === 'hadir' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $presensi->status === 'izin' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $presensi->status === 'sakit' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $presensi->status === 'alpa' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ strtoupper($presensi->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $presensi->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif(request('jadwal_id'))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <p class="mt-4 text-gray-500">Belum ada presensi untuk mata kuliah ini</p>
        </div>
    @endif
</div>
@endsection

