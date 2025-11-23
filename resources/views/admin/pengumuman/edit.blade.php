@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Pengumuman</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.pengumuman.update', $pengumuman) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul *</label>
                <input type="text" id="judul" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('judul') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="isi" class="block text-sm font-medium text-gray-700 mb-2">Isi *</label>
                <textarea id="isi" name="isi" rows="6" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('isi', $pengumuman->isi) }}</textarea>
                @error('isi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select id="kategori" name="kategori" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="umum" {{ old('kategori', $pengumuman->kategori) == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="akademik" {{ old('kategori', $pengumuman->kategori) == 'akademik' ? 'selected' : '' }}>Akademik</option>
                        <option value="beasiswa" {{ old('kategori', $pengumuman->kategori) == 'beasiswa' ? 'selected' : '' }}>Beasiswa</option>
                        <option value="kegiatan" {{ old('kategori', $pengumuman->kategori) == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                    </select>
                    @error('kategori') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="target" class="block text-sm font-medium text-gray-700 mb-2">Target *</label>
                    <select id="target" name="target" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="semua" {{ old('target', $pengumuman->target) == 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="mahasiswa" {{ old('target', $pengumuman->target) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('target', $pengumuman->target) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="admin" {{ old('target', $pengumuman->target) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('target') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Publikasi</label>
                    <input type="datetime-local" id="published_at" name="published_at" 
                           value="{{ old('published_at', $pengumuman->published_at?->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Pengumuman akan muncul pada tanggal yang dipilih atau setelahnya. Kosongkan untuk publikasi langsung.</p>
                    @if($pengumuman->published_at && $pengumuman->published_at->isFuture())
                        <p class="mt-1 text-xs text-yellow-600 font-medium">⚠️ Pengumuman ini akan muncul pada {{ $pengumuman->published_at->format('d M Y H:i') }}</p>
                    @elseif($pengumuman->published_at)
                        <p class="mt-1 text-xs text-green-600 font-medium">✓ Pengumuman sudah dipublikasikan sejak {{ $pengumuman->published_at->format('d M Y H:i') }}</p>
                    @else
                        <p class="mt-1 text-xs text-blue-600 font-medium">ℹ️ Pengumuman dipublikasikan langsung</p>
                    @endif
                </div>

                <div class="flex items-center pt-8">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $pengumuman->is_pinned) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Pin pengumuman</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.pengumuman.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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

