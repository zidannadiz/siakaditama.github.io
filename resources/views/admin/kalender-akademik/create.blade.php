@extends('layouts.app')

@section('title', 'Tambah Event Kalender Akademik')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Tambah Event Kalender Akademik</h1>
        <p class="text-gray-600 mt-1">Tambahkan event atau deadline akademik baru</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.kalender-akademik.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Event *</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Event *</label>
                    <select name="jenis" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Jenis</option>
                        <option value="semester" {{ old('jenis') === 'semester' ? 'selected' : '' }}>Semester</option>
                        <option value="krs" {{ old('jenis') === 'krs' ? 'selected' : '' }}>KRS</option>
                        <option value="pembayaran" {{ old('jenis') === 'pembayaran' ? 'selected' : '' }}>Pembayaran</option>
                        <option value="ujian" {{ old('jenis') === 'ujian' ? 'selected' : '' }}>Ujian</option>
                        <option value="libur" {{ old('jenis') === 'libur' ? 'selected' : '' }}>Libur</option>
                        <option value="kegiatan" {{ old('jenis') === 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                        <option value="pengumuman" {{ old('jenis') === 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                        <option value="lainnya" {{ old('jenis') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai *</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('tanggal_mulai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('tanggal_selesai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai (Opsional)</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('jam_mulai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai (Opsional)</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('jam_selesai')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Role *</label>
                    <select name="target_role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="semua" {{ old('target_role') === 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="admin" {{ old('target_role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dosen" {{ old('target_role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="mahasiswa" {{ old('target_role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                    @error('target_role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Semester (Opsional)</label>
                    <select name="semester_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Semester</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->nama_semester ?? $semester->jenis }} - {{ $semester->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Event</label>
                    <input type="color" name="warna" value="{{ old('warna', '#3B82F6') }}"
                           class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer">
                    @error('warna')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Link (Opsional)</label>
                    <input type="url" name="link" value="{{ old('link') }}" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_important" value="1" id="is_important" {{ old('is_important') ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_important" class="ml-2 text-sm font-medium text-gray-700">
                    Tandai sebagai Event Penting (Deadline, dll)
                </label>
            </div>

            <div class="flex gap-2">
                <button type="submit" 
                        class="px-6 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
                        style="background-color: #3B82F6 !important;">
                    Simpan
                </button>
                <a href="{{ route('admin.kalender-akademik.index') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

