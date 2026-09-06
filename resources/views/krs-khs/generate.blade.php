@extends('layouts.app')

@section('title', 'Cetak ' . strtoupper($jenis))

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Cetak Dokumen {{ strtoupper($jenis) }}</h1>
        <p class="text-gray-600 mt-1">Gunakan form di bawah ini untuk mencetak dokumen berdasarkan template yang tersedia.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        @if(session('error'))
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        @php
            $formRoute = auth()->user()->role === 'mahasiswa' 
                ? route('mahasiswa.generate-krs-khs.generate') 
                : route('admin.generate-krs-khs.generate');
        @endphp

        <form action="{{ $formRoute }}" method="POST" class="space-y-6">
            @csrf
            
            <input type="hidden" name="jenis" value="{{ $jenis }}">

            {{-- Template Dropdown --}}
            <div>
                <label for="template_id" class="block text-sm font-medium text-gray-700 mb-2">Template Dokumen *</label>
                @if($templates->count() > 0)
                    <select id="template_id" name="template_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Pilih Template --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->nama }} ({{ strtoupper($template->jenis) }})</option>
                        @endforeach
                    </select>
                @else
                    <div class="p-3 bg-yellow-50 text-yellow-800 rounded-lg text-sm border border-yellow-200">
                        Belum ada template {{ strtoupper($jenis) }} yang aktif. Silakan tambahkan melalui menu Master Template.
                    </div>
                @endif
                @error('template_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Mahasiswa Dropdown / Input --}}
            <div>
                <label for="mahasiswa_id" class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa *</label>
                
                @if(auth()->user()->role !== 'mahasiswa')
                    @if(count($mahasiswa) > 0)
                        <select id="mahasiswa_id" name="mahasiswa_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Pilih Mahasiswa --</option>
                            @foreach($mahasiswa as $mhs)
                                @if($mhs)
                                    <option value="{{ $mhs->id }}">{{ $mhs->nim }} - {{ $mhs->nama }} ({{ $mhs->prodi->nama_prodi ?? '-' }})</option>
                                @endif
                            @endforeach
                        </select>
                    @else
                        <div class="p-3 bg-red-50 text-red-800 rounded-lg text-sm border border-red-200">
                            Belum ada data mahasiswa yang tersedia untuk Anda.
                        </div>
                    @endif
                @else
                    @if(!empty($mahasiswa) && isset($mahasiswa[0]) && $mahasiswa[0])
                        <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa[0]->id }}">
                        <input type="text" disabled value="{{ $mahasiswa[0]->nim }} - {{ $mahasiswa[0]->nama }}"
                               class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-lg text-gray-600 cursor-not-allowed">
                    @else
                        <div class="p-3 bg-red-50 text-red-800 rounded-lg text-sm border border-red-200">
                            Data profil mahasiswa Anda tidak ditemukan. Hubungi administrator.
                        </div>
                    @endif
                @endif
                @error('mahasiswa_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Semester Dropdown (Opsional) --}}
            <div>
                <label for="semester_id" class="block text-sm font-medium text-gray-700 mb-2">Semester (Opsional)</label>
                @php
                    $semesters = \App\Models\Semester::orderBy('tahun_ajaran', 'desc')->get();
                @endphp
                <select id="semester_id" name="semester_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Semua Semester / Semester Aktif --</option>
                    @foreach($semesters as $smt)
                        <option value="{{ $smt->id }}">{{ $smt->nama_semester }} {{ $smt->tahun_ajaran }}</option>
                    @endforeach
                </select>
                <p class="text-gray-500 text-xs mt-1">Kosongkan untuk menggunakan semester aktif secara default.</p>
                @error('semester_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-gray-200 flex flex-wrap gap-4">
                <button type="submit" name="action" value="download" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2"
                        {{ $templates->count() === 0 ? 'disabled' : '' }}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download (Word)
                </button>
                <button type="submit" name="action" value="cetak" formtarget="_blank"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center justify-center gap-2"
                        {{ $templates->count() === 0 ? 'disabled' : '' }}>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Langsung
                </button>
            </div>
        </form>
    </div>
</div>
@endsection