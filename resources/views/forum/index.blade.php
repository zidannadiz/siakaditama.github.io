@extends('layouts.app')

@section('title', 'Forum Diskusi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Forum Diskusi</h1>
            <p class="text-gray-600 mt-1">Diskusikan topik dengan komunitas</p>
        </div>
        <a href="{{ route('forum.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Buat Topik Baru
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('forum.index') }}" class="flex items-center space-x-4">
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                <option value="umum" {{ request('category') === 'umum' ? 'selected' : '' }}>Umum</option>
                <option value="akademik" {{ request('category') === 'akademik' ? 'selected' : '' }}>Akademik</option>
                <option value="organisasi" {{ request('category') === 'organisasi' ? 'selected' : '' }}>Organisasi</option>
                <option value="hobi" {{ request('category') === 'hobi' ? 'selected' : '' }}>Hobi</option>
                <option value="lainnya" {{ request('category') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            <input type="text" name="search" placeholder="Cari topik..." 
                   value="{{ request('search') }}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cari
            </button>
        </form>
    </div>

    <!-- Topics List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        @forelse($topics as $topic)
            <a href="{{ route('forum.show', $topic) }}" class="block p-6 border-b border-gray-200 hover:bg-gray-50 transition-colors">
                <div class="flex items-start space-x-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            @if($topic->is_pinned)
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Pinned</span>
                            @endif
                            @if($topic->is_locked)
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Locked</span>
                            @endif
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                {{ ucfirst($topic->category) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $topic->title }}</h3>
                        @if($topic->description)
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($topic->description, 100) }}</p>
                        @endif
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>Oleh: {{ $topic->creator->name }}</span>
                            <span>•</span>
                            <span>{{ $topic->replies_count }} balasan</span>
                            <span>•</span>
                            <span>{{ $topic->views }} dilihat</span>
                            @if($topic->last_reply_at)
                                <span>•</span>
                                <span>Terakhir: {{ $topic->last_reply_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <p class="text-gray-500">Belum ada topik forum</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $topics->links() }}
    </div>
</div>
@endsection

