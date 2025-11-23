@extends('layouts.app')

@section('title', 'Mata Kuliah')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mata Kuliah</h1>
            <p class="text-gray-600 mt-1">Kelola mata kuliah</p>
        </div>
        <a href="{{ route('admin.mata-kuliah.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            + Tambah Mata Kuliah
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($mataKuliahs as $mk)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mk->kode_mk }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $mk->nama_mk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mk->prodi->nama_prodi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->sks }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->semester }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.mata-kuliah.edit', $mk) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <form action="{{ route('admin.mata-kuliah.destroy', $mk) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data mata kuliah</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $mataKuliahs->links() }}
        </div>
    </div>
</div>
@endsection

