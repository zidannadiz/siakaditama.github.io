@extends('layouts.app')

@section('title', 'Ujian')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Ujian</h1>
        <p class="text-gray-600 mt-1">Pilih kelas untuk mengelola ujian</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($jadwals as $jadwal)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $jadwal->mataKuliah->kode_mk ?? 'N/A' }} • {{ $jadwal->mataKuliah->sks ?? 0 }} SKS</p>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ $jadwal->hari }}, {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $jadwal->ruangan ?? 'TBA' }} • {{ $jadwal->semester->nama_semester ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('dosen.exam.create', ['jadwal_id' => $jadwal->id]) }}" 
                       class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center text-sm font-medium block cursor-pointer">
                        + Buat Ujian
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <p class="mt-4 text-gray-500">Tidak ada kelas yang diampu</p>
            </div>
        @endforelse
    </div>

    @if(request('jadwal_id') && $exams->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Daftar Ujian</h2>
            </div>
            <div class="space-y-4">
                @foreach($exams as $exam)
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $exam->judul }}</h3>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($exam->status === 'published') bg-green-100 text-green-800
                                        @elseif($exam->status === 'draft') bg-gray-100 text-gray-800
                                        @elseif($exam->status === 'ongoing') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </div>
                                @if($exam->deskripsi)
                                    <p class="text-sm text-gray-600 mt-2">{{ Str::limit($exam->deskripsi, 100) }}</p>
                                @endif
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                    <span>Tipe: {{ ucfirst($exam->tipe) }}</span>
                                    <span>Durasi: {{ $exam->durasi }} menit</span>
                                    <span>Soal: {{ $exam->total_soal }}</span>
                                    <span>Selesai: {{ $exam->selesai->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('dosen.exam.show', $exam) }}" 
                                   class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm cursor-pointer">
                                    Kelola
                                </a>
                                <a href="{{ route('dosen.exam.edit', $exam) }}" 
                                   class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm cursor-pointer">
                                    Edit
                                </a>
                                <a href="{{ route('dosen.exam.results', $exam) }}" 
                                   class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm cursor-pointer">
                                    Hasil
                                </a>
                                <form action="{{ route('dosen.exam.destroy', $exam) }}" 
                                      method="POST" 
                                      class="inline delete-form"
                                      data-title="Hapus Ujian"
                                      data-message="Apakah Anda yakin ingin menghapus ujian ini? Semua data terkait (soal, jawaban, hasil) akan ikut terhapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors delete-btn cursor-pointer"
                                            title="Hapus Ujian">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(request('jadwal_id'))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <p class="mt-4 text-gray-500">Belum ada ujian untuk kelas ini</p>
            <a href="{{ route('dosen.exam.create', ['jadwal_id' => request('jadwal_id')]) }}" 
               class="mt-4 inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors cursor-pointer">
                Buat Ujian Pertama
            </a>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-form');
            const title = form.getAttribute('data-title');
            const message = form.getAttribute('data-message');
            
            showConfirm(
                title,
                message,
                function() {
                    form.submit();
                },
                function() {
                    closeUniversalModal();
                }
            );
        });
    });
});
</script>
@endsection

