@extends('layouts.app')

@section('title', 'Statistik Presensi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Statistik Presensi</h1>
            <p class="text-gray-600 mt-1">Statistik presensi mahasiswa per semester</p>
        </div>
        <a href="{{ route('admin.laporan.akademik.index') }}" 
           class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
           style="background-color: #6B7280 !important;">
            Kembali
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.laporan.akademik.presensi') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <select name="semester_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semester Aktif</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id || ($semester && $semester->id == $sem->id) ? 'selected' : '' }}>
                            {{ $sem->nama_semester ?? $sem->jenis }} - {{ $sem->tahun_akademik ?? $sem->tahun_ajaran }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.laporan.akademik.presensi') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if($semester)
        <div class="bg-blue-100 border-2 border-blue-400 rounded-xl p-4" style="background-color: #DBEAFE !important; border-color: #60A5FA !important;">
            <p class="text-sm font-medium text-blue-900" style="color: #1E40AF !important;">
                <strong>Semester:</strong> {{ $semester->nama_semester ?? $semester->jenis }} - {{ $semester->tahun_akademik ?? $semester->tahun_ajaran }}
            </p>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-200" style="background-color: #E5E7EB !important;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">Mata Kuliah</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">Hadir</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">Izin</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">Sakit</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">Alpha</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-900 uppercase tracking-wider" style="background-color: #E5E7EB !important; color: #111827 !important; font-weight: bold !important;">% Hadir</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($statistik as $data)
                        @php
                            $firstRow = true;
                            $rowspan = count($data['mata_kuliah']);
                        @endphp
                        @foreach($data['mata_kuliah'] as $mk)
                            <tr class="hover:bg-gray-50">
                                @if($firstRow)
                                    <td rowspan="{{ $rowspan }}" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 align-top">
                                        {{ $data['nim'] }}
                                    </td>
                                    <td rowspan="{{ $rowspan }}" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 align-top">
                                        {{ $data['nama'] }}
                                    </td>
                                    @php $firstRow = false; @endphp
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk['nama'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ $mk['hadir'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ $mk['izin'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ $mk['sakit'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ $mk['alpha'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ $mk['total'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @php
                                        $persentase = $mk['total'] > 0 ? ($mk['hadir'] / $mk['total']) * 100 : 0;
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $persentase >= 75 ? 'bg-green-100 text-green-800' : ($persentase >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($persentase, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @if(count($data['mata_kuliah']) > 0)
                            <tr class="bg-gray-200" style="background-color: #E5E7EB !important;">
                                <td colspan="3" class="px-6 py-4 text-sm font-bold text-gray-900 text-right" style="font-weight: bold !important;">Total:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-gray-900" style="font-weight: bold !important;">{{ $data['total_hadir'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-gray-900" style="font-weight: bold !important;">{{ $data['total_izin'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-gray-900" style="font-weight: bold !important;">{{ $data['total_sakit'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-gray-900" style="font-weight: bold !important;">{{ $data['total_alpha'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-gray-900" style="font-weight: bold !important;">{{ $data['total_presensi'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @php
                                        $total_persentase = $data['total_presensi'] > 0 ? ($data['total_hadir'] / $data['total_presensi']) * 100 : 0;
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $total_persentase >= 75 ? 'bg-green-200 text-green-900' : ($total_persentase >= 50 ? 'bg-yellow-200 text-yellow-900' : 'bg-red-200 text-red-900') }}" style="font-weight: bold !important;">
                                        {{ number_format($total_persentase, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data presensi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

