@extends('layouts.app')

@section('title', 'Edit Mata Kuliah')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Mata Kuliah</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.mata-kuliah.update', $mataKuliah) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kode_mk" class="block text-sm font-medium text-gray-700 mb-2">Kode Mata Kuliah *</label>
                    <input type="text" id="kode_mk" name="kode_mk" value="{{ old('kode_mk', $mataKuliah->kode_mk) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('kode_mk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama_mk" class="block text-sm font-medium text-gray-700 mb-2">Nama Mata Kuliah *</label>
                    <input type="text" id="nama_mk" name="nama_mk" value="{{ old('nama_mk', $mataKuliah->nama_mk) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nama_mk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="prodi_id" class="block text-sm font-medium text-gray-700 mb-2">Program Studi *</label>
                    <select id="prodi_id" name="prodi_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Prodi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id', $mataKuliah->prodi_id) == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama_prodi }}
                            </option>
                        @endforeach
                    </select>
                    @error('prodi_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sks" class="block text-sm font-medium text-gray-700 mb-2">SKS *</label>
                    <input type="number" id="sks" name="sks" value="{{ old('sks', $mataKuliah->sks) }}" min="1" max="6" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('sks') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                    <input type="number" id="semester" name="semester" value="{{ old('semester', $mataKuliah->semester) }}" min="1" max="14" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('semester') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis *</label>
                    <select id="jenis" name="jenis" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="wajib" {{ old('jenis', $mataKuliah->jenis) == 'wajib' ? 'selected' : '' }}>Wajib</option>
                        <option value="pilihan" {{ old('jenis', $mataKuliah->jenis) == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
                    </select>
                    @error('jenis') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('deskripsi', $mataKuliah->deskripsi) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.mata-kuliah.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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

