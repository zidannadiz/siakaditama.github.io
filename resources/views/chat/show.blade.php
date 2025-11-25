@extends('layouts.app')

@section('title', 'Chat')

@section('content')
<div class="flex flex-col h-[calc(100vh-8rem)]">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('chat.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-semibold">{{ substr($otherUser->name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $otherUser->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $otherUser->email }}</p>
                </div>
            </div>
            <span class="px-2 py-1 text-xs font-medium rounded
                @if($otherUser->role === 'admin') bg-purple-100 text-purple-800
                @elseif($otherUser->role === 'dosen') bg-blue-100 text-blue-800
                @else bg-green-100 text-green-800
                @endif">
                {{ ucfirst($otherUser->role) }}
            </span>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md">
                        <div class="flex items-start space-x-2 {{ $message->sender_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-600 text-xs font-semibold">{{ substr($message->sender->name, 0, 1) }}</span>
                            </div>
                            <div class="{{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg px-4 py-2">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm flex-1">{{ $message->message }}</p>
                                    <span class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-400' }} font-mono">
                                        #{{ $message->id }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }}">
                                        {{ $message->created_at->format('H:i') }}
                                    </p>
                                    @if($message->sender_id === auth()->id())
                                        <span class="text-xs {{ $message->getStatusColor() }} font-semibold ml-2">
                                            {{ $message->getStatusIcon() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada pesan. Mulai percakapan dengan mengirim pesan di bawah.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mx-4 mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Message Form -->
        <form id="messageForm" action="{{ route('chat.message', $conversation) }}" method="POST" class="p-4 border-t border-gray-200">
            @csrf
            <div class="flex space-x-2">
                <input type="text" id="messageInput" name="message" placeholder="Ketik pesan..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       required maxlength="1000" autocomplete="off">
                <button type="submit" id="sendButton" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Kirim
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto scroll to bottom
    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    }
    
    scrollToBottom();

    // Handle form submission
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        
        // Disable button and input while sending
        if (messageInput.value.trim() === '') {
            e.preventDefault();
            return;
        }
        
        sendButton.disabled = true;
        sendButton.textContent = 'Mengirim...';
    });

    // Auto scroll when page loads or after message sent
    window.addEventListener('load', scrollToBottom);
    
    // Scroll to bottom after a short delay to ensure content is loaded
    setTimeout(scrollToBottom, 100);
</script>
@endpush
@endsection

