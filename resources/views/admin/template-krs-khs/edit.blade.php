@extends('layouts.app')

@section('title', 'Edit Template KRS/KHS')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Template KRS/KHS</h1>
        <p class="text-gray-600 mt-1">Edit template Word untuk KRS atau KHS</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.template-krs-khs.update', $templateKrsKh) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Jenis Template (Read Only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Template</label>
                    <input type="text" value="{{ strtoupper($templateKrsKh->jenis) }}" disabled
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                    <p class="mt-1 text-sm text-gray-500">Jenis template tidak dapat diubah</p>
                </div>

                <!-- Nama Template -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Template *</label>
                    <input type="text" name="nama_template" value="{{ old('nama_template', $templateKrsKh->nama_template) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Template KRS 2025">
                    @error('nama_template')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current File -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Template Saat Ini</label>
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $templateKrsKh->nama_template }}</p>
                            <p class="text-xs text-gray-500">{{ basename($templateKrsKh->file_path) }}</p>
                        </div>
                        <a href="{{ route('admin.template-krs-khs.download', $templateKrsKh) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Download
                        </a>
                    </div>
                </div>

                <!-- New File Template (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Baru (Opsional)</label>
                    <input type="file" name="template_file" accept=".doc,.docx"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah file template</p>
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
                              placeholder="Deskripsi template (opsional)">{{ old('deskripsi', $templateKrsKh->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Aktif -->
<div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $templateKrsKh->is_active) ? 'checked' : '' }}
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
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
