@extends('layouts.app')

@section('title', 'Statistik Keaktifan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Statistik Keaktifan</h1>
            <p class="text-gray-600 mt-1">Grafik keaktifan presensi Anda per semester</p>
        </div>
    </div>

    <!-- Filter Semester -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('mahasiswa.statistik-keaktifan.index') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Semester (bisa pilih lebih dari 1)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($semesters as $semester)
                        <label class="flex items-center space-x-2 p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="semester_ids[]" value="{{ $semester->id }}" 
                                   {{ in_array($semester->id, $selectedSemesterIds ?? []) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">
                                {{ $semester->nama_semester ?? ($semester->jenis . ' ' . $semester->tahun_ajaran) }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Tampilkan Grafik
                </button>
            </div>
        </form>
    </div>

    @if(!empty($data))
        @foreach($data as $semesterId => $semesterData)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $semesterData['semester_name'] }}</h2>
                
                <!-- Rata-rata Kehadiran -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-900">Rata-rata Kehadiran</p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $semesterData['rata_rata_kehadiran'] }}%</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-blue-700">Total: {{ $semesterData['total_hadir'] }} / {{ $semesterData['total_presensi'] }}</p>
                        </div>
                    </div>
                    <div class="mt-3 w-full bg-blue-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $semesterData['rata_rata_kehadiran'] }}%"></div>
                    </div>
                </div>

                <!-- Grafik Per Mata Kuliah -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Kehadiran per Mata Kuliah</h3>
                    <div class="h-64">
                        <canvas id="chartMataKuliah_{{ $semesterId }}"></canvas>
                    </div>
                </div>

                <!-- Tabel Detail -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sakit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alpa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($semesterData['mata_kuliah'] as $mk)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $mk['mata_kuliah'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $mk['kode_mk'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">{{ $mk['hadir'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-medium">{{ $mk['izin'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium">{{ $mk['sakit'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">{{ $mk['alpa'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $mk['persentase_kehadiran'] }}%</div>
                                            <div class="ml-2 w-24 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mk['persentase_kehadiran'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <!-- Grafik Perbandingan Antar Semester -->
        @if(count($data) > 1)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Perbandingan Keaktifan Antar Semester</h2>
                <div class="h-80">
                    <canvas id="chartPerbandingan"></canvas>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
            <p class="mt-1 text-sm text-gray-500">Pilih semester untuk melihat statistik keaktifan</p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    @if(!empty($data))
    const dataKeaktifan = @json($data);
    
    // Grafik per mata kuliah untuk setiap semester
    @foreach($data as $semesterId => $semesterData)
    const ctxMataKuliah_{{ $semesterId }} = document.getElementById('chartMataKuliah_{{ $semesterId }}').getContext('2d');
    new Chart(ctxMataKuliah_{{ $semesterId }}, {
        type: 'bar',
        data: {
            labels: @json(array_column($semesterData['mata_kuliah'], 'mata_kuliah')),
            datasets: [{
                label: 'Persentase Kehadiran (%)',
                data: @json(array_column($semesterData['mata_kuliah'], 'persentase_kehadiran')),
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
    @endforeach

    // Grafik Perbandingan Antar Semester
    @if(count($data) > 1)
    const ctxPerbandingan = document.getElementById('chartPerbandingan').getContext('2d');
    const semesterLabels = @json(array_column($data, 'semester_name'));
    const rataRataData = @json(array_column($data, 'rata_rata_kehadiran'));
    
    new Chart(ctxPerbandingan, {
        type: 'line',
        data: {
            labels: semesterLabels,
            datasets: [{
                label: 'Rata-rata Kehadiran (%)',
                data: rataRataData,
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
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
                }
            }
        }
    });
    @endif
    @endif
</script>
@endpush
@endsection

