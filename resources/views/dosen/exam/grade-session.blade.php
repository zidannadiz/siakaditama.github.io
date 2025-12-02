@extends('layouts.app')

@section('title', 'Nilai Ujian')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Nilai Ujian</h1>
            <p class="text-gray-600 mt-1">{{ $exam->judul }} - {{ $session->mahasiswa->nama }} ({{ $session->mahasiswa->nim }})</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.exam.results', $exam) }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Kembali
            </a>
        </div>
    </div>

    <!-- Session Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-500">Mulai</label>
                <p class="text-sm text-gray-900 mt-1">{{ $session->started_at->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Selesai</label>
                <p class="text-sm text-gray-900 mt-1">{{ $session->finished_at ? $session->finished_at->format('d M Y, H:i') : '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Nilai Saat Ini</label>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $session->nilai ? number_format($session->nilai, 2) : '-' }}</p>
            </div>
        </div>
        @if($session->violations && count($session->violations) > 0)
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            <label class="text-sm font-medium text-red-900">Pelanggaran</label>
            <div class="mt-2 space-y-1">
                @foreach($session->violations as $violation)
                    <p class="text-xs text-red-700">
                        {{ ucfirst(str_replace('_', ' ', $violation['type'])) }} - 
                        {{ \Carbon\Carbon::parse($violation['timestamp'])->format('d M Y, H:i:s') }}
                    </p>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Grade Form -->
    @if($essayAnswers->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Nilai Soal Essay</h2>
            
            <form action="{{ route('dosen.exam.grade-session.store', ['exam' => $exam, 'session' => $session]) }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    @foreach($essayAnswers as $answer)
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="mb-3">
                                <h3 class="font-semibold text-gray-900">Soal:</h3>
                                <p class="text-gray-700 mt-1">{{ $answer->examQuestion->pertanyaan }}</p>
                                <p class="text-xs text-gray-500 mt-1">Bobot: {{ $answer->examQuestion->bobot }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="text-sm font-medium text-gray-700">Jawaban Mahasiswa:</label>
                                <div class="mt-2 p-3 bg-white rounded border border-gray-300">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $answer->jawaban_essay ?? '-' }}</p>
                                </div>
                            </div>

                            @if($answer->examQuestion->jawaban_benar_essay)
                            <div class="mb-3">
                                <label class="text-sm font-medium text-gray-700">Kunci Jawaban:</label>
                                <div class="mt-2 p-3 bg-blue-50 rounded border border-blue-200">
                                    <p class="text-blue-900 whitespace-pre-wrap">{{ $answer->examQuestion->jawaban_benar_essay }}</p>
                                </div>
                            </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nilai_{{ $answer->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nilai (0 - {{ $answer->examQuestion->bobot }}) *
                                    </label>
                                    <input type="number" 
                                           id="nilai_{{ $answer->id }}" 
                                           name="answers[{{ $answer->id }}][nilai]" 
                                           value="{{ old("answers.{$answer->id}.nilai", $answer->nilai) }}"
                                           min="0" 
                                           max="{{ $answer->examQuestion->bobot }}"
                                           step="0.01"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                </div>
                                <div>
                                    <label for="feedback_{{ $answer->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                        Feedback
                                    </label>
                                    <textarea id="feedback_{{ $answer->id }}" 
                                              name="answers[{{ $answer->id }}][feedback]" 
                                              rows="2"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old("answers.{$answer->id}.feedback", $answer->feedback) }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium cursor-pointer">
                            Simpan Nilai
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-4 text-gray-500">Tidak ada soal essay yang perlu dinilai</p>
                <p class="text-sm text-gray-400 mt-2">Soal pilihan ganda sudah dinilai otomatis</p>
            </div>
        </div>
    @endif

    <!-- All Answers Review -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Semua Jawaban</h2>
        
        <div class="space-y-4">
            @foreach($session->answers->sortBy(function($answer) {
                return $answer->examQuestion->urutan ?? 999;
            }) as $answer)
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="font-semibold text-gray-900">Soal {{ $answer->examQuestion->urutan ?? '?' }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $answer->examQuestion->tipe === 'pilgan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($answer->examQuestion->tipe) }}
                            </span>
                        </div>
                        @if($answer->nilai !== null)
                            <span class="px-3 py-1 text-sm font-medium rounded bg-green-100 text-green-800">
                                Nilai: {{ number_format($answer->nilai, 2) }} / {{ $answer->examQuestion->bobot }}
                            </span>
                        @endif
                    </div>
                    
                    <p class="text-gray-700 mb-3">{{ $answer->examQuestion->pertanyaan }}</p>
                    
                    @if($answer->examQuestion->isPilgan())
                        <div>
                            <label class="text-sm font-medium text-gray-700">Jawaban:</label>
                            <p class="mt-1 text-gray-900">
                                <span class="font-medium">{{ $answer->jawaban_pilgan ?? '-' }}</span>
                                @if($answer->jawaban_pilgan === $answer->examQuestion->jawaban_benar)
                                    <span class="ml-2 text-green-600 font-medium">✓ Benar</span>
                                @elseif($answer->jawaban_pilgan)
                                    <span class="ml-2 text-red-600 font-medium">✗ Salah (Benar: {{ $answer->examQuestion->jawaban_benar }})</span>
                                @endif
                            </p>
                        </div>
                    @else
                        <div>
                            <label class="text-sm font-medium text-gray-700">Jawaban:</label>
                            <div class="mt-2 p-3 bg-white rounded border border-gray-300">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $answer->jawaban_essay ?? '-' }}</p>
                            </div>
                            @if($answer->feedback)
                                <div class="mt-2 p-3 bg-blue-50 rounded border border-blue-200">
                                    <label class="text-sm font-medium text-blue-900">Feedback:</label>
                                    <p class="text-blue-800 mt-1 whitespace-pre-wrap">{{ $answer->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

