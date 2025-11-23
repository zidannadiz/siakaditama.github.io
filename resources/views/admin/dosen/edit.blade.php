@extends('layouts.app')

@section('title', 'Edit Dosen')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Dosen</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.dosen.update', $dosen) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nidn" class="block text-sm font-medium text-gray-700 mb-2">NIDN *</label>
                    <input type="text" id="nidn" name="nidn" value="{{ old('nidn', $dosen->nidn) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nidn') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $dosen->nama) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nama') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $dosen->user->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" id="password" name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih</option>
                        <option value="L" {{ old('jenis_kelamin', $dosen->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $dosen->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $dosen->no_hp) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="aktif" {{ old('status', $dosen->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $dosen->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('alamat', $dosen->alamat) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.dosen.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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

