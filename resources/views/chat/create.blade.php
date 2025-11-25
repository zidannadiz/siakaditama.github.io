@extends('layouts.app')

@section('title', 'Pesan Baru')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Pesan Baru</h1>
        <p class="text-gray-600 mt-1">Pilih pengguna untuk memulai percakapan</p>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <input type="text" id="searchInput" placeholder="Cari pengguna..." 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>

    <!-- Users List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        @forelse($users as $user)
            <div class="user-item p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer"
                 onclick="startConversation({{ $user->id }}, '{{ $user->name }}')">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <span class="inline-block mt-1 px-2 py-1 text-xs font-medium rounded
                            @if($user->role === 'admin') bg-purple-100 text-purple-800
                            @elseif($user->role === 'dosen') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <p class="text-gray-500">Tidak ada pengguna lain</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal untuk mengirim pesan pertama -->
<div id="messageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Kirim Pesan ke <span id="recipientName"></span></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="messageForm" method="POST" action="{{ route('chat.store') }}">
                @csrf
                <input type="hidden" name="receiver_id" id="receiverId">
                <div class="mb-4">
                    <textarea name="message" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Ketik pesan Anda..." required maxlength="1000"></textarea>
                </div>
                <div class="flex items-center space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Kirim
                    </button>
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const userItems = document.querySelectorAll('.user-item');
        
        userItems.forEach(item => {
            const userName = item.querySelector('h3').textContent.toLowerCase();
            const userEmail = item.querySelector('p').textContent.toLowerCase();
            
            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Start conversation
    function startConversation(userId, userName) {
        document.getElementById('receiverId').value = userId;
        document.getElementById('recipientName').textContent = userName;
        document.getElementById('messageModal').classList.remove('hidden');
        document.getElementById('messageForm').querySelector('textarea').focus();
    }

    // Close modal
    function closeModal() {
        document.getElementById('messageModal').classList.add('hidden');
        document.getElementById('messageForm').reset();
    }

    // Close modal on outside click
    document.getElementById('messageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush
@endsection

