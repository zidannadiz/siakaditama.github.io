@extends('layouts.app')

@section('title', 'Tambah Semester')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Tambah Semester</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.semester.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama_semester" class="block text-sm font-medium text-gray-700 mb-2">Nama Semester *</label>
                    <input type="text" id="nama_semester" name="nama_semester" value="{{ old('nama_semester') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: Semester Ganjil 2024/2025">
                    @error('nama_semester') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis *</label>
                    <select id="jenis" name="jenis" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="ganjil" {{ old('jenis') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ old('jenis') == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                    @error('jenis') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran *</label>
                    <input type="number" id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran', date('Y')) }}" min="2020" max="2100" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('tahun_ajaran') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai *</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('tanggal_mulai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai *</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('tanggal_selesai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    </select>
                    @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.semester.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

