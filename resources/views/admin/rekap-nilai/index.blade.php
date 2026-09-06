@extends('layouts.app')

@section('title', 'Rekap Nilai')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Rekap Nilai Mahasiswa</h1>
            <p class="text-gray-600 mt-1">Pemantauan nilai akademik mahasiswa prodi (Hanya Lihat)</p>
        </div>
        <div class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Akses: Hanya Lihat
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.rekap-nilai.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="semester_id" class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                <select name="semester_id" id="semester_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sm)
                        <option value="{{ $sm->id }}" {{ request('semester_id') == $sm->id ? 'selected' : '' }}>
                            {{ $sm->nama_semester }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari Mahasiswa / MK</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama, NIM, atau Mata Kuliah..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.rekap-nilai.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen Pengampu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">UTS</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">UAS</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Akhir</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Huruf</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($nilais as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->mahasiswa->nama ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->mahasiswa->nim ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->jadwalKuliah->mataKuliah->nama_mk ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->jadwalKuliah->mataKuliah->kode_mk ?? '-' }} • {{ $item->jadwalKuliah->mataKuliah->sks ?? 0 }} SKS</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $item->dosen->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item->nilai_tugas !== null ? number_format($item->nilai_tugas, 1) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item->nilai_uts !== null ? number_format($item->nilai_uts, 1) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item->nilai_uas !== null ? number_format($item->nilai_uas, 1) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">
                                {{ $item->nilai_akhir !== null ? number_format($item->nilai_akhir, 1) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item->huruf_mutu)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        @if(in_array($item->huruf_mutu, ['A', 'A-'])) bg-green-100 text-green-800
                                        @elseif(in_array($item->huruf_mutu, ['B+', 'B', 'B-'])) bg-blue-100 text-blue-800
                                        @elseif(in_array($item->huruf_mutu, ['C+', 'C'])) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $item->huruf_mutu }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Belum dinilai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                Tidak ada data nilai mahasiswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $nilais->links() }}
        </div>
    </div>
</div>
@endsection
