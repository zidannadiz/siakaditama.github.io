@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pembayaran</h1>
            <p class="text-gray-600 mt-1">Invoice: {{ $payment->invoice_number }}</p>
        </div>
        <a href="{{ route('admin.payment.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            Kembali
        </a>
    </div>

    <!-- Payment Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
            @if($payment->status === 'pending')
                <form action="{{ route('admin.payment.verify', $payment) }}" method="POST" 
                      onsubmit="return confirm('Verifikasi pembayaran ini?');">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Verifikasi Pembayaran
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Virtual Account -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-8 text-white">
        <div class="text-center">
            <p class="text-sm opacity-90 mb-2">Nomor Virtual Account</p>
            <p class="text-3xl font-bold font-mono mb-4 tracking-wider">{{ $payment->virtual_account }}</p>
            <div class="flex items-center justify-center space-x-2">
                @if($payment->bank->logo && file_exists(public_path('storage/' . $payment->bank->logo)))
                    <img src="{{ asset('storage/' . $payment->bank->logo) }}" 
                         alt="{{ $payment->bank->name }}" 
                         class="w-8 h-8 object-contain bg-white rounded p-1">
                @endif
                <p class="text-sm opacity-90">{{ $payment->bank->name }}</p>
            </div>
            @if($payment->xendit_id)
                <p class="text-xs opacity-75 mt-2">Xendit ID: {{ $payment->xendit_id }}</p>
            @endif
        </div>
    </div>

    <!-- Payment Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- User Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi User</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <p class="text-gray-600">Nama</p>
                    <p class="font-medium text-gray-900">{{ $payment->user->name }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="text-gray-600">Email</p>
                    <p class="font-medium text-gray-900">{{ $payment->user->email }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="text-gray-600">Role</p>
                    <p class="font-medium text-gray-900">{{ ucfirst($payment->user->role) }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Pembayaran</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <p class="text-gray-600">Jumlah Pembayaran</p>
                    <p class="font-semibold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="text-gray-600">Biaya Admin</p>
                    <p class="font-semibold text-gray-900">Rp {{ number_format($payment->fee, 0, ',', '.') }}</p>
                </div>
                <div class="border-t border-gray-200 pt-3 flex justify-between">
                    <p class="text-lg font-semibold text-gray-900">Total Pembayaran</p>
                    <p class="text-lg font-bold text-blue-600">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</p>
                </div>
                <div class="flex justify-between mt-3">
                    <p class="text-gray-600">Tipe Pembayaran</p>
                    <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</p>
                </div>
                @if($payment->description)
                <div class="flex justify-between">
                    <p class="text-gray-600">Keterangan</p>
                    <p class="font-medium text-gray-900">{{ $payment->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Timeline -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Timeline</h2>
        <div class="space-y-3">
            <div class="flex items-center space-x-3">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Pembayaran Dibuat</p>
                    <p class="text-xs text-gray-500">{{ $payment->created_at->format('d M Y, H:i:s') }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Batas Waktu Pembayaran</p>
                    <p class="text-xs text-gray-500">{{ $payment->expired_at->format('d M Y, H:i:s') }}</p>
                </div>
            </div>
            @if($payment->paid_at)
            <div class="flex items-center space-x-3">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Waktu Pembayaran</p>
                    <p class="text-xs text-gray-500">{{ $payment->paid_at->format('d M Y, H:i:s') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Admin Notes -->
    @if($payment->notes || $payment->status === 'pending')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Catatan Admin</h2>
        @if($payment->notes)
            <p class="text-gray-700">{{ $payment->notes }}</p>
        @else
            <p class="text-gray-500 italic">Belum ada catatan</p>
        @endif
        
        @if($payment->status === 'pending')
        <form action="{{ route('admin.payment.verify', $payment) }}" method="POST" class="mt-4">
            @csrf
            <textarea name="notes" rows="3" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                      placeholder="Tambahkan catatan (opsional)"></textarea>
            <button type="submit" class="mt-2 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Verifikasi dengan Catatan
            </button>
        </form>
        @endif
    </div>
    @endif
</div>
@endsection

