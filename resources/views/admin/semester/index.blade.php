@extends('layouts.app')

@section('title', 'Semester')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Semester</h1>
            <p class="text-gray-600 mt-1">Kelola semester akademik</p>
        </div>
        <a href="{{ route('admin.semester.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            + Tambah Semester
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($semesters as $semester)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $semester->nama_semester }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($semester->jenis) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $semester->tahun_ajaran }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $semester->tanggal_mulai->format('d M Y') }} - {{ $semester->tanggal_selesai->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($semester->status === 'aktif') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($semester->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.semester.edit', $semester) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <form action="{{ route('admin.semester.destroy', $semester) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data semester</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $semesters->links() }}
        </div>
    </div>
</div>
@endsection

