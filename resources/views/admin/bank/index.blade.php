@extends('layouts.app')

@section('title', 'Kelola Bank')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kelola Bank</h1>
            <p class="text-gray-600 mt-1">Kelola bank untuk pembayaran Virtual Account</p>
        </div>
    </div>

    <!-- Banks Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($banks as $bank)
            <div class="bg-white rounded-xl shadow-sm border-2 {{ $bank->is_active ? 'border-green-200' : 'border-gray-200 opacity-60' }} p-6 hover:shadow-md transition-shadow">
                <div class="flex flex-col items-center text-center mb-4">
                    @php
                        $logoPath = $bank->logo ? 'storage/' . $bank->logo : null;
                        $logoExists = $logoPath && (file_exists(public_path($logoPath)) || file_exists(storage_path('app/public/' . $bank->logo)));
                    @endphp
                    @if($logoExists)
                        <img src="{{ asset($logoPath) }}" 
                             alt="{{ $bank->name }}" 
                             class="w-16 h-16 object-contain mb-3">
                    @else
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mb-3">
                            <span class="text-gray-500 text-xs font-medium">{{ $bank->code }}</span>
                        </div>
                    @endif
                    <h3 class="font-semibold text-gray-900">{{ $bank->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Code: {{ $bank->code }}</p>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $bank->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $bank->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                    <a href="{{ route('admin.bank.edit', $bank) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Edit
                    </a>
                </div>

                <form action="{{ route('admin.bank.toggle-status', $bank) }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full px-3 py-1 text-xs font-medium rounded-lg transition-colors
                        {{ $bank->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                        {{ $bank->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection

