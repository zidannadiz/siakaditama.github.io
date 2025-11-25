@extends('layouts.app')

@section('title', 'Pesan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Pesan</h1>
            <p class="text-gray-600 mt-1">Daftar percakapan Anda</p>
        </div>
        <a href="{{ route('chat.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Pesan Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        @forelse($conversations as $conversation)
            @php
                $otherUser = $conversation->other_user;
                $unreadCount = $conversation->unreadCount();
                $latestMessage = $conversation->latestMessage;
            @endphp
            <a href="{{ route('chat.show', $conversation) }}" class="block p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-semibold">{{ substr($otherUser->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">{{ $otherUser->name }}</h3>
                            @if($latestMessage)
                                <span class="text-xs text-gray-500">{{ $latestMessage->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        @if($latestMessage)
                            <p class="text-sm text-gray-600 mt-1 truncate">
                                {{ $latestMessage->sender_id === auth()->id() ? 'Anda: ' : '' }}{{ Str::limit($latestMessage->message, 50) }}
                            </p>
                        @endif
                    </div>
                    @if($unreadCount > 0)
                        <div class="flex-shrink-0">
                            <span class="bg-blue-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-gray-500">Belum ada percakapan</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

