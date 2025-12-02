@extends('layouts.app')

@section('title', $assignment->judul)

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $assignment->judul }}</h1>
        <p class="text-gray-600 mt-1">{{ $assignment->jadwalKuliah->mataKuliah->nama_mk }} - {{ $assignment->jadwalKuliah->semester->nama_semester }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Tugas</h2>
                <div class="space-y-4">
                    @if($assignment->deskripsi)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $assignment->deskripsi }}</p>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Deadline</label>
                            <p class="mt-1 text-gray-900 {{ $assignment->isExpired() ? 'text-red-600 font-semibold' : '' }}">
                                {{ $assignment->deadline->format('d M Y, H:i') }}
                                @if($assignment->isExpired())
                                    (Sudah lewat)
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Bobot</label>
                            <p class="mt-1 text-gray-900">{{ $assignment->bobot }}%</p>
                        </div>
                    </div>
                    @if($assignment->file_path)
                    <div>
                        <label class="text-sm font-medium text-gray-500">File Tugas</label>
                        <div class="mt-2">
                            <a href="{{ route('mahasiswa.assignment.download', $assignment) }}" 
                               class="inline-flex items-center space-x-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Download File</span>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if(!$submission || !$assignment->isExpired())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        {{ $submission ? 'Update Submission' : 'Submit Tugas' }}
                    </h2>
                    <form action="{{ $submission ? route('mahasiswa.assignment.update-submission', ['assignment' => $assignment, 'submission' => $submission]) : route('mahasiswa.assignment.submit', $assignment) }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf
                        @if($submission)
                            @method('PUT')
                        @endif

                        <div class="space-y-4">
                            <div>
                                <label for="jawaban" class="block text-sm font-medium text-gray-700 mb-2">Jawaban</label>
                                <textarea id="jawaban" 
                                          name="jawaban" 
                                          rows="8"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('jawaban', $submission->jawaban ?? '') }}</textarea>
                                @error('jawaban')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">File Jawaban (Opsional)</label>
                                <input type="file" 
                                       id="file" 
                                       name="file"
                                       accept=".pdf,.doc,.docx,.zip,.rar"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">Format: PDF, DOC, DOCX, ZIP, RAR (Max: 10MB)</p>
                                @if($submission && $submission->file_path)
                                    <p class="mt-2 text-sm text-green-600">✓ File sudah diupload</p>
                                @endif
                                @error('file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    {{ $submission ? 'Update' : 'Submit' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            @if($submission)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Submission Anda</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Submitted</label>
                            <p class="mt-1 text-gray-900">
                                {{ $submission->submitted_at->format('d M Y, H:i') }}
                                @if($submission->isLate())
                                    <span class="ml-2 px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">Terlambat</span>
                                @endif
                            </p>
                        </div>
                        @if($submission->jawaban)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Jawaban</label>
                                <div class="mt-2 p-3 bg-gray-50 rounded border border-gray-200">
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $submission->jawaban }}</p>
                                </div>
                            </div>
                        @endif
                        @if($submission->file_path)
                            <div>
                                <label class="text-sm font-medium text-gray-500">File</label>
                                <div class="mt-2">
                                    <a href="{{ Storage::url($submission->file_path) }}" 
                                       target="_blank"
                                       class="inline-flex items-center space-x-2 text-sm text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Download Submission</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                        @if($submission->nilai !== null)
                            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                                <label class="text-sm font-medium text-green-900">Nilai</label>
                                <p class="text-2xl font-bold text-green-700 mt-1">{{ number_format($submission->nilai, 2) }}</p>
                            </div>
                        @endif
                        @if($submission->feedback)
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <label class="text-sm font-medium text-blue-900">Feedback</label>
                                <p class="text-blue-800 mt-1 whitespace-pre-wrap">{{ $submission->feedback }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

