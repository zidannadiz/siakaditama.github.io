@extends('layouts.app')

@section('title', 'Kurikulum')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kurikulum</h1>
            <p class="text-gray-600 mt-1">Kelola data kurikulum program studi</p>
        </div>
        <a href="{{ route('admin.kurikulum.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            + Tambah Kurikulum
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kurikulum</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program Studi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kurikulums as $k)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $k->nama }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $k->prodi->nama_prodi }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $k->tahun }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $k->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($k->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.kurikulum.show', $k) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail & Mata Kuliah</a>
                            <a href="{{ route('admin.kurikulum.edit', $k) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.kurikulum.destroy', $k) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kurikulum ini beserta detail mata kuliahnya?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Tidak ada data kurikulum.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kurikulums->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $kurikulums->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
