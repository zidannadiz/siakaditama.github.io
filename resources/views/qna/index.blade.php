@extends('layouts.app')

@section('title', 'Tanya Jawab')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tanya Jawab</h1>
            <p class="text-gray-600 mt-1">Ajukan pertanyaan dan dapatkan jawaban</p>
        </div>
        <a href="{{ route('qna.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Ajukan Pertanyaan
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('qna.index') }}" class="flex items-center space-x-4">
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                <option value="akademik" {{ request('category') === 'akademik' ? 'selected' : '' }}>Akademik</option>
                <option value="administrasi" {{ request('category') === 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                <option value="teknologi" {{ request('category') === 'teknologi' ? 'selected' : '' }}>Teknologi</option>
                <option value="umum" {{ request('category') === 'umum' ? 'selected' : '' }}>Umum</option>
            </select>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Terbuka</option>
                <option value="answered" {{ request('status') === 'answered' ? 'selected' : '' }}>Terjawab</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Tertutup</option>
            </select>
            <input type="text" name="search" placeholder="Cari pertanyaan..." 
                   value="{{ request('search') }}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cari
            </button>
        </form>
    </div>

    <!-- Questions List -->
    <div class="space-y-4">
        @forelse($questions as $question)
            <a href="{{ route('qna.show', $question) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $question->answers_count }}</div>
                        <div class="text-xs text-gray-500">Jawaban</div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $question->title }}</h3>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($question->content, 150) }}</p>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>Oleh: {{ $question->user->name }}</span>
                            <span>•</span>
                            <span>{{ $question->views }} dilihat</span>
                            <span>•</span>
                            <span>{{ $question->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-500">Belum ada pertanyaan</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $questions->links() }}
    </div>
</div>
@endsection

