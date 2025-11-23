@extends('layouts.app')

@section('title', 'KRS')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Kartu Rencana Studi (KRS)</h1>
        <p class="text-gray-600 mt-1">Kelola persetujuan KRS mahasiswa</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($krs_list as $krs)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $krs->mahasiswa->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nama }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $krs->jadwalKuliah->mataKuliah->nama_mk }}</div>
                                <div class="text-sm text-gray-500">{{ $krs->jadwalKuliah->mataKuliah->kode_mk }} • {{ $krs->jadwalKuliah->mataKuliah->sks }} SKS</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $krs->semester->nama_semester }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($krs->status === 'disetujui') bg-green-100 text-green-800
                                    @elseif($krs->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($krs->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @if($krs->status === 'pending')
                                    <form action="{{ route('admin.krs.approve', $krs) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.krs.reject', $krs) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menolak KRS ini?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">Tolak</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data KRS</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $krs_list->links() }}
        </div>
    </div>
</div>
@endsection

