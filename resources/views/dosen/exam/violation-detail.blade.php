@extends('layouts.app')

@section('title', 'Detail Pelanggaran - ' . $exam->judul)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pelanggaran</h1>
            <p class="text-gray-600 mt-1">
                {{ $exam->judul }} - {{ $session->mahasiswa->nama }} ({{ $session->mahasiswa->nim }})
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.exam.violations', $exam) }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Student Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Mahasiswa</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-500">Nama</span>
                <p class="text-sm font-medium text-gray-900">{{ $session->mahasiswa->nama }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">NIM</span>
                <p class="text-sm font-medium text-gray-900">{{ $session->mahasiswa->nim }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Waktu Mulai</span>
                <p class="text-sm font-medium text-gray-900">{{ $session->started_at->format('d M Y, H:i:s') }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Waktu Selesai</span>
                <p class="text-sm font-medium text-gray-900">
                    {{ $session->finished_at ? $session->finished_at->format('d M Y, H:i:s') : '-' }}
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Status</span>
                <p class="text-sm font-medium">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($session->status === 'terminated') bg-red-100 text-red-800
                        @elseif($session->status === 'submitted') bg-green-100 text-green-800
                        @elseif($session->status === 'auto_submitted') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        @if($session->status === 'terminated') Dihentikan
                        @elseif($session->status === 'submitted') Disubmit
                        @elseif($session->status === 'auto_submitted') Auto Submit
                        @else Sedang Berlangsung
                        @endif
                    </span>
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Nilai</span>
                <p class="text-sm font-medium text-gray-900">
                    {{ $session->nilai !== null ? number_format($session->nilai, 2) : '-' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Violation Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistik Pelanggaran</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="text-sm text-blue-600 font-medium">Tab Switch</div>
                <div class="text-2xl font-bold text-blue-900">{{ $session->tab_switch_count ?? 0 }}</div>
            </div>
            <div class="p-4 bg-red-50 rounded-lg">
                <div class="text-sm text-red-600 font-medium">Copy-Paste</div>
                <div class="text-2xl font-bold text-red-900">{{ $session->copy_paste_attempt_count ?? 0 }}</div>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg">
                <div class="text-sm text-yellow-600 font-medium">Total Pelanggaran</div>
                <div class="text-2xl font-bold text-yellow-900">{{ count($violations) }}</div>
            </div>
        </div>
    </div>

    <!-- Violations List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Riwayat Pelanggaran</h2>
        
        @if(empty($violations))
        <p class="text-gray-500 text-center py-8">Tidak ada riwayat pelanggaran</p>
        @else
        <div class="space-y-4">
            @foreach($violations as $index => $violation)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($violation['type'] === 'tab_switch') bg-blue-100 text-blue-800
                                @elseif($violation['type'] === 'copy_paste') bg-red-100 text-red-800
                                @elseif($violation['type'] === 'window_blur') bg-yellow-100 text-yellow-800
                                @elseif($violation['type'] === 'fullscreen_exit') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $violation['type'])) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                Pelanggaran #{{ $index + 1 }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Waktu:</span> 
                            {{ \Carbon\Carbon::parse($violation['timestamp'])->format('d M Y, H:i:s') }}
                        </div>
                        @if(isset($violation['details']['user_agent']))
                        <div class="text-sm text-gray-600 mt-1">
                            <span class="font-medium">User Agent:</span> 
                            <span class="text-xs">{{ Str::limit($violation['details']['user_agent'], 80) }}</span>
                        </div>
                        @endif
                        @if(isset($violation['details']['reason']))
                        <div class="text-sm text-gray-600 mt-1">
                            <span class="font-medium">Alasan:</span> 
                            {{ $violation['details']['reason'] }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

