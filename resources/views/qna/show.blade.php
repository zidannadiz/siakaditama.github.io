@extends('layouts.app')

@section('title', $question->title)

@section('content')
<div class="space-y-6">
    <!-- Question -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center space-x-2 mb-4">
            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                {{ ucfirst($question->category) }}
            </span>
            <span class="px-2 py-1 text-xs font-medium rounded
                @if($question->status === 'answered') bg-green-100 text-green-800
                @elseif($question->status === 'closed') bg-gray-100 text-gray-800
                @else bg-yellow-100 text-yellow-800
                @endif">
                {{ $question->status === 'answered' ? 'Terjawab' : ($question->status === 'closed' ? 'Tertutup' : 'Terbuka') }}
            </span>
        </div>
        <div class="flex items-start justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-900">{{ $question->title }}</h1>
            <span class="text-sm text-gray-500 font-mono">#{{ $question->id }}</span>
        </div>
        <div class="prose max-w-none mb-4">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $question->content }}</p>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span>Oleh: {{ $question->user->name }}</span>
                <span>•</span>
                <span>{{ $question->created_at->format('d M Y, H:i') }}</span>
                <span>•</span>
                <span>{{ $question->views }} dilihat</span>
            </div>
        </div>
    </div>

    <!-- Answers -->
    <div class="space-y-4">
        <h2 class="text-2xl font-bold text-gray-900">{{ $question->answers_count }} Jawaban</h2>
        
        @forelse($question->answers as $answer)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 {{ $answer->is_best_answer ? 'border-green-500 border-2' : '' }}">
                @if($answer->is_best_answer)
                    <div class="mb-4 flex items-center space-x-2">
                        <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">
                            ✓ Jawaban Terbaik
                        </span>
                    </div>
                @endif
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-600 font-semibold">{{ substr($answer->user->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <h3 class="font-semibold text-gray-900">{{ $answer->user->name }}</h3>
                                <span class="text-xs text-gray-400 font-mono">#{{ $answer->id }}</span>
                            </div>
                            @if($question->user_id === auth()->id() || auth()->user()->role === 'admin')
                                @if(!$answer->is_best_answer && $question->status !== 'closed')
                                    <form action="{{ route('qna.best-answer', [$question, $answer]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded hover:bg-green-200 transition-colors">
                                            Pilih sebagai Jawaban Terbaik
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mb-2">{{ $answer->created_at->format('d M Y, H:i') }}</p>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $answer->content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-500">Belum ada jawaban</p>
            </div>
        @endforelse
    </div>

    <!-- Answer Form -->
    @if($question->status !== 'closed')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Berikan Jawaban</h2>
            <form action="{{ route('qna.answer', $question) }}" method="POST">
                @csrf
                <textarea name="content" rows="8" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Tulis jawaban Anda..." required></textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="mt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Kirim Jawaban
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <p class="text-gray-800">Pertanyaan ini telah ditutup dan tidak dapat dijawab lagi.</p>
        </div>
    @endif
</div>
@endsection

