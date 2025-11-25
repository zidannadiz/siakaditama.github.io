@extends('layouts.app')

@section('title', 'Statistik Presensi per Prodi per Semester')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Statistik Presensi per Prodi per Semester</h1>
            <p class="text-gray-600 mt-1">Grafik presensi mahasiswa per program studi per semester (1-8)</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.statistik-presensi-per-prodi.index') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Akademik</label>
                <select name="tahun_ajaran" onchange="this.form.submit()" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($tahunAkademiks as $tahun)
                        <option value="{{ $tahun }}" {{ $selectedTahun == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}/{{ $tahun + 1 }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                <select name="prodi_id" onchange="this.form.submit()" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ $selectedProdiId == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama_prodi }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if(!empty($data))
        @foreach($data as $prodiData)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $prodiData['prodi'] }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Kode: {{ $prodiData['kode_prodi'] }}</p>
                </div>

                <!-- Grafik Persentase Kehadiran -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Persentase Kehadiran per Semester</h3>
                    <div class="h-64">
                        <canvas id="chartPresensi{{ $prodiData['kode_prodi'] }}"></canvas>
                    </div>
                </div>

                <!-- Grafik Detail Status -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Detail Status Presensi</h3>
                    <div class="h-64">
                        <canvas id="chartDetail{{ $prodiData['kode_prodi'] }}"></canvas>
                    </div>
                </div>

                <!-- Tabel Data -->
                <div class="overflow-hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Detail Presensi</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Mahasiswa</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sakit</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alpa</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @for($i = 1; $i <= 8; $i++)
                                    @if(isset($prodiData['semesters'][$i]))
                                        @php $sem = $prodiData['semesters'][$i]; @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Semester {{ $i }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center">{{ $sem['total_mahasiswa'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600 font-medium text-center">{{ $sem['hadir'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-yellow-600 font-medium text-center">{{ $sem['izin'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-blue-600 font-medium text-center">{{ $sem['sakit'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-medium text-center">{{ $sem['alpa'] }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center">
                                                    <div class="text-sm font-medium text-gray-900">{{ $sem['persentase_kehadiran'] }}%</div>
                                                    <div class="ml-2 w-24 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $sem['persentase_kehadiran'] }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
            <p class="mt-1 text-sm text-gray-500">Pilih tahun akademik untuk melihat statistik presensi</p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    @if(!empty($data))
    const dataProdi = @json($data);
    
    @foreach($data as $prodiData)
    // Grafik Persentase Kehadiran untuk {{ $prodiData['prodi'] }}
    const ctxPresensi{{ $prodiData['kode_prodi'] }} = document.getElementById('chartPresensi{{ $prodiData['kode_prodi'] }}');
    if (ctxPresensi{{ $prodiData['kode_prodi'] }}) {
        const semesters = [];
        const percentages = [];
        @for($i = 1; $i <= 8; $i++)
            @if(isset($prodiData['semesters'][$i]))
                semesters.push('Semester {{ $i }}');
                percentages.push({{ $prodiData['semesters'][$i]['persentase_kehadiran'] }});
            @endif
        @endfor
        
        new Chart(ctxPresensi{{ $prodiData['kode_prodi'] }}, {
            type: 'bar',
            data: {
                labels: semesters,
                datasets: [{
                    label: 'Persentase Kehadiran (%)',
                    data: percentages,
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
    }

    // Grafik Detail Status untuk {{ $prodiData['prodi'] }}
    const ctxDetail{{ $prodiData['kode_prodi'] }} = document.getElementById('chartDetail{{ $prodiData['kode_prodi'] }}');
    if (ctxDetail{{ $prodiData['kode_prodi'] }}) {
        const semesters = [];
        const hadir = [];
        const izin = [];
        const sakit = [];
        const alpa = [];
        
        @for($i = 1; $i <= 8; $i++)
            @if(isset($prodiData['semesters'][$i]))
                semesters.push('Semester {{ $i }}');
                hadir.push({{ $prodiData['semesters'][$i]['hadir'] }});
                izin.push({{ $prodiData['semesters'][$i]['izin'] }});
                sakit.push({{ $prodiData['semesters'][$i]['sakit'] }});
                alpa.push({{ $prodiData['semesters'][$i]['alpa'] }});
            @endif
        @endfor
        
        new Chart(ctxDetail{{ $prodiData['kode_prodi'] }}, {
            type: 'bar',
            data: {
                labels: semesters,
                datasets: [
                    {
                        label: 'Hadir',
                        data: hadir,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Izin',
                        data: izin,
                        backgroundColor: 'rgba(234, 179, 8, 0.8)',
                        borderColor: 'rgba(234, 179, 8, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Sakit',
                        data: sakit,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Alpa',
                        data: alpa,
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
    }
    @endforeach
    @endif
</script>
@endpush
@endsection

