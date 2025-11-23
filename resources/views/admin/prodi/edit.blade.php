@extends('layouts.app')

@section('title', 'Edit Prodi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Program Studi</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.prodi.update', $prodi) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kode_prodi" class="block text-sm font-medium text-gray-700 mb-2">Kode Prodi *</label>
                    <input type="text" id="kode_prodi" name="kode_prodi" value="{{ old('kode_prodi', $prodi->kode_prodi) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('kode_prodi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama_prodi" class="block text-sm font-medium text-gray-700 mb-2">Nama Prodi *</label>
                    <input type="text" id="nama_prodi" name="nama_prodi" value="{{ old('nama_prodi', $prodi->nama_prodi) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nama_prodi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('deskripsi', $prodi->deskripsi) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.prodi.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

