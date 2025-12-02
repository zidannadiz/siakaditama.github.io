@extends('layouts.app')

@section('title', 'Hasil Ujian: ' . $exam->judul)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Hasil Ujian</h1>
            <p class="text-gray-600 mt-1">{{ $exam->judul }} - {{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.exam.show', $exam) }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Kembali
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Peserta</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $sessions->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Sudah Selesai</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $sessions->whereIn('status', ['submitted', 'auto_submitted'])->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Sedang Mengerjakan</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $sessions->where('status', 'started')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Rata-rata Nilai</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">
                {{ $sessions->whereNotNull('nilai')->count() > 0 ? number_format($sessions->whereNotNull('nilai')->avg('nilai'), 2) : '-' }}
            </p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Daftar Hasil Ujian</h2>
        
        @if($sessions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sessions as $session)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->mahasiswa->nim }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->mahasiswa->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->started_at->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $session->finished_at ? $session->finished_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($session->status === 'submitted') bg-green-100 text-green-800
                                        @elseif($session->status === 'auto_submitted') bg-yellow-100 text-yellow-800
                                        @elseif($session->status === 'started') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    @if($session->nilai !== null)
                                        {{ number_format($session->nilai, 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($session->tab_switch_count > 0 || $session->copy_paste_attempt_count > 0)
                                        <span class="text-red-600">
                                            Tab: {{ $session->tab_switch_count }}, 
                                            Copy/Paste: {{ $session->copy_paste_attempt_count }}
                                        </span>
                                    @else
                                        <span class="text-green-600">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('dosen.exam.grade-session', ['exam' => $exam, 'session' => $session]) }}" 
                                       class="text-blue-600 hover:text-blue-900 cursor-pointer">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <p class="mt-4 text-gray-500">Belum ada mahasiswa yang mengerjakan ujian ini</p>
            </div>
        @endif
    </div>
</div>
@endsection

