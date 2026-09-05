@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tambah Tahun Ajaran</h1>
            <p class="text-gray-600 mt-1">Tambahkan tahun ajaran baru ke sistem</p>
        </div>
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Tahun Ajaran</label>
                    <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Genap 2024/2025" value="{{ old('nama') }}" required>
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tahun_mulai" class="block text-sm font-medium text-gray-700">Tahun Mulai</label>
                        <input type="number" name="tahun_mulai" id="tahun_mulai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="2024" value="{{ old('tahun_mulai') }}" required>
                        @error('tahun_mulai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tahun_selesai" class="block text-sm font-medium text-gray-700">Tahun Selesai</label>
                        <input type="number" name="tahun_selesai" id="tahun_selesai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="2025" value="{{ old('tahun_selesai') }}" required>
                        @error('tahun_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai (Opsional)</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('tanggal_mulai') }}">
                    @error('tanggal_mulai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai (Opsional)</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('tanggal_selesai') }}">
                    @error('tanggal_selesai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif (Tahun ajaran aktif saat ini)</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Jika diset Aktif, tahun ajaran lain akan otomatis dinonaktifkan.</p>
                </div>
                
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-medium">
                    Simpan Tahun Ajaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
