@extends('layouts.app')

@section('title', 'Upload Template KRS/KHS')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Upload Template KRS/KHS</h1>
        <p class="text-gray-600 mt-1">Upload template Word untuk KRS atau KHS</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.template-krs-khs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <!-- Jenis Template -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Template *</label>
                    <select name="jenis" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Jenis</option>
                        <option value="krs" {{ old('jenis') === 'krs' ? 'selected' : '' }}>KRS (Kartu Rencana Studi)</option>
                        <option value="khs" {{ old('jenis') === 'khs' ? 'selected' : '' }}>KHS (Kartu Hasil Studi)</option>
                    </select>
                    @error('jenis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Template -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Template *</label>
                    <input type="text" name="nama_template" value="{{ old('nama_template') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Template KRS 2025">
                    @error('nama_template')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Template -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Template (Word) *</label>
                    <input type="file" name="template_file" accept=".doc,.docx" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Format: .doc atau .docx (Maks: 5MB)</p>
                    <p class="mt-2 text-sm text-blue-600">
                        <strong>Catatan:</strong> Gunakan placeholder berikut di template Word:<br>
                        <strong>KRS:</strong> {NIM}, {NAMA}, {PROGRAM_STUDI}, {TANGGAL_CETAK}, {TAHUN_AKADEMIK}, {NO}, {KODE_MK}, {NAMA_MK}, {SKS}, {SEMESTER}<br>
                        <strong>KHS:</strong> {NIM}, {NAMA}, {PROGRAM_STUDI}, {TANGGAL_CETAK}, {TAHUN_AKADEMIK}, {IP}, {TOTAL_SKS}, {NO}, {KODE_MK}, {NAMA_MK}, {SKS}, {NILAI}, {HURUF}, {BOBOT}, {NILAI_X_SKS}, {DOSEN}
                    </p>
                    @error('template_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Deskripsi template (opsional)">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Aktif -->
<div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Aktifkan template ini (nonaktifkan template aktif lainnya dengan jenis yang sama)</span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                    <a href="{{ route('admin.template-krs-khs.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Upload Template
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
