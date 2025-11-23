@extends('layouts.app')

@section('title', 'Rekapan Presensi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Rekapan Presensi</h1>
            <p class="text-gray-600 mt-1">{{ $jadwal->mataKuliah->nama_mk }} - {{ $jadwal->semester->nama_semester }}</p>
        </div>
        <a href="{{ route('dosen.presensi.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Kembali
        </a>
    </div>

    <!-- Statistik Presensi -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($statistik as $stat)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">{{ $stat['mahasiswa']->nama }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stat['mahasiswa']->nim }}</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <p class="text-gray-500">Hadir</p>
                        <p class="text-lg font-semibold text-green-600">{{ $stat['hadir'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Izin</p>
                        <p class="text-lg font-semibold text-yellow-600">{{ $stat['izin'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Sakit</p>
                        <p class="text-lg font-semibold text-orange-600">{{ $stat['sakit'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Alpa</p>
                        <p class="text-lg font-semibold text-red-600">{{ $stat['alpa'] }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-500">Total: <span class="font-semibold text-gray-900">{{ $stat['total'] }}</span></p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Detail Presensi per Pertemuan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Presensi per Pertemuan</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertemuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        @foreach($krs_list as $krs)
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $krs->mahasiswa->nama }}<br>
                                <span class="text-xs text-gray-400">{{ $krs->mahasiswa->nim }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($presensis as $pertemuan => $presensi_pertemuan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pertemuan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $presensi_pertemuan->first()->tanggal->format('d/m/Y') }}
                            </td>
                            @foreach($krs_list as $krs)
                                @php
                                    $presensi = $presensi_pertemuan->firstWhere('mahasiswa_id', $krs->mahasiswa_id);
                                @endphp
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($presensi)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $presensi->status === 'hadir' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $presensi->status === 'izin' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $presensi->status === 'sakit' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $presensi->status === 'alpa' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ strtoupper($presensi->status) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $krs_list->count() + 2 }}" class="px-6 py-8 text-center text-gray-500">
                                Belum ada presensi yang diinput
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

