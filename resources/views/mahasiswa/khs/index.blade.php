@extends('layouts.app')

@section('title', 'KHS')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kartu Hasil Studi (KHS)</h1>
            <p class="text-gray-600 mt-1">{{ $mahasiswa->nama }} - {{ $mahasiswa->nim }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <form method="GET" action="{{ route('mahasiswa.khs.index') }}" class="flex items-center space-x-2">
                <select name="semester_id" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Pilih Semester</option>
                    @foreach($semesters as $s)
                        <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_semester }}
                        </option>
                    @endforeach
                </select>
            </form>
            @if($nilais->count() > 0)
                <a href="{{ route('mahasiswa.export.khs', $semester->id) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export PDF</span>
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Semester</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $semester->nama_semester }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">Total SKS</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $total_sks }} SKS</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">IPK Semester</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($ipk, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode MK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Huruf Mutu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bobot</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($nilais as $nilai)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $nilai->jadwalKuliah->mataKuliah->kode_mk }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $nilai->jadwalKuliah->mataKuliah->nama_mk }}</div>
                                <div class="text-sm text-gray-500">{{ $nilai->dosen->nama }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $nilai->jadwalKuliah->mataKuliah->sks }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($nilai->huruf_mutu)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($nilai->huruf_mutu == 'A' || $nilai->huruf_mutu == 'A-') bg-green-100 text-green-800
                                        @elseif($nilai->huruf_mutu == 'B+' || $nilai->huruf_mutu == 'B' || $nilai->huruf_mutu == 'B-') bg-blue-100 text-blue-800
                                        @elseif($nilai->huruf_mutu == 'C+' || $nilai->huruf_mutu == 'C' || $nilai->huruf_mutu == 'C-') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $nilai->huruf_mutu }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $nilai->bobot ? number_format($nilai->bobot, 2) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada nilai untuk semester ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

