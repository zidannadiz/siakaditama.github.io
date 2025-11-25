@extends('layouts.app')

@section('title', 'Daftar Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Daftar Pembayaran</h1>
            <p class="text-gray-600 mt-1">Riwayat semua pembayaran Anda</p>
        </div>
        <a href="{{ route('payment.create', ['amount' => 100000, 'payment_type' => 'SPP']) }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Buat Pembayaran Baru
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('payment.index') }}" class="flex items-center space-x-4">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <select name="payment_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Tipe</option>
                <option value="SPP" {{ request('payment_type') === 'SPP' ? 'selected' : '' }}>SPP</option>
                <option value="UKT" {{ request('payment_type') === 'UKT' ? 'selected' : '' }}>UKT</option>
                <option value="BIAYA_PENDAFTARAN" {{ request('payment_type') === 'BIAYA_PENDAFTARAN' ? 'selected' : '' }}>Biaya Pendaftaran</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Payments List -->
    <div class="space-y-4">
        @forelse($payments as $payment)
            <a href="{{ route('payment.show', $payment) }}" 
               class="block bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 flex-1">
                        @php
                            $logoPath = $payment->bank->logo ? 'storage/' . $payment->bank->logo : null;
                            $logoExists = $logoPath && (file_exists(public_path($logoPath)) || file_exists(storage_path('app/public/' . $payment->bank->logo)));
                        @endphp
                        @if($logoExists)
                            <img src="{{ asset($logoPath) }}" 
                                 alt="{{ $payment->bank->name }}" 
                                 class="w-16 h-16 object-contain">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="text-gray-500 text-xs font-medium">{{ $payment->bank->code }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $payment->bank->name }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($payment->status === 'paid') bg-green-100 text-green-800
                                    @elseif($payment->status === 'expired') bg-red-100 text-red-800
                                    @elseif($payment->status === 'cancelled') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $payment->status_badge['label'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</p>
                            <p class="text-xs text-gray-500 mt-1">VA: {{ $payment->virtual_account }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <p class="text-gray-500">Belum ada riwayat pembayaran</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection

