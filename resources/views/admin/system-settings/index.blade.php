@extends('layouts.app')

@section('title', 'Konfigurasi Sistem')

@section('content')
<div class="space-y-6" x-data="{ activeTab: '{{ $activeTab }}' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Konfigurasi Sistem</h1>
            <p class="text-gray-600 mt-1">Kelola pengaturan sistem aplikasi</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-red-800">Terjadi Kesalahan</h3>
                    <ul class="text-sm text-red-700 mt-1 list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'semester'" 
                        :class="activeTab === 'semester' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                    Semester Aktif
                </button>
                <button @click="activeTab = 'grading'" 
                        :class="activeTab === 'grading' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                    Bobot Penilaian
                </button>
                <button @click="activeTab = 'letter-grades'" 
                        :class="activeTab === 'letter-grades' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                    Huruf Mutu & Bobot
                </button>
                <button @click="activeTab = 'app-info'" 
                        :class="activeTab === 'app-info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition-colors">
                    Informasi Aplikasi
                </button>
            </nav>
        </div>

        <!-- Tab Content: Semester Aktif -->
        <div x-show="activeTab === 'semester'" x-transition class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Pengaturan Semester Aktif</h2>
            <p class="text-sm text-gray-600 mb-6">Pilih semester yang aktif untuk sistem akademik</p>
            
            <form action="{{ route('admin.system-settings.update-semester') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Semester Aktif</label>
                        <select name="semester_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Semester --</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ $activeSemester && $activeSemester->id === $semester->id ? 'selected' : '' }}>
                                    {{ $semester->nama_semester }} - {{ $semester->tahun_ajaran }} 
                                    @if($semester->status === 'aktif')
                                        (Aktif)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if($activeSemester)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-blue-800">
                                        <strong>Semester Aktif Saat Ini:</strong> {{ $activeSemester->nama_semester }} - {{ $activeSemester->tahun_ajaran }}
                                    </p>
                                    @if($activeSemester->tanggal_mulai && $activeSemester->tanggal_selesai)
                                        <p class="text-xs text-blue-600 mt-1">
                                            Periode: {{ $activeSemester->tanggal_mulai->format('d/m/Y') }} - {{ $activeSemester->tanggal_selesai->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium" style="cursor: pointer;">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab Content: Bobot Penilaian -->
        <div x-show="activeTab === 'grading'" x-transition class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Konfigurasi Bobot Penilaian</h2>
            <p class="text-sm text-gray-600 mb-6">Atur bobot untuk komponen penilaian (Total harus 100%)</p>
            
            <form action="{{ route('admin.system-settings.update-grading') }}" method="POST" id="gradingForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bobot Tugas (%)</label>
                        <input type="number" name="weight_tugas" step="0.01" min="0" max="100" 
                               value="{{ $gradingWeights['tugas'] }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bobot UTS (%)</label>
                        <input type="number" name="weight_uts" step="0.01" min="0" max="100" 
                               value="{{ $gradingWeights['uts'] }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bobot UAS (%)</label>
                        <input type="number" name="weight_uas" step="0.01" min="0" max="100" 
                               value="{{ $gradingWeights['uas'] }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Bobot:</span>
                            <span id="totalWeight" class="text-lg font-bold text-gray-900">
                                {{ number_format($gradingWeights['tugas'] + $gradingWeights['uts'] + $gradingWeights['uas'], 2) }}%
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium" style="cursor: pointer;">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tab Content: Huruf Mutu & Bobot -->
        <div x-show="activeTab === 'letter-grades'" x-transition class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Konfigurasi Huruf Mutu & Bobot</h2>
                    <p class="text-sm text-gray-600 mt-1">Kelola huruf mutu dan bobot nilai untuk sistem penilaian</p>
                </div>
            </div>

            @if($letterGrades->isEmpty())
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500">Belum ada huruf mutu yang dikonfigurasi</p>
                </div>
            @else
                <div class="overflow-x-auto mb-6">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Huruf Mutu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Range Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bobot</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($letterGrades as $letterGrade)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-semibold text-gray-900">{{ $letterGrade->letter }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $letterGrade->min_score }} - {{ $letterGrade->max_score ?? '100' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ number_format($letterGrade->bobot, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.system-settings.index', ['tab' => 'letter-grades', 'edit' => $letterGrade->id]) }}" class="text-blue-600 hover:text-blue-900 transition-colors" style="cursor: pointer;">
                                                Edit
                                            </a>
                                            <span class="text-gray-300">|</span>
                                            <form action="{{ route('admin.system-settings.delete-letter-grade', $letterGrade->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus huruf mutu {{ $letterGrade->letter }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" style="cursor: pointer;">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <!-- Success/Error Messages -->
            @if(session('success') && ($activeTab === 'letter-grades' || request('tab') === 'letter-grades'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            
            @if($errors->any() && ($activeTab === 'letter-grades' || request('tab') === 'letter-grades'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-red-800">Terjadi Kesalahan</p>
                            <ul class="text-sm text-red-700 mt-1 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Form Tambah/Edit Huruf Mutu -->
            <div id="letterGradeFormContainer" class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ request('edit') ? 'Edit' : 'Tambah' }} Huruf Mutu</h3>
                @php
                    $editGrade = request('edit') ? $letterGrades->firstWhere('id', request('edit')) : null;
                @endphp
                <form id="letterGradeForm" action="{{ $editGrade ? route('admin.system-settings.update-letter-grade', $editGrade->id) : route('admin.system-settings.store-letter-grade') }}" method="POST">
                    @csrf
                    @if($editGrade)
                        @method('PUT')
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Huruf Mutu</label>
                            <input type="text" name="letter" value="{{ old('letter', $editGrade->letter ?? '') }}" maxlength="5" required
                                   class="w-full px-4 py-2 border {{ $errors->has('letter') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('letter')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Minimal</label>
                            <input type="number" name="min_score" value="{{ old('min_score', $editGrade->min_score ?? '') }}" min="0" max="100" required
                                   class="w-full px-4 py-2 border {{ $errors->has('min_score') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('min_score')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Maksimal (kosongkan untuk 100)</label>
                            <input type="number" name="max_score" value="{{ old('max_score', $editGrade->max_score ?? '') }}" min="0" max="100"
                                   class="w-full px-4 py-2 border {{ $errors->has('max_score') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('max_score')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bobot</label>
                            <input type="number" name="bobot" value="{{ old('bobot', $editGrade->bobot ?? '') }}" step="0.01" min="0" max="4" required
                                   class="w-full px-4 py-2 border {{ $errors->has('bobot') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('bobot')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 space-x-3">
                        @if($editGrade)
                            <a href="{{ route('admin.system-settings.index', ['tab' => 'letter-grades']) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                        @endif
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" style="cursor: pointer;">
                            {{ $editGrade ? 'Update' : 'Tambah' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab Content: Informasi Aplikasi -->
        <div x-show="activeTab === 'app-info'" x-transition class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Konfigurasi Informasi Aplikasi</h2>
            <p class="text-sm text-gray-600 mb-6">Kelola informasi aplikasi, logo, dan favicon</p>
            
            <!-- Success Message in Tab -->
            @if(session('success') && request('tab') === 'app-info')
                <div id="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 animate-pulse">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            <p class="text-xs text-green-600 mt-1">Perubahan telah berhasil disimpan.</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <form id="appInfoForm" action="{{ route('admin.system-settings.update-app-info') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Aplikasi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $appInfo['name']) }}" required maxlength="255"
                               class="w-full px-4 py-2 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Institusi</label>
                        <input type="text" name="institution" value="{{ old('institution', $appInfo['institution']) }}" maxlength="255"
                               class="w-full px-4 py-2 border {{ $errors->has('institution') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('institution')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea name="address" rows="3" maxlength="500"
                                  class="w-full px-4 py-2 border {{ $errors->has('address') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('address', $appInfo['address']) }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $appInfo['phone']) }}" maxlength="50"
                                   class="w-full px-4 py-2 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $appInfo['email']) }}" maxlength="255"
                                   class="w-full px-4 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="text" name="website" value="{{ old('website', $appInfo['website']) }}" maxlength="255" placeholder="https://example.com"
                               class="w-full px-4 py-2 border {{ $errors->has('website') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('website')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Masukkan URL lengkap (contoh: https://example.com)</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo Aplikasi</label>
                            @if($appInfo['logo'])
                                @php
                                    $logoUrl = \App\Services\SystemSettingsService::getLogoUrl();
                                @endphp
                                @if($logoUrl)
                                    <div class="mb-3">
                                        <img src="{{ $logoUrl }}" alt="Logo" class="h-20 object-contain">
                                    </div>
                                @endif
                            @endif
                            <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg" 
                                   class="w-full px-4 py-2 border {{ $errors->has('logo') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Format: PNG, JPG, JPEG (Max: 2MB)</p>
                            @error('logo')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                            @if($appInfo['favicon'])
                                @php
                                    $faviconUrl = \App\Services\SystemSettingsService::getFaviconUrl();
                                @endphp
                                @if($faviconUrl)
                                    <div class="mb-3">
                                        <img src="{{ $faviconUrl }}" alt="Favicon" class="h-16 w-16 object-contain">
                                    </div>
                                @endif
                            @endif
                            <input type="file" name="favicon" accept="image/png,image/ico" 
                                   class="w-full px-4 py-2 border {{ $errors->has('favicon') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Format: PNG, ICO (Max: 512KB)</p>
                            @error('favicon')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" id="submitAppInfoBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium" style="cursor: pointer;">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grading form handler
        const form = document.getElementById('gradingForm');
        if (form) {
            const inputs = form.querySelectorAll('input[type="number"]');
            const totalWeightSpan = document.getElementById('totalWeight');
            
            function updateTotal() {
                let total = 0;
                inputs.forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    total += value;
                });
                totalWeightSpan.textContent = total.toFixed(2) + '%';
                
                if (Math.abs(total - 100) < 0.01) {
                    totalWeightSpan.classList.remove('text-red-600');
                    totalWeightSpan.classList.add('text-green-600');
                } else {
                    totalWeightSpan.classList.remove('text-green-600');
                    totalWeightSpan.classList.add('text-red-600');
                }
            }
            
            inputs.forEach(input => {
                input.addEventListener('input', updateTotal);
            });
            
            form.addEventListener('submit', function(e) {
                updateTotal();
                const total = parseFloat(totalWeightSpan.textContent);
                if (Math.abs(total - 100) > 0.01) {
                    e.preventDefault();
                    alert('Total bobot harus 100%. Saat ini: ' + total.toFixed(2) + '%');
                    return false;
                }
            });
        }
        
        // App Info form handler - simple handler for button loading state only
        const appInfoForm = document.getElementById('appInfoForm');
        if (appInfoForm) {
            appInfoForm.addEventListener('submit', function(e) {
                console.log('Form submitting...');
                
                // Only update button state, don't prevent submission
                const submitBtn = document.getElementById('submitAppInfoBtn');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span>⏳ Menyimpan...</span>';
                }
                
                // Allow form to submit normally
                return true;
            });
        }
        
        // Auto-scroll to success message if present
        @if(session('success') && request('tab') === 'app-info')
            setTimeout(function() {
                const successMsg = document.getElementById('successMessage');
                if (successMsg) {
                    successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Auto-hide after 5 seconds
                    setTimeout(function() {
                        successMsg.style.transition = 'opacity 0.5s';
                        successMsg.style.opacity = '0';
                        setTimeout(function() {
                            successMsg.remove();
                        }, 500);
                    }, 5000);
                }
            }, 100);
        @endif
        
        // Letter Grade form handler
        const letterGradeForm = document.getElementById('letterGradeForm');
        if (letterGradeForm) {
            letterGradeForm.addEventListener('submit', function(e) {
                console.log('Letter Grade form submitting...');
                
                // Only update button state if needed
                const submitBtn = letterGradeForm.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = submitBtn.textContent.replace('Update', 'Menyimpan...').replace('Tambah', 'Menambahkan...');
                }
                
                // Allow form to submit normally
                return true;
            });
        }
        
        // Auto-scroll to edit form if edit parameter exists
        @if(request('edit'))
            setTimeout(function() {
                // Ensure tab is active first
                const alpineData = document.querySelector('[x-data*="activeTab"]');
                if (alpineData && alpineData.__x) {
                    alpineData.__x.$data.activeTab = 'letter-grades';
                }
                
                // Wait a bit for Alpine.js to render
                setTimeout(function() {
                    const formContainer = document.getElementById('letterGradeFormContainer');
                    if (formContainer) {
                        formContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Highlight the form
                        formContainer.style.transition = 'box-shadow 0.3s';
                        formContainer.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.3)';
                        setTimeout(function() {
                            formContainer.style.boxShadow = '';
                        }, 2000);
                        
                        // Focus first input
                        const firstInput = formContainer.querySelector('input[name="letter"]');
                        if (firstInput) {
                            setTimeout(function() {
                                firstInput.focus();
                                firstInput.select();
                            }, 500);
                        }
                    }
                }, 200);
            }, 100);
        @endif
    });
</script>
@endpush
@endsection
