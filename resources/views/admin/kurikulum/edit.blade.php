@extends('layouts.app')

@section('title', 'Edit Kurikulum')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Kurikulum</h1>
            <p class="text-gray-600 mt-1">Ubah data kurikulum program studi</p>
        </div>
        <a href="{{ route('admin.kurikulum.index') }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.kurikulum.update', $kurikulum) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Kurikulum</label>
                    <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('nama', $kurikulum->nama) }}" required>
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="prodi_id" class="block text-sm font-medium text-gray-700">Program Studi</label>
                    @if(auth()->user()->isAdminProdi())
                        <select name="prodi_id" id="prodi_id" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" selected>{{ $prodi->nama_prodi }}</option>
                            @endforeach
                        </select>
                    @else
                        <select name="prodi_id" id="prodi_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Pilih Program Studi</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('prodi_id', $kurikulum->prodi_id) == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('prodi_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                    <input type="number" name="tahun" id="tahun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ old('tahun', $kurikulum->tahun) }}" required>
                    @error('tahun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="aktif" {{ old('status', $kurikulum->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $kurikulum->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan', $kurikulum->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
