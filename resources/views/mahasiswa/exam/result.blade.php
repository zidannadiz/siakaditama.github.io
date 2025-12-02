@extends('layouts.app')

@section('title', 'Hasil Ujian: ' . $exam->judul)

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Hasil Ujian</h1>
        <p class="text-gray-600 mt-1">{{ $exam->judul }} - {{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
    </div>

    <!-- Score Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-center">
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">Nilai Anda</h2>
            @if($session->nilai !== null)
                <p class="text-5xl font-bold text-blue-600 mb-4">{{ number_format($session->nilai, 2) }}</p>
            @else
                <p class="text-2xl font-bold text-gray-400 mb-4">Belum dinilai</p>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div>
                    <label class="text-sm text-gray-500">Status</label>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ ucfirst(str_replace('_', ' ', $session->status)) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Mulai</label>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $session->started_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Selesai</label>
                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $session->finished_at ? $session->finished_at->format('d M Y, H:i') : '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($exam->tampilkan_nilai || $session->nilai !== null)
        <!-- Questions and Answers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Review Jawaban</h2>
            
            <div class="space-y-6">
                @foreach($questions as $index => $question)
                    @php
                        $answer = $session->answers->where('exam_question_id', $question->id)->first();
                    @endphp
                    <div class="p-5 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">Soal {{ $index + 1 }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $question->tipe === 'pilgan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($question->tipe) }}
                                </span>
                            </div>
                            @if($answer && $answer->nilai !== null)
                                <span class="px-3 py-1 text-sm font-medium rounded bg-green-100 text-green-800">
                                    {{ number_format($answer->nilai, 2) }} / {{ $question->bobot }}
                                </span>
                            @endif
                        </div>
                        
                        <p class="text-gray-900 mb-4">{{ $question->pertanyaan }}</p>
                        
                        @if($question->isPilgan())
                            <div class="space-y-2">
                                @foreach($question->pilihan ?? [] as $key => $value)
                                    <div class="p-2 rounded border 
                                        {{ $key === $answer->jawaban_pilgan ? 'border-blue-500 bg-blue-50' : '' }}
                                        {{ $key === $question->jawaban_benar ? 'border-green-500' : '' }}">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-700">{{ $key }}.</span>
                                            <span class="text-gray-900">{{ $value }}</span>
                                            @if($key === $question->jawaban_benar)
                                                <span class="ml-auto text-green-600 font-medium text-sm">✓ Benar</span>
                                            @endif
                                            @if($key === $answer->jawaban_pilgan && $key !== $question->jawaban_benar)
                                                <span class="ml-auto text-red-600 font-medium text-sm">✗ Jawaban Anda</span>
                                            @endif
                                            @if($key === $answer->jawaban_pilgan && $key === $question->jawaban_benar)
                                                <span class="ml-auto text-green-600 font-medium text-sm">✓ Jawaban Anda (Benar)</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Jawaban Anda:</label>
                                <div class="p-3 bg-white rounded border border-gray-300">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $answer->jawaban_essay ?? '-' }}</p>
                                </div>
                                
                                @if($answer && $answer->feedback)
                                    <div class="mt-3 p-3 bg-blue-50 rounded border border-blue-200">
                                        <label class="text-sm font-medium text-blue-900 block mb-1">Feedback:</label>
                                        <p class="text-blue-800 whitespace-pre-wrap">{{ $answer->feedback }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        @if($question->penjelasan && ($exam->tampilkan_nilai || $session->nilai !== null))
                            <div class="mt-4 p-3 bg-yellow-50 rounded border border-yellow-200">
                                <label class="text-sm font-medium text-yellow-900 block mb-1">Penjelasan:</label>
                                <p class="text-yellow-800 text-sm whitespace-pre-wrap">{{ $question->penjelasan }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-4 text-gray-500">Nilai belum tersedia</p>
                <p class="text-sm text-gray-400 mt-2">Nilai akan ditampilkan setelah dinilai oleh dosen</p>
            </div>
        </div>
    @endif
</div>
@endsection

