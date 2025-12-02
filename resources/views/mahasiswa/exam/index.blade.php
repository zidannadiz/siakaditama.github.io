@extends('layouts.app')

@section('title', 'Ujian')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Ujian</h1>
        <p class="text-gray-600 mt-1">Daftar ujian dari mata kuliah yang Anda ambil</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="space-y-4">
            @forelse($exams as $exam)
                <div class="p-5 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $exam->judul }}</h3>
                                @if($exam->isFinished())
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">Selesai</span>
                                @elseif($exam->isOngoing())
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Berlangsung</span>
                                @elseif($exam->mulai && now()->isBefore($exam->mulai))
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">Belum Dimulai</span>
                                @endif
                                @if($exam->session && $exam->session->isFinished())
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800">Sudah Dikerjakan</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
                            @if($exam->deskripsi)
                                <p class="text-sm text-gray-600 mt-2">{{ Str::limit($exam->deskripsi, 150) }}</p>
                            @endif
                            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                                <span>Tipe: {{ ucfirst($exam->tipe) }}</span>
                                <span>Durasi: {{ $exam->durasi }} menit</span>
                                <span>Soal: {{ $exam->total_soal }}</span>
                                @if($exam->mulai)
                                    <span>Mulai: {{ $exam->mulai->format('d M Y, H:i') }}</span>
                                @endif
                                <span>Selesai: {{ $exam->selesai->format('d M Y, H:i') }}</span>
                                @if($exam->session && $exam->session->nilai !== null)
                                    <span class="font-semibold text-green-600">Nilai: {{ number_format($exam->session->nilai, 2) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('mahasiswa.exam.show', $exam) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium cursor-pointer">
                                {{ $exam->session && $exam->session->isFinished() ? 'Lihat Hasil' : ($exam->session && $exam->session->isActive() ? 'Lanjutkan' : 'Mulai') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <p class="mt-4 text-gray-500">Belum ada ujian</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

