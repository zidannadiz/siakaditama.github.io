@extends('layouts.app')

@section('title', $exam->judul . ' - Belum Dimulai')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $exam->judul }}</h1>
        <p class="text-gray-600 mt-1">{{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-yellow-200 p-8">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-semibold text-yellow-900 mb-2">Ujian Belum Dimulai</h2>
            <p class="text-gray-600 mb-6">Ujian ini akan dimulai pada waktu yang telah ditentukan.</p>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 max-w-md mx-auto">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-lg font-medium text-yellow-900">
                        {{ $exam->mulai->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>

            <div class="space-y-4 max-w-md mx-auto">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Detail Ujian</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Durasi:</span>
                            <span class="font-medium text-gray-900">{{ $exam->durasi }} menit</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Soal:</span>
                            <span class="font-medium text-gray-900">{{ $exam->total_soal }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tipe:</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($exam->tipe) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waktu Selesai:</span>
                            <span class="font-medium text-gray-900">{{ $exam->selesai->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

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

