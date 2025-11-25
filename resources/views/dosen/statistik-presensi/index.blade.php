@extends('layouts.app')

@section('title', 'Statistik Presensi per Mata Kuliah')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Statistik Presensi per Mata Kuliah</h1>
            <p class="text-gray-600 mt-1">Grafik presensi mata kuliah yang Anda ajar per semester</p>
        </div>
    </div>

    <!-- Filter Semester -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('dosen.statistik-presensi.index') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Semester</label>
                <select name="semester_id" onchange="this.form.submit()" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ $selectedSemesterId == $semester->id ? 'selected' : '' }}>
                            {{ $semester->nama_semester ?? ($semester->jenis . ' ' . $semester->tahun_ajaran) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if(!empty($data))
        <!-- Grafik Presensi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Grafik Persentase Kehadiran per Mata Kuliah</h2>
            <div class="h-80">
                <canvas id="chartPresensi"></canvas>
            </div>
        </div>

        <!-- Grafik Detail Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Grafik Detail Status Presensi</h2>
            <div class="h-80">
                <canvas id="chartDetail"></canvas>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Data Detail Presensi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Mahasiswa</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sakit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alpa</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($data as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item['mata_kuliah'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $item['kode_mk'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $item['total_mahasiswa'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium text-center">{{ $item['hadir'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-medium text-center">{{ $item['izin'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium text-center">{{ $item['sakit'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium text-center">{{ $item['alpa'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $item['persentase_kehadiran'] }}%</div>
                                        <div class="ml-2 w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $item['persentase_kehadiran'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data presensi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
            <p class="mt-1 text-sm text-gray-500">Pilih semester untuk melihat statistik presensi</p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    @if(!empty($data))
    // Data untuk grafik
    const dataPresensi = @json($data);
    
    // Grafik Persentase Kehadiran
    const ctxPresensi = document.getElementById('chartPresensi').getContext('2d');
    new Chart(ctxPresensi, {
        type: 'bar',
        data: {
            labels: dataPresensi.map(item => item.mata_kuliah),
            datasets: [{
                label: 'Persentase Kehadiran (%)',
                data: dataPresensi.map(item => item.persentase_kehadiran),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Kehadiran: ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 0,
                        minRotation: 0
                    }
                }
            }
        }
    });

    // Grafik Detail Status
    const ctxDetail = document.getElementById('chartDetail').getContext('2d');
    new Chart(ctxDetail, {
        type: 'bar',
        data: {
            labels: dataPresensi.map(item => item.mata_kuliah),
            datasets: [
                {
                    label: 'Hadir',
                    data: dataPresensi.map(item => item.hadir),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Izin',
                    data: dataPresensi.map(item => item.izin),
                    backgroundColor: 'rgba(234, 179, 8, 0.8)',
                    borderColor: 'rgba(234, 179, 8, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Sakit',
                    data: dataPresensi.map(item => item.sakit),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Alpa',
                    data: dataPresensi.map(item => item.alpa),
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: false
                },
                x: {
                    ticks: {
                        maxRotation: 0,
                        minRotation: 0
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection

