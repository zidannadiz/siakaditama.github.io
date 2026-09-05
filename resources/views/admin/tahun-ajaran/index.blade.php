@extends('layouts.app')

@section('title', 'Tahun Ajaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tahun Ajaran</h1>
            <p class="text-gray-600 mt-1">Kelola daftar tahun ajaran sistem</p>
        </div>
        <a href="{{ route('admin.tahun-ajaran.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            + Tambah Tahun Ajaran
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tahunAjarans as $ta)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $ta->nama }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ta->tahun_mulai }} / {{ $ta->tahun_selesai }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ta->tanggal_mulai?->format('d M Y') ?? '-' }} <br> s/d <br> {{ $ta->tanggal_selesai?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ta->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($ta->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.tahun-ajaran.show', $ta) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                            <a href="{{ route('admin.tahun-ajaran.edit', $ta) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.tahun-ajaran.destroy', $ta) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Tidak ada data tahun ajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tahunAjarans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tahunAjarans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
