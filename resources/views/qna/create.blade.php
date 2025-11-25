@extends('layouts.app')

@section('title', 'Ajukan Pertanyaan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Ajukan Pertanyaan</h1>
        <p class="text-gray-600 mt-1">Dapatkan bantuan dari komunitas</p>
    </div>

    <form action="{{ route('qna.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        @csrf
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Pertanyaan</label>
            <input type="text" name="title" value="{{ old('title') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   required maxlength="255" placeholder="Contoh: Bagaimana cara mengajukan KRS?">
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                <option value="akademik" {{ old('category') === 'akademik' ? 'selected' : '' }}>Akademik</option>
                <option value="administrasi" {{ old('category') === 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                <option value="teknologi" {{ old('category') === 'teknologi' ? 'selected' : '' }}>Teknologi</option>
                <option value="umum" {{ old('category') === 'umum' ? 'selected' : '' }}>Umum</option>
            </select>
            @error('category')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Detail Pertanyaan</label>
            <textarea name="content" rows="10" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Jelaskan pertanyaan Anda secara detail..."
                      required>{{ old('content') }}</textarea>
            @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center space-x-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Ajukan Pertanyaan
            </button>
            <a href="{{ route('qna.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

