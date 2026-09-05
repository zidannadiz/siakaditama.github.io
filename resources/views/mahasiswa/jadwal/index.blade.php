@extends('layouts.app')

@section('title', 'Jadwal Kuliah')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Jadwal Kuliah</h1>
            <p class="text-gray-600 mt-1">Daftar jadwal perkuliahan Anda berdasarkan KRS yang telah disetujui</p>
        </div>
        <div class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Akses: Hanya Lihat
        </div>
    </div>

    <!-- Filter Semester -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('mahasiswa.jadwal.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="w-full sm:w-64">
                <label for="semester_id" class="block text-xs font-medium text-gray-700 mb-1">Pilih Semester</label>
                <select name="semester_id" id="semester_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" onchange="this.form.submit()">
                    @foreach($semesters as $sm)
                        <option value="{{ $sm->id }}" {{ ($semester_aktif && $semester_aktif->id == $sm->id) ? 'selected' : '' }}>
                            {{ $sm->nama_semester }} {{ $sm->status === 'aktif' ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(request('semester_id'))
            <div class="pt-5">
                <a href="{{ route('mahasiswa.jadwal.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Semester Aktif</a>
            </div>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari & Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen Pengampu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruangan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SKS</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jadwals as $jadwal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-blue-50 text-blue-700">
                                    {{ $jadwal->hari }}
                                </span>
                                <div class="text-xs text-gray-600 mt-1 font-mono">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $jadwal->mataKuliah->nama_mk }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $jadwal->mataKuliah->kode_mk }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $jadwal->dosen->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $jadwal->ruangan ?? 'Ruang Kuliah' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                {{ $jadwal->mataKuliah->sks }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada jadwal kuliah yang disetujui untuk semester ini. Pastikan KRS Anda telah disetujui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
