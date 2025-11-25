@extends('layouts.app')

@section('title', 'Edit Bank')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Bank</h1>
        <p class="text-gray-600 mt-1">Update informasi bank</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.bank.update', $bank) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Current Logo -->
                @php
                    $logoPath = $bank->logo ? 'storage/' . $bank->logo : null;
                    $logoExists = $logoPath && (file_exists(public_path($logoPath)) || file_exists(storage_path('app/public/' . $bank->logo)));
                @endphp
                @if($logoExists)
                <div class="flex items-center space-x-4">
                    <img src="{{ asset($logoPath) }}" 
                         alt="{{ $bank->name }}" 
                         class="w-24 h-24 object-contain border border-gray-200 rounded-lg p-2">
                    <div>
                        <p class="text-sm text-gray-600">Logo Saat Ini</p>
                        <p class="text-xs text-gray-500">{{ $bank->logo }}</p>
                    </div>
                </div>
                @endif

                <!-- Bank Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank</label>
                    <input type="text" name="name" value="{{ old('name', $bank->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Bank</label>
                    <input type="text" name="code" value="{{ old('code', $bank->code) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required maxlength="10">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Logo Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Bank</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, SVG. Maksimal 2MB</p>
                    @error('logo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           {{ old('is_active', $bank->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                        Bank Aktif
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.bank.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

