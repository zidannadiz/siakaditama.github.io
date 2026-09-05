@extends('layouts.app')

@section('title', 'Detail Tahun Ajaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Tahun Ajaran</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap tahun ajaran dan semester terkait</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.tahun-ajaran.edit', $tahunAjaran) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                Edit Tahun Ajaran
            </a>
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium border border-gray-200">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Info Card -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-3 mb-4">Informasi Umum</h3>
                
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Tahun Ajaran</dt>
                        <dd class="mt-1 text-base text-gray-900 font-medium">{{ $tahunAjaran->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Periode</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tahunAjaran->tahun_mulai }} / {{ $tahunAjaran->tahun_selesai }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Pelaksanaan</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $tahunAjaran->tanggal_mulai?->format('d M Y') ?? 'Belum diset' }} 
                            - 
                            {{ $tahunAjaran->tanggal_selesai?->format('d M Y') ?? 'Belum diset' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tahunAjaran->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($tahunAjaran->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($tahunAjaran->keterangan)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $tahunAjaran->keterangan }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
        
        <!-- Semester Terkait -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Semester</h3>
                    <a href="{{ route('admin.semester.create') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">+ Tambah Semester</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Semester</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tahunAjaran->semesters as $semester)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $semester->nama }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ucfirst($semester->jenis) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $semester->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($semester->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.semester.edit', $semester) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Belum ada semester pada tahun ajaran ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
