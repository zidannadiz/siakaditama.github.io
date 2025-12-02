@extends('layouts.app')

@section('title', $exam->judul)

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $exam->judul }}</h1>
        <p class="text-gray-600 mt-1">{{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Ujian</h2>
                <div class="space-y-4">
                    @if($exam->deskripsi)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $exam->deskripsi }}</p>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tipe</label>
                            <p class="mt-1 text-gray-900">{{ ucfirst($exam->tipe) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Durasi</label>
                            <p class="mt-1 text-gray-900">{{ $exam->durasi }} menit</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Jumlah Soal</label>
                            <p class="mt-1 text-gray-900">{{ $exam->total_soal }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Bobot</label>
                            <p class="mt-1 text-gray-900">{{ $exam->bobot }}%</p>
                        </div>
                    </div>
                    @if($exam->mulai)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Waktu Mulai</label>
                        <p class="mt-1 text-gray-900">{{ $exam->mulai->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-500">Waktu Selesai</label>
                        <p class="mt-1 text-gray-900">{{ $exam->selesai->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Pengaturan</label>
                        <div class="mt-2 space-y-1">
                            @if($exam->prevent_copy_paste)
                                <p class="text-sm text-red-600">⚠️ Copy/Paste dilarang</p>
                            @endif
                            @if($exam->prevent_new_tab)
                                <p class="text-sm text-red-600">⚠️ Membuka tab lain dilarang</p>
                            @endif
                            @if($exam->fullscreen_mode)
                                <p class="text-sm text-red-600">⚠️ Mode fullscreen wajib</p>
                            @endif
                            @if($exam->random_soal)
                                <p class="text-sm text-blue-600">✓ Soal diacak</p>
                            @endif
                            @if($exam->random_pilihan)
                                <p class="text-sm text-blue-600">✓ Pilihan jawaban diacak</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if($session && $session->isFinished())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hasil Ujian</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nilai</label>
                            <p class="text-3xl font-bold text-green-600">{{ $session->nilai ? number_format($session->nilai, 2) : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1 text-gray-900">{{ ucfirst(str_replace('_', ' ', $session->status)) }}</p>
                        </div>
                        <a href="{{ route('mahasiswa.exam.result', ['exam' => $exam, 'session' => $session]) }}" 
                           class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @elseif($session && $session->isActive())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ujian Sedang Berlangsung</h3>
                    <p class="text-sm text-gray-600 mb-4">Anda sudah memulai ujian ini. Klik tombol di bawah untuk melanjutkan.</p>
                    <a href="{{ route('mahasiswa.exam.take', ['exam' => $exam, 'session' => $session]) }}" 
                       class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium cursor-pointer">
                        Lanjutkan Ujian
                    </a>
                </div>
            @elseif($exam->isFinished())
                <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6 bg-red-50">
                    <h3 class="text-lg font-semibold text-red-900 mb-2">Ujian Sudah Selesai</h3>
                    <p class="text-sm text-red-700">Waktu ujian sudah berakhir.</p>
                </div>
            @elseif($exam->mulai && now()->isBefore($exam->mulai))
                <div class="bg-white rounded-xl shadow-sm border border-yellow-200 p-6 bg-yellow-50">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">Ujian Belum Dimulai</h3>
                    <p class="text-sm text-yellow-700">Ujian akan dimulai pada: {{ $exam->mulai->format('d M Y, H:i') }}</p>
                </div>
            @elseif($exam->isOngoing())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mulai Ujian</h3>
                    <div class="space-y-3 mb-4">
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm font-medium text-yellow-900">⚠️ Perhatian!</p>
                            <ul class="text-xs text-yellow-800 mt-2 space-y-1 list-disc list-inside">
                                <li>Pastikan koneksi internet stabil</li>
                                <li>Jangan refresh halaman saat ujian</li>
                                <li>Jangan membuka tab/window lain</li>
                                <li>Pelanggaran akan dicatat</li>
                            </ul>
                        </div>
                    </div>
                    <form action="{{ route('mahasiswa.exam.start', $exam) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-lg cursor-pointer">
                            Mulai Ujian
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

