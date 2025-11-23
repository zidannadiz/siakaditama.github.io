@extends('layouts.app')

@section('title', 'Jadwal Kuliah')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Jadwal Kuliah</h1>
            <p class="text-gray-600 mt-1">Kelola jadwal kuliah</p>
        </div>
        <a href="{{ route('admin.jadwal-kuliah.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            + Tambah Jadwal
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuota</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jadwalKuliahs as $jadwal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $jadwal->mataKuliah->nama_mk }}</div>
                                <div class="text-sm text-gray-500">{{ $jadwal->mataKuliah->kode_mk }} • {{ $jadwal->mataKuliah->sks }} SKS</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwal->dosen->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwal->hari }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }} - {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwal->ruangan ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwal->terisi }}/{{ $jadwal->kuota }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.jadwal-kuliah.edit', $jadwal) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                <form action="{{ route('admin.jadwal-kuliah.destroy', $jadwal) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada jadwal kuliah</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $jadwalKuliahs->links() }}
        </div>
    </div>
</div>
@endsection

