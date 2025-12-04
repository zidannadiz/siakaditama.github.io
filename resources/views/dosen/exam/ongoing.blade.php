@extends('layouts.app')

@section('title', 'Ujian yang Sedang Berlangsung')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Ujian yang Sedang Berlangsung</h1>
            <p class="text-gray-600 mt-1">Daftar ujian yang sedang dikerjakan oleh mahasiswa</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.exam.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Kembali ke Daftar Ujian
            </a>
            <a href="{{ route('dosen.exam.finished') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors cursor-pointer">
                Ujian Selesai
            </a>
        </div>
    </div>

    @if($exams->isEmpty())
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
        <p class="text-blue-800">Tidak ada ujian yang sedang berlangsung saat ini.</p>
    </div>
    @else
    <div class="grid grid-cols-1 gap-6">
        @foreach($exams as $exam)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $exam->judul }}</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $exam->jadwalKuliah->mataKuliah->nama_mk ?? '-' }} - 
                        {{ $exam->jadwalKuliah->semester->nama_semester ?? '-' }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                        Sedang Berlangsung
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <div class="text-sm text-blue-600 font-medium">Mahasiswa Aktif</div>
                    <div class="text-2xl font-bold text-blue-900">{{ $exam->active_count }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-sm text-gray-600 font-medium">Total Peserta</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $exam->total_sessions }}</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-3">
                    <div class="text-sm text-purple-600 font-medium">Durasi</div>
                    <div class="text-lg font-bold text-purple-900">{{ $exam->durasi }} menit</div>
                </div>
                <div class="bg-orange-50 rounded-lg p-3">
                    <div class="text-sm text-orange-600 font-medium">Waktu Selesai</div>
                    <div class="text-sm font-bold text-orange-900">{{ $exam->selesai->format('d M Y, H:i') }}</div>
                </div>
            </div>

            @if($exam->active_count > 0)
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Mahasiswa yang Sedang Mengerjakan ({{ $exam->active_count }})</h3>
                    <a href="{{ route('dosen.exam.active-students', $exam) }}" 
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium cursor-pointer">
                        Lihat Detail →
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($exam->active_sessions->take(6) as $session)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-sm font-medium text-gray-900">{{ $session->mahasiswa->nama }}</div>
                        <div class="text-xs text-gray-500">{{ $session->mahasiswa->nim }}</div>
                        <div class="text-xs text-gray-600 mt-1">
                            Mulai: {{ $session->started_at->format('H:i:s') }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($exam->active_count > 6)
                <div class="mt-3 text-center">
                    <a href="{{ route('dosen.exam.active-students', $exam) }}" 
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium cursor-pointer">
                        Lihat {{ $exam->active_count - 6 }} mahasiswa lainnya →
                    </a>
                </div>
                @endif
            </div>
            @endif

            <div class="flex items-center justify-end space-x-3 mt-4 pt-4 border-t border-gray-200 -mr-2">
                <a href="{{ route('dosen.exam.show', $exam) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium cursor-pointer">
                    Detail Ujian
                </a>
                <a href="{{ route('dosen.exam.active-students', $exam) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium cursor-pointer">
                    Lihat Semua Mahasiswa
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

