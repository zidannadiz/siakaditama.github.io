@extends('layouts.app')

@section('title', 'Kriteria Pelanggaran - ' . $exam->judul)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kriteria Pelanggaran Ujian</h1>
            <p class="text-gray-600 mt-1">{{ $exam->judul }} - {{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.exam.show', $exam) }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('dosen.exam.violation-rules.update', $exam) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Tab Switch Detection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Deteksi Pergantian Tab</h2>
                    <p class="text-sm text-gray-500 mt-1">Mendeteksi ketika mahasiswa beralih ke tab/window lain</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enable_tab_switch_detection" value="1" 
                           {{ old('enable_tab_switch_detection', $violationRule->enable_tab_switch_detection) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="space-y-4 mt-4" id="tab-switch-settings">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Maksimal Jumlah Pergantian Tab
                        </label>
                        <input type="number" name="max_tab_switch_count" 
                               value="{{ old('max_tab_switch_count', $violationRule->max_tab_switch_count) }}"
                               min="1" max="100" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="terminate_on_tab_switch_limit" value="1"
                                   {{ old('terminate_on_tab_switch_limit', $violationRule->terminate_on_tab_switch_limit) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Hentikan ujian jika melebihi batas</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copy Paste Detection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Deteksi Copy-Paste</h2>
                    <p class="text-sm text-gray-500 mt-1">Mendeteksi ketika mahasiswa melakukan copy atau paste</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enable_copy_paste_detection" value="1"
                           {{ old('enable_copy_paste_detection', $violationRule->enable_copy_paste_detection) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="space-y-4 mt-4" id="copy-paste-settings">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Maksimal Jumlah Copy-Paste
                        </label>
                        <input type="number" name="max_copy_paste_count" 
                               value="{{ old('max_copy_paste_count', $violationRule->max_copy_paste_count) }}"
                               min="1" max="100" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="terminate_on_copy_paste_limit" value="1"
                                   {{ old('terminate_on_copy_paste_limit', $violationRule->terminate_on_copy_paste_limit) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Hentikan ujian jika melebihi batas</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Window Blur Detection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Deteksi Kehilangan Fokus Window</h2>
                    <p class="text-sm text-gray-500 mt-1">Mendeteksi ketika window kehilangan fokus</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enable_window_blur_detection" value="1"
                           {{ old('enable_window_blur_detection', $violationRule->enable_window_blur_detection) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="space-y-4 mt-4" id="window-blur-settings">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Maksimal Jumlah Kehilangan Fokus
                        </label>
                        <input type="number" name="max_window_blur_count" 
                               value="{{ old('max_window_blur_count', $violationRule->max_window_blur_count) }}"
                               min="1" max="100" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="terminate_on_window_blur_limit" value="1"
                                   {{ old('terminate_on_window_blur_limit', $violationRule->terminate_on_window_blur_limit) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Hentikan ujian jika melebihi batas</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fullscreen Exit Detection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Deteksi Keluar dari Fullscreen</h2>
                    <p class="text-sm text-gray-500 mt-1">Mendeteksi ketika mahasiswa keluar dari mode fullscreen</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enable_fullscreen_exit_detection" value="1"
                           {{ old('enable_fullscreen_exit_detection', $violationRule->enable_fullscreen_exit_detection) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="space-y-4 mt-4" id="fullscreen-exit-settings">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Maksimal Jumlah Keluar Fullscreen
                        </label>
                        <input type="number" name="max_fullscreen_exit_count" 
                               value="{{ old('max_fullscreen_exit_count', $violationRule->max_fullscreen_exit_count) }}"
                               min="1" max="100" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="terminate_on_fullscreen_exit_limit" value="1"
                                   {{ old('terminate_on_fullscreen_exit_limit', $violationRule->terminate_on_fullscreen_exit_limit) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Hentikan ujian jika melebihi batas</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Multiple Device Detection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Deteksi Multiple Device</h2>
                    <p class="text-sm text-gray-500 mt-1">Mendeteksi ketika mahasiswa mengakses ujian dari perangkat berbeda</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="enable_multiple_device_detection" value="1"
                           {{ old('enable_multiple_device_detection', $violationRule->enable_multiple_device_detection) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            
            <div class="space-y-4 mt-4" id="multiple-device-settings">
                <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="terminate_on_multiple_device" value="1"
                               {{ old('terminate_on_multiple_device', $violationRule->terminate_on_multiple_device) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Hentikan ujian jika terdeteksi multiple device</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Global Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Pengaturan Umum</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Terminasi Berdasarkan Total Pelanggaran</label>
                        <p class="text-sm text-gray-500 mt-1">Hentikan ujian jika total pelanggaran mencapai batas</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_time_based_termination" value="1"
                               {{ old('enable_time_based_termination', $violationRule->enable_time_based_termination) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                
                <div id="termination-settings">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Maksimal Total Pelanggaran Sebelum Dihentikan
                    </label>
                    <input type="number" name="max_violations_before_termination" 
                           value="{{ old('max_violations_before_termination', $violationRule->max_violations_before_termination) }}"
                           min="1" max="100" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pesan Peringatan untuk Mahasiswa
                    </label>
                    <textarea name="warning_message" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Pesan yang akan ditampilkan ketika mahasiswa melakukan pelanggaran">{{ old('warning_message', $violationRule->warning_message) }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pesan Saat Ujian Dihentikan
                    </label>
                    <textarea name="termination_message" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Pesan yang akan ditampilkan ketika ujian dihentikan karena pelanggaran">{{ old('termination_message', $violationRule->termination_message) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('dosen.exam.show', $exam) }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer font-medium">
                Simpan Kriteria Pelanggaran
            </button>
        </div>
    </form>
</div>

<script>
// Toggle visibility based on checkbox state
document.querySelectorAll('input[type="checkbox"][name*="enable"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const settingsId = this.name.replace('enable_', '').replace('_detection', '-settings');
        const settingsDiv = document.getElementById(settingsId);
        if (settingsDiv) {
            settingsDiv.style.display = this.checked ? 'block' : 'none';
        }
    });
    
    // Set initial state
    const settingsId = checkbox.name.replace('enable_', '').replace('_detection', '-settings');
    const settingsDiv = document.getElementById(settingsId);
    if (settingsDiv && !checkbox.checked) {
        settingsDiv.style.display = 'none';
    }
});
</script>
@endsection

