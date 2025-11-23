<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAKAD') - Sistem Informasi Akademik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-gray-900">SIAKAD</span>
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                            @php
                                $unreadCount = auth()->user()->notifikasis()->where('is_read', false)->count();
                                $recentNotifikasis = auth()->user()->notifikasis()->where('is_read', false)->orderBy('created_at', 'desc')->limit(5)->get();
                            @endphp
                            
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('profile.show') }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors" style="cursor: pointer;">{{ auth()->user()->name }}</a>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if(auth()->user()->role === 'admin') bg-purple-100 text-purple-800
                                    @elseif(auth()->user()->role === 'dosen') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                                
                                <!-- Notifikasi Dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        @if($unreadCount > 0)
                                            <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-bold">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                        @endif
                                    </button>
                                    
                                    <!-- Dropdown -->
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto">
                                        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                            <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                                            @if($unreadCount > 0)
                                                <form action="{{ route('notifikasi.read-all') }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">Tandai semua dibaca</button>
                                                </form>
                                            @endif
                                        </div>
                                        
                                        <div class="divide-y divide-gray-200">
                                            @forelse($recentNotifikasis as $notifikasi)
                                                <a href="{{ $notifikasi->link ?? route('notifikasi.index') }}" 
                                                   class="block p-4 hover:bg-gray-50 transition-colors {{ !$notifikasi->is_read ? 'bg-blue-50' : '' }}"
                                                   onclick="markAsRead({{ $notifikasi->id }})">
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0">
                                                            @if($notifikasi->tipe === 'success')
                                                                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                                            @elseif($notifikasi->tipe === 'error')
                                                                <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                                                            @elseif($notifikasi->tipe === 'warning')
                                                                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                                            @else
                                                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900">{{ $notifikasi->judul }}</p>
                                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $notifikasi->pesan }}</p>
                                                            <p class="text-xs text-gray-400 mt-1">{{ $notifikasi->created_at->diffForHumans() }}</p>
                                                        </div>
                                                        @if(!$notifikasi->is_read)
                                                            <div class="flex-shrink-0">
                                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </a>
                                            @empty
                                                <div class="p-4 text-center text-gray-500 text-sm">
                                                    Tidak ada notifikasi
                                                </div>
                                            @endforelse
                                        </div>
                                        
                                        @if($recentNotifikasis->count() > 0)
                                            <div class="p-3 border-t border-gray-200 text-center">
                                                <a href="{{ route('notifikasi.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                    Lihat semua notifikasi
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900" style="cursor: pointer;">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex-1 flex">
            <!-- Sidebar -->
            @auth
                @include('layouts.sidebar')
            @endauth

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @if(session('success'))
                            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                                <span>{{ session('success') }}</span>
                                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                                <span>{{ session('error') }}</span>
                                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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

        // Pastikan semua form POST memiliki CSRF token yang valid
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                // Update semua form dengan token terbaru
                document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function(form) {
                    let csrfInput = form.querySelector('input[name="_token"]');
                    if (csrfInput) {
                        csrfInput.value = csrfToken.content;
                    }
                });
            }
        });

        // Handle form submission - pastikan token selalu fresh
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.tagName === 'FORM' && (form.method.toUpperCase() === 'POST' || form.method === '')) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    let csrfInput = form.querySelector('input[name="_token"]');
                    if (!csrfInput) {
                        csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        form.appendChild(csrfInput);
                    }
                    csrfInput.value = csrfToken.content;
                }
            }
        });
    </script>
</body>
</html>

