@extends('layouts.app')

@section('title', 'Pilih Bank untuk Pembayaran')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Pilih Bank untuk Pembayaran</h1>
        <p class="text-gray-600 mt-1">Pilih bank untuk melakukan pembayaran via Virtual Account</p>
    </div>

    <!-- Payment Info -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">Tipe Pembayaran</p>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment_type ?? 'Pembayaran')) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Jumlah Pembayaran</p>
                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($amount ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        @if($description ?? null)
        <div class="mt-4 pt-4 border-t border-blue-200">
            <p class="text-sm text-gray-500 mb-1">Keterangan</p>
            <p class="text-gray-700 font-medium">{{ $description }}</p>
        </div>
        @endif
        <div class="mt-4 pt-4 border-t border-blue-200">
            <p class="text-xs text-gray-500">* Biaya admin akan ditambahkan saat pembayaran</p>
        </div>
    </div>

    <!-- Bank Selection -->
    <form action="{{ route('payment.store') }}" method="POST">
        @csrf
        <input type="hidden" name="amount" value="{{ $amount }}">
        <input type="hidden" name="payment_type" value="{{ $payment_type }}">
        <input type="hidden" name="description" value="{{ $description ?? '' }}">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Pilih Bank</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($banks as $bank)
                    <label class="relative">
                        <input type="radio" name="bank_id" value="{{ $bank->id }}" 
                               class="peer sr-only" required>
                        <div class="border-2 border-gray-200 rounded-lg p-4 cursor-pointer 
                                    hover:border-blue-500 hover:bg-blue-50 transition-all
                                    peer-checked:border-blue-600 peer-checked:bg-blue-50
                                    peer-checked:ring-2 peer-checked:ring-blue-500">
                            <div class="flex flex-col items-center text-center">
                                @php
                                    $logoPath = $bank->logo ? 'storage/' . $bank->logo : null;
                                    $logoExists = $logoPath && (file_exists(public_path($logoPath)) || file_exists(storage_path('app/public/' . $bank->logo)));
                                @endphp
                                @if($logoExists)
                                    <img src="{{ asset($logoPath) }}" 
                                         alt="{{ $bank->name }}" 
                                         class="w-16 h-16 object-contain mb-2">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mb-2">
                                        <span class="text-gray-500 text-xs font-medium">{{ $bank->code }}</span>
                                    </div>
                                @endif
                                <p class="text-sm font-medium text-gray-900">{{ $bank->name }}</p>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('bank_id')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ url()->previous() }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Kembali
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Lanjutkan Pembayaran
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

