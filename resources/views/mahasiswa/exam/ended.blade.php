@extends('layouts.app')

@section('title', $exam->judul . ' - Sudah Berakhir')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $exam->judul }}</h1>
        <p class="text-gray-600 mt-1">{{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-8">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-semibold text-red-900 mb-2">Ujian Sudah Berakhir</h2>
            <p class="text-gray-600 mb-6">Waktu ujian ini sudah habis dan tidak dapat diakses lagi.</p>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 max-w-md mx-auto">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-lg font-medium text-red-900">
                        Berakhir: {{ $exam->selesai->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>

            @php
                $mahasiswa = \App\Models\Mahasiswa::where('user_id', Auth::id())->first();
                $session = \App\Models\ExamSession::where('exam_id', $exam->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->first();
            @endphp

            @if($session && $session->isFinished())
                <div class="bg-gray-50 rounded-lg p-4 mb-6 max-w-md mx-auto">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Status Ujian Anda</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $session->status)) }}</span>
                        </div>
                        @if($session->nilai !== null)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nilai:</span>
                            <span class="font-medium text-green-600">{{ number_format($session->nilai, 2) }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('mahasiswa.exam.result', ['exam' => $exam, 'session' => $session]) }}" 
                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium cursor-pointer">
                            Lihat Hasil Ujian
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-4 mb-6 max-w-md mx-auto">
                    <p class="text-sm text-gray-600">Anda belum menyelesaikan ujian ini.</p>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('mahasiswa.exam.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors font-medium cursor-pointer">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Daftar Ujian
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

