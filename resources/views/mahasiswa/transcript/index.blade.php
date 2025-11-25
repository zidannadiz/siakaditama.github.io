@extends('layouts.app')

@section('title', 'Transkrip Nilai')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Transkrip Nilai</h1>
            <p class="text-gray-600 mt-1">Transkrip nilai akademik resmi</p>
        </div>
        <a href="{{ route('mahasiswa.transcript.download') }}" 
           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Download PDF</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">TRANSKRIP NILAI</h2>
            <p class="text-gray-600">Sistem Informasi Akademik</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">NIM</p>
                <p class="text-lg font-semibold text-gray-900">{{ $mahasiswa->nim }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Nama</p>
                <p class="text-lg font-semibold text-gray-900">{{ $mahasiswa->nama }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Program Studi</p>
                <p class="text-lg font-semibold text-gray-900">{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Email</p>
                <p class="text-lg font-semibold text-gray-900">{{ $mahasiswa->user->email ?? '-' }}</p>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <strong>Catatan:</strong> Transkrip nilai ini berisi semua nilai yang telah diinput oleh dosen. 
                Untuk mendapatkan transkrip resmi, silakan download PDF dan verifikasi ke bagian akademik.
            </p>
        </div>
    </div>
</div>
@endsection

