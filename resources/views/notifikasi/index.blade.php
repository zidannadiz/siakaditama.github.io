@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifikasi</h1>
            <p class="text-gray-600 mt-1">Semua notifikasi Anda</p>
        </div>
        @if($notifikasis->where('is_read', false)->count() > 0)
            <form action="{{ route('notifikasi.read-all') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse($notifikasis as $notifikasi)
            <a href="{{ $notifikasi->link ?? '#' }}" 
               class="block p-6 border-b border-gray-200 hover:bg-gray-50 transition-colors {{ !$notifikasi->is_read ? 'bg-blue-50' : '' }}"
               onclick="markAsRead({{ $notifikasi->id }})">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        @if($notifikasi->tipe === 'success')
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        @elseif($notifikasi->tipe === 'error')
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        @elseif($notifikasi->tipe === 'warning')
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        @else
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $notifikasi->judul }}</h3>
                            @if(!$notifikasi->is_read)
                                <span class="px-2 py-1 text-xs font-medium bg-blue-500 text-white rounded-full">Baru</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $notifikasi->pesan }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $notifikasi->created_at->format('d M Y H:i') }} ({{ $notifikasi->created_at->diffForHumans() }})</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada notifikasi</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum memiliki notifikasi.</p>
            </div>
        @endforelse
    </div>

    @if($notifikasis->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notifikasis->links() }}
        </div>
    @endif
</div>

<script>
    function markAsRead(notifikasiId) {
        fetch(`/notifikasi/${notifikasiId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    }
</script>
@endsection

