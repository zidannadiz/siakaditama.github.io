@extends('layouts.app')

@section('title', 'Scan QR Code Presensi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Scan QR Code Presensi</h1>
        <p class="text-gray-600 mt-1">Arahkan kamera ke QR code yang ditampilkan dosen</p>
    </div>

    <div id="alert-container"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Scanner Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Scanner QR Code</h2>
            
            <div class="space-y-4">
                <div id="reader" class="w-full border-2 border-gray-200 rounded-lg overflow-hidden"></div>
                
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-2">Atau masukkan token secara manual</p>
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               id="manual-token" 
                               placeholder="Masukkan token QR code"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button onclick="scanManualToken()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Submit
                        </button>
                    </div>
                </div>
                
                <button id="start-scan-btn" 
                        onclick="startScan()" 
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Mulai Scan
                </button>
                
                <button id="stop-scan-btn" 
                        onclick="stopScan()" 
                        style="display: none;"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Hentikan Scan
                </button>
            </div>
        </div>

        <!-- Info Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Panduan</h2>
            
            <div class="space-y-3">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-semibold text-sm">1</span>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">Pastikan QR code terlihat jelas</p>
                        <p class="text-sm text-gray-600">Arahkan kamera ke QR code yang ditampilkan dosen di layar</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-semibold text-sm">2</span>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">Berikan izin akses kamera</p>
                        <p class="text-sm text-gray-600">Browser akan meminta izin untuk menggunakan kamera</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-semibold text-sm">3</span>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">Tunggu konfirmasi</p>
                        <p class="text-sm text-gray-600">Sistem akan memproses presensi Anda secara otomatis</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <strong>Catatan:</strong> Pastikan Anda sudah terdaftar di kelas tersebut dan QR code masih aktif.
                </p>
            </div>

            <div class="mt-4">
                <a href="{{ route('mahasiswa.qr-presensi.history') }}" 
                   class="block text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    Lihat Riwayat Presensi
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;
    let isScanning = false;

    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alert-container');
        const alertClass = type === 'success' 
            ? 'bg-green-50 border-green-200 text-green-800' 
            : 'bg-red-50 border-red-200 text-red-800';
        
        alertContainer.innerHTML = `
            <div class="${alertClass} border px-4 py-3 rounded-lg mb-4">
                <p>${message}</p>
            </div>
        `;
        
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
    }

    function startScan() {
        if (isScanning) return;

        const readerElement = document.getElementById('reader');
        
        html5QrcodeScanner = new Html5Qrcode("reader");
        
        html5QrcodeScanner.start(
            { facingMode: "environment" }, // Gunakan kamera belakang
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanError
        ).then(() => {
            isScanning = true;
            document.getElementById('start-scan-btn').style.display = 'none';
            document.getElementById('stop-scan-btn').style.display = 'block';
        }).catch((err) => {
            showAlert('Gagal memulai scanner. Pastikan browser mendukung akses kamera.', 'error');
            console.error('Error starting scanner:', err);
        });
    }

    function stopScan() {
        if (!isScanning || !html5QrcodeScanner) return;

        html5QrcodeScanner.stop().then(() => {
            isScanning = false;
            document.getElementById('start-scan-btn').style.display = 'block';
            document.getElementById('stop-scan-btn').style.display = 'none';
        }).catch((err) => {
            console.error('Error stopping scanner:', err);
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Extract token from URL or use directly if it's a token
        let token = decodedText;
        
        // If it's a URL, extract token parameter
        if (decodedText.includes('token=')) {
            const urlParams = new URLSearchParams(decodedText.split('?')[1]);
            token = urlParams.get('token');
        }
        
        if (token) {
            stopScan();
            processPresensi(token);
        }
    }

    function onScanError(errorMessage) {
        // Ignore scanning errors (too frequent)
    }

    function scanManualToken() {
        const tokenInput = document.getElementById('manual-token');
        const token = tokenInput.value.trim();
        
        if (!token) {
            showAlert('Silakan masukkan token QR code', 'error');
            return;
        }
        
        processPresensi(token);
        tokenInput.value = '';
    }

    function processPresensi(token) {
        showAlert('Memproses presensi...', 'success');
        
        fetch('{{ route("mahasiswa.qr-presensi.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(`Presensi berhasil! Pertemuan ${data.data.pertemuan} pada ${data.data.tanggal}`, 'success');
                
                // Redirect to history after 2 seconds
                setTimeout(() => {
                    window.location.href = '{{ route("mahasiswa.qr-presensi.history") }}';
                }, 2000);
            } else {
                showAlert(data.message || 'Presensi gagal. Silakan coba lagi.', 'error');
                // Restart scan if failed
                setTimeout(() => {
                    if (!isScanning) {
                        startScan();
                    }
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan. Silakan coba lagi.', 'error');
        });
    }

    // Check if user came from public scan link
    // Note: Public scan link will automatically process presensi, so no need to handle token here
    // This is kept for backward compatibility if someone uses old format
    window.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        
        if (token) {
            // If token exists in URL, redirect to public scan route
            window.location.href = '{{ route("qr-presensi.public-scan", ":token") }}'.replace(':token', token);
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (isScanning && html5QrcodeScanner) {
            html5QrcodeScanner.stop().catch(() => {});
        }
    });
</script>
@endsection
