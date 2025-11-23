@extends('layouts.app')

@section('title', 'Input Nilai')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Input Nilai</h1>
        <p class="text-gray-600 mt-1">Pilih kelas untuk input nilai</p>
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
                    <a href="{{ route('dosen.nilai.index', ['jadwal_id' => $jadwal->id]) }}" 
                       class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center text-sm font-medium">
                        Lihat Nilai
                    </a>
                    <a href="{{ route('dosen.nilai.create', $jadwal->id) }}" 
                       class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center text-sm font-medium">
                        Input Nilai
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

    @if(request('jadwal_id') && $nilais->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Daftar Nilai</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UTS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UAS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Huruf</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($nilais as $nilai)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $nilai->mahasiswa->nim }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $nilai->mahasiswa->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $nilai->nilai_tugas ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $nilai->nilai_uts ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $nilai->nilai_uas ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 2) : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $nilai->huruf_mutu ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('dosen.nilai.edit', $nilai) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

