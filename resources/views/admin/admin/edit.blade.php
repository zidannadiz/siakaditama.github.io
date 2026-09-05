@extends('layouts.app')

@section('title', 'Edit Pengguna Admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Pengguna Admin</h1>
        <p class="text-gray-600 mt-1">Ubah data akun admin</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.admin.update', $admin) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    @if($admin->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $admin->role }}">
                        <input type="text" value="{{ $roles[$admin->role] ?? $admin->role }}" disabled
                            class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-lg text-gray-500">
                        <p class="mt-1 text-xs text-gray-500">Anda tidak dapat mengubah role Anda sendiri.</p>
                    @else
                        <select name="role" id="role" required onchange="toggleProdi(this.value)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-500 @enderror">
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ old('role', $admin->role) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                            @if($admin->role === 'admin')
                                <option value="admin" selected>Admin (Legacy)</option>
                            @endif
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                @if($admin->id !== auth()->id())
                <div id="prodi_container" style="display: {{ in_array(old('role', $admin->role), ['kaprodi', 'admin_prodi']) ? 'block' : 'none' }};">
                    <label for="prodi_id" class="block text-sm font-medium text-gray-700 mb-2">Program Studi (Untuk Kaprodi / Admin Prodi)</label>
                    <select name="prodi_id" id="prodi_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('prodi_id') border-red-500 @enderror">
                        <option value="">Pilih Program Studi</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id', $admin->prodi_id) == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                        @endforeach
                    </select>
                    @error('prodi_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <div style="display: {{ in_array($admin->role, ['kaprodi', 'admin_prodi']) ? 'block' : 'none' }};">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                    <input type="hidden" name="prodi_id" value="{{ $admin->prodi_id }}">
                    <input type="text" value="{{ $admin->prodi?->nama_prodi ?? '-' }}" disabled
                        class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-lg text-gray-500">
                </div>
                @endif

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru (Opsional)</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center space-x-4 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Simpan Perubahan
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
        if (container) {
            if (role === 'kaprodi' || role === 'admin_prodi') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }
    }
</script>
@endsection
