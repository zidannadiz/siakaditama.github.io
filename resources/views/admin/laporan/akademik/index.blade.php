@extends('layouts.app')

@section('title', 'Laporan Akademik')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Laporan Akademik</h1>
            <p class="text-gray-600 mt-1">Laporan IPK, kelulusan, dan statistik akademik mahasiswa</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.laporan.akademik.export-excel', request()->all()) }}" 
               class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
               style="background-color: #16A34A !important;">
                Export Excel
            </a>
            <a href="{{ route('admin.laporan.akademik.export-pdf', request()->all()) }}" 
               class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
               style="background-color: #DC2626 !important;">
                Export PDF
            </a>
            <a href="{{ route('admin.laporan.akademik.presensi', request()->all()) }}" 
               class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
               style="background-color: #9333EA !important;">
                Statistik Presensi
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Mahasiswa</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_mahasiswa']) }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-sm text-blue-700">Rata-rata IPK</p>
            <p class="text-2xl font-bold text-blue-900 mt-1">{{ number_format($stats['avg_ipk'], 2) }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm text-green-700">Lulus</p>
            <p class="text-2xl font-bold text-green-900 mt-1">{{ number_format($stats['lulus']) }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm text-red-700">Belum Lulus</p>
            <p class="text-2xl font-bold text-red-900 mt-1">{{ number_format($stats['tidak_lulus']) }}</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.laporan.akademik.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <select name="prodi_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}
                        </option>
                    @endforeach
                </select>
                
                <select name="semester_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semester Aktif</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->nama_semester ?? $sem->jenis }} - {{ $sem->tahun_akademik ?? $sem->tahun_ajaran }}
                        </option>
                    @endforeach
                </select>
                
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                       placeholder="Cari NIM atau nama...">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.laporan.akademik.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program Studi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IPK Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IPK Kumulatif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS Kumulatif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($mahasiswas as $mahasiswa)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mahasiswa->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $mahasiswa->nama }}</div>
                                <div class="text-xs text-gray-500">{{ $mahasiswa->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium {{ $mahasiswa->ipk >= 3.0 ? 'text-green-600' : ($mahasiswa->ipk >= 2.0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($mahasiswa->ipk, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium {{ $mahasiswa->ipk_cumulative >= 3.0 ? 'text-green-600' : ($mahasiswa->ipk_cumulative >= 2.0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($mahasiswa->ipk_cumulative, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->total_sks }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->cumulative_sks }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($mahasiswa->ipk_cumulative >= 2.00 && $mahasiswa->cumulative_sks >= 144)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Lulus
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Belum Lulus
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data mahasiswa
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $mahasiswas->links() }}
        </div>
    </div>
    
    @if($semester)
        <div class="bg-blue-100 border-2 border-blue-400 rounded-xl p-4" style="background-color: #DBEAFE !important; border-color: #60A5FA !important;">
            <p class="text-sm font-medium text-blue-900" style="color: #1E40AF !important;">
                <strong>Semester:</strong> {{ $semester->nama_semester ?? $semester->jenis }} - {{ $semester->tahun_akademik ?? $semester->tahun_ajaran }}
            </p>
        </div>
    @endif
</div>
@endsection

