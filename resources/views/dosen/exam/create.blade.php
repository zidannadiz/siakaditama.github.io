@extends('layouts.app')

@section('title', 'Buat Ujian')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Buat Ujian</h1>
        <p class="text-gray-600 mt-1">{{ $jadwal->mataKuliah->nama_mk }} - {{ $jadwal->semester->nama_semester }}</p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('dosen.exam.store') }}" method="POST">
            @csrf
            <input type="hidden" name="jadwal_kuliah_id" value="{{ $jadwal->id }}">

            <div class="space-y-6">
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">Judul Ujian *</label>
                    <input type="text" 
                           id="judul" 
                           name="judul" 
                           value="{{ old('judul') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tipe" class="block text-sm font-medium text-gray-700 mb-2">Tipe Ujian *</label>
                        <select id="tipe" 
                                name="tipe"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="pilgan" {{ old('tipe') === 'pilgan' ? 'selected' : '' }}>Pilihan Ganda</option>
                            <option value="essay" {{ old('tipe') === 'essay' ? 'selected' : '' }}>Essay</option>
                            <option value="campuran" {{ old('tipe') === 'campuran' ? 'selected' : '' }}>Campuran</option>
                        </select>
                        @error('tipe')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="durasi" class="block text-sm font-medium text-gray-700 mb-2">Durasi (menit) *</label>
                        <input type="number" 
                               id="durasi" 
                               name="durasi" 
                               value="{{ old('durasi', 60) }}"
                               min="1" 
                               max="600"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('durasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="mulai" class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai (Opsional)</label>
                        <input type="datetime-local" 
                               id="mulai" 
                               name="mulai" 
                               value="{{ old('mulai') }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika ujian bisa dimulai kapan saja</p>
                        @error('mulai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="selesai" class="block text-sm font-medium text-gray-700 mb-2">Waktu Selesai *</label>
                        <input type="datetime-local" 
                               id="selesai" 
                               name="selesai" 
                               value="{{ old('selesai') }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bobot" class="block text-sm font-medium text-gray-700 mb-2">Bobot Nilai (%) *</label>
                        <input type="number" 
                               id="bobot" 
                               name="bobot" 
                               value="{{ old('bobot', 0) }}"
                               min="0" 
                               max="100"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('bobot')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select id="status" 
                                name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Security Features -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Keamanan (Anti-Cheat)</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700 block">
                                    Cegah Copy/Paste
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Mencegah mahasiswa copy/paste saat mengerjakan ujian</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer ml-4">
                                <input type="checkbox" 
                                       id="prevent_copy_paste" 
                                       name="prevent_copy_paste" 
                                       value="1"
                                       {{ old('prevent_copy_paste', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700 block">
                                    Cegah Membuka Tab Baru
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Mendeteksi dan mencatat jika mahasiswa membuka tab/window lain</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer ml-4">
                                <input type="checkbox" 
                                       id="prevent_new_tab" 
                                       name="prevent_new_tab" 
                                       value="1"
                                       {{ old('prevent_new_tab', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700 block">
                                    Mode Fullscreen Wajib
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Memaksa mahasiswa menggunakan mode fullscreen saat ujian</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer ml-4">
                                <input type="checkbox" 
                                       id="fullscreen_mode" 
                                       name="fullscreen_mode" 
                                       value="1"
                                       {{ old('fullscreen_mode', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Exam Settings -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Ujian</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700 block">
                                    Acak Urutan Soal
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Urutan soal akan diacak untuk setiap mahasiswa</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer ml-4">
                                <input type="checkbox" 
                                       id="random_soal" 
                                       name="random_soal" 
                                       value="1"
                                       {{ old('random_soal', false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700 block">
                                    Acak Pilihan Jawaban (Pilgan)
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Pilihan jawaban akan diacak untuk soal pilihan ganda</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer ml-4">
                                <input type="checkbox" 
                                       id="random_pilihan" 
                                       name="random_pilihan" 
                                       value="1"
                                       {{ old('random_pilihan', false) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-700 block">
                                    Tampilkan Nilai Setelah Selesai
                                </span>
                                <p class="text-xs text-gray-500 mt-1">Mahasiswa dapat melihat nilai setelah menyelesaikan ujian</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer ml-4">
                                <input type="checkbox" 
                                       id="tampilkan_nilai" 
                                       name="tampilkan_nilai" 
                                       value="1"
                                       {{ old('tampilkan_nilai', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('dosen.exam.index', ['jadwal_id' => $jadwal->id]) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium cursor-pointer">
                        Simpan & Tambahkan Soal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

