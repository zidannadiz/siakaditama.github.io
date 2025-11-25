@extends('layouts.app')

@section('title', $forumTopic->title)

@section('content')
<div class="space-y-6">
    <!-- Topic Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center space-x-2 mb-4">
            @if($forumTopic->is_pinned)
                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Pinned</span>
            @endif
            @if($forumTopic->is_locked)
                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Locked</span>
            @endif
            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                {{ ucfirst($forumTopic->category) }}
            </span>
        </div>
        <div class="flex items-start justify-between mb-2">
            <h1 class="text-3xl font-bold text-gray-900">{{ $forumTopic->title }}</h1>
            <span class="text-sm text-gray-500 font-mono">#{{ $forumTopic->id }}</span>
        </div>
        @if($forumTopic->description)
            <p class="text-gray-600 mb-4">{{ $forumTopic->description }}</p>
        @endif
        <div class="flex items-center space-x-4 text-sm text-gray-500">
            <span>Oleh: {{ $forumTopic->creator->name }}</span>
            <span>•</span>
            <span>{{ $forumTopic->created_at->format('d M Y, H:i') }}</span>
            <span>•</span>
            <span>{{ $forumTopic->views }} dilihat</span>
            <span>•</span>
            <span>{{ $forumTopic->replies_count }} balasan</span>
        </div>
    </div>

    <!-- Posts -->
    <div class="space-y-4">
        @foreach($forumTopic->posts as $post)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-blue-600 font-semibold">{{ substr($post->user->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <h3 class="font-semibold text-gray-900">{{ $post->user->name }}</h3>
                                <span class="text-xs text-gray-400 font-mono">#{{ $post->id }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($post->is_first_post)
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">OP</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">{{ $post->created_at->format('d M Y, H:i') }}</p>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $post->content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Reply Form -->
    @if(!$forumTopic->is_locked)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Balas Topik</h2>
            <form action="{{ route('forum.reply', $forumTopic) }}" method="POST">
                @csrf
                <textarea name="content" rows="6" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Tulis balasan Anda..." required></textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="mt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Kirim Balasan
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-red-800">Topik ini terkunci dan tidak dapat dibalas.</p>
        </div>
    @endif
</div>
@endsection

