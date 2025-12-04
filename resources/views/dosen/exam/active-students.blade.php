@extends('layouts.app')

@section('title', 'Mahasiswa yang Sedang Mengerjakan - ' . $exam->judul)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mahasiswa yang Sedang Mengerjakan</h1>
            <p class="text-gray-600 mt-1">{{ $exam->judul }} - {{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.exam.ongoing') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Kembali
            </a>
        </div>
    </div>

    @if($activeSessions->isEmpty())
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
        <p class="text-blue-800">Tidak ada mahasiswa yang sedang mengerjakan ujian ini saat ini.</p>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">
                Total: {{ $activeSessions->count() }} mahasiswa sedang mengerjakan
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Mahasiswa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            NIM
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Waktu Mulai
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Waktu Tersisa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pelanggaran
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($activeSessions as $index => $session)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $session->mahasiswa->nama }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $session->mahasiswa->nim }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $session->started_at->format('H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium 
                                @if($session->time_remaining_seconds < 300) text-red-600
                                @elseif($session->time_remaining_seconds < 600) text-yellow-600
                                @else text-gray-900
                                @endif">
                                {{ $session->time_remaining_formatted }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" 
                                     style="width: {{ min(100, $session->progress_percentage) }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ number_format($session->progress_percentage, 1) }}%
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $violations = $session->violations ?? [];
                                $violationCount = is_array($violations) ? count($violations) : 0;
                            @endphp
                            @if($violationCount > 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($violationCount >= 3) bg-red-100 text-red-800
                                    @elseif($violationCount >= 2) bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ $violationCount }} kali
                                </span>
                            @else
                                <span class="text-xs text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Auto refresh info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
            <strong>Catatan:</strong> Halaman ini tidak auto-refresh. Silakan refresh manual untuk melihat update terbaru.
        </p>
    </div>
    @endif
</div>
@endsection

