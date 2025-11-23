@extends('layouts.app')

@section('title', 'Presensi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Presensi</h1>
        <p class="text-gray-600 mt-1">Pilih kelas untuk input presensi</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($jadwals as $jadwal)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $jadwal->mataKuliah->nama_mk }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $jadwal->mataKuliah->kode_mk }} • {{ $jadwal->mataKuliah->sks }} SKS</p>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ $jadwal->hari }}, {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $jadwal->ruangan ?? 'TBA' }} • {{ $jadwal->semester->nama_semester }}</p>
                        <p class="text-sm text-gray-600 mt-2">Mahasiswa: {{ $jadwal->terisi }}/{{ $jadwal->kuota }}</p>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <a href="{{ route('dosen.presensi.show', $jadwal->id) }}" 
                       class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center text-sm font-medium">
                        Rekapan
                    </a>
                    <a href="{{ route('dosen.presensi.create', $jadwal->id) }}" 
                       class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center text-sm font-medium">
                        Input Presensi
                    </a>
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

    @if(request('jadwal_id') && $krs_list->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Pilih Pertemuan</h2>
            <form method="GET" action="{{ route('dosen.presensi.index') }}" class="mb-4">
                <input type="hidden" name="jadwal_id" value="{{ request('jadwal_id') }}">
                <div class="flex space-x-2">
                    <input type="number" name="pertemuan" value="{{ request('pertemuan') }}" min="1" placeholder="Pertemuan ke-" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Lihat</button>
                </div>
            </form>

            @if(request('pertemuan'))
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($krs_list as $krs)
                                @php
                                    $presensi = $presensis->get($krs->mahasiswa_id);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nim }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nama }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($presensi)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $presensi->status === 'hadir' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $presensi->status === 'izin' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $presensi->status === 'sakit' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $presensi->status === 'alpa' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ strtoupper($presensi->status) }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">BELUM</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $presensi->catatan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

