@extends('layouts.app')

@section('title', 'Tambah Pengguna Admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Tambah Pengguna Admin</h1>
        <p class="text-gray-600 mt-1">Buat akun admin baru dengan spesifikasi role</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.admin.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="contoh@email.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="role" required onchange="toggleProdi(this.value)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="prodi_container" style="display: none;">
                    <label for="prodi_id" class="block text-sm font-medium text-gray-700 mb-2">Program Studi (Untuk Kaprodi / Admin Prodi)</label>
                    <select name="prodi_id" id="prodi_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('prodi_id') border-red-500 @enderror">
                        <option value="">Pilih Program Studi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                    @error('prodi_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Minimal 8 karakter</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center space-x-4 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Simpan
                </button>
                <a href="{{ route('admin.admin.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleProdi(role) {
        const container = document.getElementById('prodi_container');
        if (role === 'kaprodi' || role === 'admin_prodi') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }
    
    // Run on load in case of validation errors returning old input
    document.addEventListener('DOMContentLoaded', function() {
        toggleProdi(document.getElementById('role').value);
    });
</script>
@endsection
