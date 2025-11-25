@extends('layouts.app')

@section('title', 'QR Code Presensi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">QR Code Presensi</h1>
        <p class="text-gray-600 mt-1">{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }} - Pertemuan {{ $qrSession->pertemuan }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- QR Code Display -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">QR Code</h2>
            
            <div class="flex flex-col items-center space-y-4">
                @php
                    $qrUrl = route('qr-presensi.public-scan', $qrSession->token);
                    $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrUrl) . '&bgcolor=ffffff&color=000000&margin=1';
                @endphp
                <div id="qrcode" class="p-4 bg-white border-2 border-gray-200 rounded-lg">
                    <img src="{{ $qrImageUrl }}" 
                         alt="QR Code Presensi" 
                         class="mx-auto max-w-full h-auto"
                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27300%27 height=%27300%27%3E%3Ctext x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dominant-baseline=%27middle%27%3EGagal memuat QR Code%3C/text%3E%3C/svg%3E'; loadQRCodeFallback();"
                         id="qr-image">
                </div>
                
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-2">Scan QR code ini untuk presensi</p>
                    <p class="text-xs text-gray-500">Token: <span class="font-mono">{{ substr($qrSession->token, 0, 20) }}...</span></p>
                </div>
                
                <div class="w-full space-y-2">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Berlaku hingga:</span>
                        <span class="text-sm font-semibold text-blue-600" id="expires-time">{{ $qrSession->expires_at->format('H:i:s') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Sisa waktu:</span>
                        <span class="text-sm font-semibold text-green-600" id="countdown">--:--</span>
                    </div>
                </div>
                
                <form action="{{ route('dosen.qr-presensi.stop', $qrSession->token) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Apakah Anda yakin ingin menghentikan QR code ini?')"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        Hentikan QR Code
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Jadwal -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Jadwal</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Mata Kuliah</label>
                    <p class="text-gray-900 font-semibold">{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">{{ $jadwal->mataKuliah->kode_mk ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Pertemuan</label>
                    <p class="text-gray-900 font-semibold">Pertemuan ke-{{ $qrSession->pertemuan }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Tanggal</label>
                    <p class="text-gray-900 font-semibold">{{ $qrSession->tanggal->format('d F Y') }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Semester</label>
                    <p class="text-gray-900 font-semibold">{{ $jadwal->semester->nama_semester ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Ruangan</label>
                    <p class="text-gray-900 font-semibold">{{ $jadwal->ruangan ?? 'TBA' }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-500">Waktu</label>
                    <p class="text-gray-900 font-semibold">{{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Link Presensi</h3>
                <p class="text-sm text-gray-600 mt-1">Salin link ini untuk dibagikan kepada mahasiswa</p>
            </div>
        </div>
        <div class="mt-4 flex items-center space-x-2">
            <input type="text" 
                   id="qr-url" 
                   value="{{ route('qr-presensi.public-scan', $qrSession->token) }}" 
                   readonly
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm">
            <button onclick="copyToClipboard()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                Salin
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrToken = '{{ $qrSession->token }}';
        const scanUrl = '{{ route("qr-presensi.public-scan", $qrSession->token) }}';
        const expiresAt = new Date('{{ $qrSession->expires_at->toIso8601String() }}').getTime();
        
        const qrcodeElement = document.getElementById('qrcode');
        
        if (!qrcodeElement) {
            console.error('QR code element not found');
            return;
        }

        // Fallback method menggunakan qrcode.js library jika gambar tidak ter-load
        function loadQRCodeFallback() {
            console.log('Loading QR code fallback using qrcode.js library...');
            // Load library dynamically
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
            script.onload = function() {
                if (typeof QRCode !== 'undefined') {
                    // Clear existing content
                    qrcodeElement.innerHTML = '';
                    
                    QRCode.toCanvas(qrcodeElement, scanUrl, {
                        width: 300,
                        margin: 2,
                        color: {
                            dark: '#000000',
                            light: '#FFFFFF'
                        },
                        errorCorrectionLevel: 'M'
                    }, function (error) {
                        if (error) {
                            console.error('QR Code generation error:', error);
                            showError();
                        } else {
                            console.log('QR Code generated using fallback method');
                            const canvas = qrcodeElement.querySelector('canvas');
                            if (canvas) {
                                canvas.style.maxWidth = '100%';
                                canvas.style.height = 'auto';
                                canvas.className = 'mx-auto';
                            }
                        }
                    });
                } else {
                    showError();
                }
            };
            script.onerror = function() {
                console.error('Failed to load qrcode.js library');
                showError();
            };
            document.head.appendChild(script);
        }
        
        // Check if QR image loaded successfully after 2 seconds
        setTimeout(function() {
            const qrImage = document.getElementById('qr-image');
            if (qrImage && (!qrImage.complete || qrImage.naturalHeight === 0)) {
                console.log('QR image did not load, trying fallback...');
                loadQRCodeFallback();
            }
        }, 2000);

        function showError() {
            qrcodeElement.innerHTML = `
                <div class="p-4 text-center">
                    <p class="text-red-500 mb-2 font-semibold">Gagal memuat QR code</p>
                    <p class="text-xs text-gray-600 mb-3 break-all">URL: ${scanUrl.substring(0, 60)}...</p>
                    <p class="text-xs text-gray-500 mb-3">Token: ${qrToken.substring(0, 30)}...</p>
                    <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Refresh Halaman
                    </button>
                </div>
            `;
        }

        // QR code already rendered as image in HTML
        // Fallback will be triggered automatically if image fails to load

        // Countdown timer
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = expiresAt - now;

            const countdownElement = document.getElementById('countdown');
            if (!countdownElement) return;

            if (distance < 0) {
                countdownElement.textContent = 'Kedaluwarsa';
                countdownElement.classList.remove('text-green-600');
                countdownElement.classList.add('text-red-600');
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.textContent = 
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }

        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown();

        // Copy to clipboard function
        window.copyToClipboard = function() {
            const urlInput = document.getElementById('qr-url');
            if (!urlInput) return;
            
            urlInput.select();
            urlInput.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                document.execCommand('copy');
                
                // Show feedback
                const button = event.target;
                if (button) {
                    const originalText = button.textContent;
                    button.textContent = 'Tersalin!';
                    button.classList.add('bg-green-600');
                    button.classList.remove('bg-blue-600');
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-blue-600');
                    }, 2000);
                }
            } catch (err) {
                console.error('Failed to copy:', err);
                alert('Gagal menyalin link. Silakan salin manual.');
            }
        };

        // Auto-refresh status setiap 10 detik
        setInterval(function() {
            fetch('{{ route("dosen.qr-presensi.status", $qrSession->token) }}')
                .then(response => response.json())
                .then(data => {
                    if (!data.is_active || data.expires_in_seconds <= 0) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 10000);
    });
</script>
@endsection
