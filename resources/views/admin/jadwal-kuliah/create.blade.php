@extends('layouts.app')

@section('title', 'Tambah Jadwal Kuliah')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Tambah Jadwal Kuliah</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.jadwal-kuliah.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="mata_kuliah_id" class="block text-sm font-medium text-gray-700 mb-2">Mata Kuliah *</label>
                    <select id="mata_kuliah_id" name="mata_kuliah_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Mata Kuliah</option>
                        @foreach($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>
                                {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                            </option>
                        @endforeach
                    </select>
                    @error('mata_kuliah_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="dosen_id" class="block text-sm font-medium text-gray-700 mb-2">Dosen *</label>
                    <select id="dosen_id" name="dosen_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Dosen</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->id }}" {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                {{ $dosen->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('dosen_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="semester_id" class="block text-sm font-medium text-gray-700 mb-2">Semester *</label>
                    <select id="semester_id" name="semester_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Semester</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->nama_semester }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="hari" class="block text-sm font-medium text-gray-700 mb-2">Hari *</label>
                    <select id="hari" name="hari" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Pilih Hari</option>
                        <option value="Senin" {{ old('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                        <option value="Selasa" {{ old('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                        <option value="Rabu" {{ old('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                        <option value="Kamis" {{ old('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                        <option value="Jumat" {{ old('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                        <option value="Sabtu" {{ old('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                        <option value="Minggu" {{ old('hari') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                    </select>
                    @error('hari') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai *</label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('jam_mulai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai *</label>
                    <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('jam_selesai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ruangan" class="block text-sm font-medium text-gray-700 mb-2">Ruangan</label>
                    <input type="text" id="ruangan" name="ruangan" value="{{ old('ruangan') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="kuota" class="block text-sm font-medium text-gray-700 mb-2">Kuota *</label>
                    <input type="number" id="kuota" name="kuota" value="{{ old('kuota', 30) }}" min="1" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('kuota') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.jadwal-kuliah.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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

