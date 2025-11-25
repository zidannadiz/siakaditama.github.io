<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAKAD') - Sistem Informasi Akademik</title>
    <script>
        // Nonaktifkan scroll restoration browser SEBELUM halaman dimuat
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
    </script>
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
                                // Variabel dari View Composer (AppServiceProvider)
                                // Fallback jika View Composer tidak dipanggil
                                if (!isset($unreadCount)) {
                                    $user = auth()->user();
                                    $unreadNotifikasis = $user->notifikasis()->where('is_read', false)->get();
                                    $unreadCount = $unreadNotifikasis->count();
                                    $recentNotifikasis = $unreadNotifikasis->sortByDesc('created_at')->take(5);
                                }
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
                                    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors flex items-center" style="cursor: pointer;">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        @if($unreadCount > 0)
                                            <span class="ml-1.5 rounded-full pointer-events-none" style="flex-shrink: 0; width: 10px; height: 10px; background-color: #ff0000 !important; border: 2px solid white; box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.3);"></span>
                                        @endif
                                    </button>
                                    
                                    <!-- Dropdown -->
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto">
                                        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                            <h3 class="font-semibold text-gray-900">Notifikasi</h3>
                                            @if($unreadCount > 0)
                                                <form action="{{ route('notifikasi.read-all') }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800" style="cursor: pointer;">Tandai semua dibaca</button>
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
                                
                                <form method="POST" action="{{ route('logout') }}" class="ml-2">
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

        <div class="flex-1 flex overflow-hidden">
            <!-- Sidebar -->
            @auth
                <div class="flex-shrink-0 overflow-y-auto" style="height: calc(100vh - 64px);">
                    @include('layouts.sidebar')
                </div>
            @endauth

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto" style="height: calc(100vh - 64px);">
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
        // Nonaktifkan scroll restoration default browser
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        
        // Mencegah scroll kembali ke atas saat navigasi
        (function() {
            // Simpan posisi scroll sidebar sebelum navigasi
            // Cari elemen yang bisa di-scroll (parent div dengan overflow-y-auto)
            const sidebarContainer = document.querySelector('div.flex-shrink-0.overflow-y-auto');
            if (sidebarContainer) {
                // Simpan posisi scroll sidebar saat scroll (menggunakan localStorage)
                let sidebarScrollTimeout;
                sidebarContainer.addEventListener('scroll', function() {
                    clearTimeout(sidebarScrollTimeout);
                    sidebarScrollTimeout = setTimeout(function() {
                        const scrollPos = sidebarContainer.scrollTop;
                        // Selalu simpan posisi scroll
                        localStorage.setItem('sidebarScrollPos', scrollPos.toString());
                        sessionStorage.setItem('sidebarScrollPos', scrollPos.toString());
                    }, 50);
                }, { passive: true });
                
                // Simpan posisi scroll secara berkala (backup method) - simpan SEMUA posisi
                setInterval(function() {
                    const scrollPos = sidebarContainer.scrollTop;
                    // Simpan semua posisi, termasuk 0 (jika user scroll kembali ke atas)
                    localStorage.setItem('sidebarScrollPos', scrollPos.toString());
                }, 500); // Simpan setiap 500ms
                
                // Simpan posisi scroll sidebar saat klik link di sidebar
                // Gunakan capture phase untuk menangkap klik SEBELUM default behavior
                document.addEventListener('click', function(e) {
                    const link = e.target.closest('a[href]');
                    if (link && sidebarContainer && sidebarContainer.contains(link)) {
                        if (link.href && !link.href.includes('#') && !link.href.includes('javascript:')) {
                            // Simpan posisi scroll sidebar saat ini (langsung, tidak pakai timeout)
                            const scrollPos = sidebarContainer.scrollTop;
                            localStorage.setItem('sidebarScrollPos', scrollPos.toString());
                            sessionStorage.setItem('sidebarScrollPos', scrollPos.toString());
                            // Jangan prevent default, biarkan navigasi normal terjadi
                        }
                    }
                }, true);
                
                // Flag untuk mencegah restore berulang
                let isRestored = false;
                
                // Pulihkan posisi scroll sidebar setelah halaman dimuat (hanya sekali)
                function restoreSidebarScroll() {
                    if (isRestored) return; // Jangan restore lagi jika sudah berhasil
                    
                    const savedSidebarScroll = localStorage.getItem('sidebarScrollPos');
                    if (savedSidebarScroll !== null && savedSidebarScroll !== '') {
                        const scrollPos = parseInt(savedSidebarScroll, 10);
                        if (!isNaN(scrollPos) && sidebarContainer.scrollTop !== scrollPos) {
                            sidebarContainer.scrollTop = scrollPos;
                            isRestored = true; // Tandai sudah restore
                            return true;
                        }
                    }
                    return false;
                }
                
                // Restore hanya sekali setelah halaman siap
                function attemptRestore() {
                    if (restoreSidebarScroll()) {
                        return; // Berhasil restore, stop
                    }
                    // Coba lagi setelah delay jika belum berhasil
                    if (!isRestored) {
                        setTimeout(attemptRestore, 100);
                    }
                }
                
                // Mulai restore setelah DOM ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(attemptRestore, 50);
                    });
                } else {
                    setTimeout(attemptRestore, 50);
                }
                
                // Simpan posisi scroll sebelum unload/pagehide
                window.addEventListener('beforeunload', function() {
                    const scrollPos = sidebarContainer.scrollTop;
                    localStorage.setItem('sidebarScrollPos', scrollPos.toString());
                });
                
                window.addEventListener('pagehide', function() {
                    const scrollPos = sidebarContainer.scrollTop;
                    localStorage.setItem('sidebarScrollPos', scrollPos.toString());
                });
            }
            
            // Cegah window scroll ke atas
            let preventScroll = false;
            window.addEventListener('beforeunload', function() {
                preventScroll = true;
            });
            
            window.addEventListener('scroll', function() {
                if (preventScroll && window.scrollY === 0) {
                    // Jika scroll kembali ke 0, kembalikan ke posisi sebelumnya
                    const savedMainScroll = sessionStorage.getItem('mainContentScroll');
                    if (savedMainScroll && parseInt(savedMainScroll, 10) > 0) {
                        window.scrollTo(0, parseInt(savedMainScroll, 10));
                    }
                }
            }, { passive: false });
            
            // Simpan posisi scroll main content
            const mainContent = document.querySelector('main.flex-1.overflow-y-auto');
            if (mainContent) {
                let mainScrollTimeout;
                mainContent.addEventListener('scroll', function() {
                    clearTimeout(mainScrollTimeout);
                    mainScrollTimeout = setTimeout(function() {
                        sessionStorage.setItem('mainContentScroll', mainContent.scrollTop.toString());
                    }, 50);
                });
                
                // Simpan sebelum unload
                window.addEventListener('beforeunload', function() {
                    sessionStorage.setItem('mainContentScroll', mainContent.scrollTop.toString());
                });
                
                // Pulihkan posisi scroll main content
                function restoreMainContentScroll() {
                    const savedMainScroll = sessionStorage.getItem('mainContentScroll');
                    if (savedMainScroll !== null && parseInt(savedMainScroll, 10) > 0) {
                        setTimeout(function() {
                            mainContent.scrollTop = parseInt(savedMainScroll, 10);
                        }, 100);
                    }
                }
                
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', restoreMainContentScroll);
                } else {
                    restoreMainContentScroll();
                }
            }
        })();
    </script>
    
    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="display: none; z-index: 99999; position: fixed;">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-all relative" style="z-index: 100000;" onclick="event.stopPropagation()">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2" id="confirmTitle">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-600 text-center mb-6" id="confirmMessage">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeConfirmModal()" class="flex-1 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors font-medium" style="background-color: #e5e7eb !important; color: #374151 !important; border: none !important; cursor: pointer;">
                        Batal
                    </button>
                    <button type="button" id="confirmButton" onclick="executeConfirm()" class="flex-1 px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium" style="background-color: #dc2626 !important; color: #ffffff !important; border: none !important; cursor: pointer;">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            let confirmForm = null;

            function showConfirmModal(title, message, form) {
                const titleEl = document.getElementById('confirmTitle');
                const messageEl = document.getElementById('confirmMessage');
                const modal = document.getElementById('confirmModal');
                
                if (!modal) {
                    console.error('Modal not found!');
                    return false;
                }
                
                if (titleEl) titleEl.textContent = title || 'Konfirmasi Hapus';
                if (messageEl) messageEl.textContent = message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.';
                
                confirmForm = form;
                
                modal.style.display = 'flex';
                modal.style.zIndex = '99999';
                modal.style.position = 'fixed';
                document.body.style.overflow = 'hidden';
                
                console.log('Modal should be visible now');
                return true;
            }

            function closeConfirmModal() {
                const modal = document.getElementById('confirmModal');
                if (modal) {
                    modal.style.display = 'none';
                }
                document.body.style.overflow = '';
                confirmForm = null;
            }

            function executeConfirm() {
                if (confirmForm) {
                    confirmForm.submit();
                }
                closeConfirmModal();
            }

            // Make functions global
            window.showConfirmModal = showConfirmModal;
            window.closeConfirmModal = closeConfirmModal;
            window.executeConfirm = executeConfirm;
            window.confirmForm = function() { return confirmForm; };
        })();

        // Close modal when clicking outside - wait for modal to exist
        setTimeout(function() {
            const modal = document.getElementById('confirmModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeConfirmModal();
                    }
                });
            }
        }, 100);

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirmModal();
            }
        });

        // Function to confirm delete
        function confirmDelete(formId, message) {
            const form = document.getElementById(formId);
            if (!form) {
                console.error('Form not found:', formId);
                return;
            }
            showConfirmModal('Konfirmasi Hapus', message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.', form);
        }
        
        // Make it global
        window.confirmDelete = confirmDelete;

        // Initialize delete buttons - Simple and direct approach
        function initDeleteButtons() {
            // Handle forms with class delete-form
            document.querySelectorAll('form.delete-form .delete-btn').forEach(function(button) {
                // Remove existing listeners to avoid duplicates
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                newButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const form = newButton.closest('form');
                    if (!form) {
                        console.error('Form not found');
                        return;
                    }
                    
                    const message = form.getAttribute('data-message') || 'Apakah Anda yakin ingin menghapus data ini?';
                    
                    console.log('Delete button clicked, showing modal...');
                    if (window.showConfirmModal) {
                        const result = window.showConfirmModal('Konfirmasi Hapus', message, form);
                        if (!result) {
                            console.error('Failed to show modal');
                        }
                    } else {
                        console.error('showConfirmModal function not found');
                    }
                });
            });
            
            // Handle forms with onsubmit containing confirm
            document.querySelectorAll('form[onsubmit*="confirm"]').forEach(function(form) {
                if (form.classList.contains('delete-form')) return; // Skip already handled
                
                const onsubmit = form.getAttribute('onsubmit');
                if (onsubmit && onsubmit.includes('confirm')) {
                    // Extract message from confirm
                    const match = onsubmit.match(/confirm\(['"]([^'"]+)['"]\)/);
                    const message = match ? match[1] : 'Apakah Anda yakin ingin menghapus data ini?';
                    
                    // Remove onsubmit attribute
                    form.removeAttribute('onsubmit');
                    
                    // Find submit button and change to button type
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.type = 'button';
                        submitButton.classList.add('delete-btn');
                        form.classList.add('delete-form');
                        form.setAttribute('data-message', message);
                        
                        submitButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            if (window.showConfirmModal) {
                                window.showConfirmModal('Konfirmasi Hapus', message, form);
                            }
                        });
                    }
                }
            });
        }

        // Initialize multiple times to catch dynamic content
        function runInit() {
            initDeleteButtons();
        }

        // Run on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runInit);
        } else {
            runInit();
        }
        
        // Also run after a short delay to catch any late-loading content
        setTimeout(runInit, 500);

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
        // Update token setiap kali halaman di-load untuk handle multiple tabs
        function updateCsrfTokens() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                // Update semua form dengan token terbaru
                document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function(form) {
                    let csrfInput = form.querySelector('input[name="_token"]');
                    if (csrfInput) {
                        csrfInput.value = csrfToken.content;
                    } else {
                        // Jika tidak ada, tambahkan
                        csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.content;
                        form.appendChild(csrfInput);
                    }
                });
            }
        }

        // Update token saat DOM ready
        document.addEventListener('DOMContentLoaded', updateCsrfTokens);
        
        // Update token saat visibility change (tab menjadi aktif)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateCsrfTokens();
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

        // Session expiration handler - DISABLED
        // Session tidak akan expired meskipun tidak ada aktivitas atau beralih tab
        // User hanya akan logout jika melakukan logout manual
        (function() {
            // Cek apakah user sudah login (ada route session.check yang memerlukan auth)
            // Jika tidak bisa akses route ini, berarti user belum login atau di halaman login
            function checkIfUserLoggedIn() {
                return fetch('{{ route("session.check") }}', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                })
                .then(response => {
                    // Jika response OK, berarti user sudah login
                    return response.ok;
                })
                .catch(() => {
                    // Jika error, berarti user belum login atau di halaman login
                    return false;
                });
            }
            
            // Update CSRF token saat tab menjadi aktif (untuk handle multiple tabs)
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    updateCsrfTokens();
                }
            });
            
            // Tidak ada session expiration handler yang aktif
            // Session akan tetap valid sampai user logout manual atau browser ditutup
        })();
    </script>
</body>
</html>

