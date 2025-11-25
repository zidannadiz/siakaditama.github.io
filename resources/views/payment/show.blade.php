@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Detail Pembayaran</h1>
        <p class="text-gray-600 mt-1">Lakukan pembayaran melalui Virtual Account</p>
    </div>

    <!-- Payment Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="inline-block px-3 py-1 text-sm font-medium rounded-full mt-1
                    @if($payment->status === 'paid') bg-green-100 text-green-800
                    @elseif($payment->status === 'expired') bg-red-100 text-red-800
                    @elseif($payment->status === 'cancelled') bg-gray-100 text-gray-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    {{ $payment->status_badge['label'] }}
                </span>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Invoice Number</p>
                <p class="text-lg font-mono font-semibold">{{ $payment->invoice_number }}</p>
            </div>
        </div>
    </div>

    <!-- Virtual Account -->
    @if($payment->status === 'pending')
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-8 mb-6 text-white">
        <div class="text-center">
            <p class="text-sm opacity-90 mb-2">Nomor Virtual Account</p>
            <p class="text-3xl font-bold font-mono mb-4 tracking-wider">{{ $payment->virtual_account }}</p>
            <p class="text-sm opacity-90 mb-4">{{ $payment->bank->name }}</p>
            
            <!-- Copy Button -->
            <button onclick="copyVA()" class="px-6 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                Salin Nomor VA
            </button>
        </div>
    </div>
    @endif

    <!-- Payment Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Pembayaran</h2>
        
        <div class="space-y-4">
            <div class="flex justify-between">
                <p class="text-gray-600">Jumlah Pembayaran</p>
                <p class="font-semibold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
            </div>
            <div class="flex justify-between">
                <p class="text-gray-600">Biaya Admin</p>
                <p class="font-semibold text-gray-900">Rp {{ number_format($payment->fee, 0, ',', '.') }}</p>
            </div>
            <div class="border-t border-gray-200 pt-4 flex justify-between">
                <p class="text-lg font-semibold text-gray-900">Total Pembayaran</p>
                <p class="text-lg font-bold text-blue-600">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Payment Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi</h2>
        
        <div class="space-y-3">
            <div class="flex justify-between">
                <p class="text-gray-600">Tipe Pembayaran</p>
                <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</p>
            </div>
            @if($payment->description)
            <div class="flex justify-between">
                <p class="text-gray-600">Keterangan</p>
                <p class="font-medium text-gray-900">{{ $payment->description }}</p>
            </div>
            @endif
            <div class="flex justify-between">
                <p class="text-gray-600">Batas Waktu</p>
                <p class="font-medium text-gray-900">{{ $payment->expired_at->format('d M Y, H:i') }}</p>
            </div>
            @if($payment->paid_at)
            <div class="flex justify-between">
                <p class="text-gray-600">Waktu Pembayaran</p>
                <p class="font-medium text-gray-900">{{ $payment->paid_at->format('d M Y, H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Instructions -->
    @if($payment->status === 'pending')
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
        <h3 class="font-semibold text-blue-900 mb-3">Cara Membayar</h3>
        <ol class="list-decimal list-inside space-y-2 text-blue-800">
            <li>Salin nomor Virtual Account di atas</li>
            <li>Buka aplikasi atau website {{ $payment->bank->name }}</li>
            <li>Pilih menu "Transfer" atau "Bayar Tagihan"</li>
            <li>Pilih "Virtual Account" atau "VA"</li>
            <li>Masukkan nomor Virtual Account yang sudah disalin</li>
            <li>Konfirmasi dan lakukan pembayaran</li>
            <li>Tunggu beberapa saat, status pembayaran akan otomatis terupdate</li>
        </ol>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('payment.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            Kembali ke Daftar Pembayaran
        </a>
        
        @if($payment->status === 'pending')
            <form action="{{ route('payment.cancel', $payment) }}" method="POST" class="inline" 
                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pembayaran ini?');">
                @csrf
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Batalkan Pembayaran
                </button>
            </form>
        @endif
    </div>
</div>

<script>
function copyVA() {
    const va = '{{ $payment->virtual_account }}';
    navigator.clipboard.writeText(va).then(function() {
        alert('Nomor Virtual Account berhasil disalin!');
    }, function(err) {
        // Fallback untuk browser lama
        const textArea = document.createElement('textarea');
        textArea.value = va;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Nomor Virtual Account berhasil disalin!');
    });
}

// Auto refresh jika masih pending (polling setiap 30 detik)
@if($payment->status === 'pending')
setTimeout(function() {
    location.reload();
}, 30000);
@endif
</script>
@endsection

