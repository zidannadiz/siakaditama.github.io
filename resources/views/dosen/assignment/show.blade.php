@extends('layouts.app')

@section('title', $assignment->judul)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $assignment->judul }}</h1>
            <p class="text-gray-600 mt-1">{{ $assignment->jadwalKuliah->mataKuliah->nama_mk }} - {{ $assignment->jadwalKuliah->semester->nama_semester }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.assignment.edit', $assignment) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                Edit
            </a>
            <a href="{{ route('dosen.assignment.index', ['jadwal_id' => $assignment->jadwal_kuliah_id]) }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Tugas</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <span class="ml-2 px-3 py-1 text-sm font-medium rounded-full 
                            @if($assignment->status === 'published') bg-green-100 text-green-800
                            @elseif($assignment->status === 'draft') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($assignment->status) }}
                        </span>
                    </div>
                    @if($assignment->deskripsi)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $assignment->deskripsi }}</p>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Deadline</label>
                            <p class="mt-1 text-gray-900">{{ $assignment->deadline->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Bobot</label>
                            <p class="mt-1 text-gray-900">{{ $assignment->bobot }}%</p>
                        </div>
                    </div>
                    @if($assignment->file_path)
                    <div>
                        <label class="text-sm font-medium text-gray-500">File</label>
                        <div class="mt-2">
                            <a href="{{ Storage::url($assignment->file_path) }}" 
                               target="_blank"
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

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Submissions ({{ $assignment->submissions->count() }})</h2>
                <div class="space-y-4">
                    @forelse($assignment->submissions as $submission)
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $submission->mahasiswa->nama }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $submission->mahasiswa->nim }}</p>
                                    <p class="text-sm text-gray-500 mt-2">
                                        Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, H:i') : '-' }}
                                        @if($submission->isLate())
                                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">Terlambat</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @if($submission->nilai !== null)
                                        <span class="text-lg font-bold text-gray-900">{{ number_format($submission->nilai, 2) }}</span>
                                    @else
                                        <span class="text-sm text-gray-500">Belum dinilai</span>
                                    @endif
                                    <button onclick="gradeSubmission({{ $submission->id }}, '{{ $submission->mahasiswa->nama }}', {{ $submission->nilai ?? 'null' }})"
                                            class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        Nilai
                                    </button>
                                </div>
                            </div>
                            @if($submission->jawaban)
                                <div class="mt-3 p-3 bg-white rounded border border-gray-200">
                                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $submission->jawaban }}</p>
                                </div>
                            @endif
                            @if($submission->file_path)
                                <div class="mt-3">
                                    <a href="{{ Storage::url($submission->file_path) }}" 
                                       target="_blank"
                                       class="inline-flex items-center space-x-2 text-sm text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Download Submission</span>
                                    </a>
                                </div>
                            @endif
                            @if($submission->feedback)
                                <div class="mt-3 p-3 bg-blue-50 rounded border border-blue-200">
                                    <label class="text-sm font-medium text-blue-900">Feedback:</label>
                                    <p class="text-sm text-blue-800 mt-1 whitespace-pre-wrap">{{ $submission->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Belum ada submission</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk grading -->
<div id="gradeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Nilai Submission</h3>
        <form id="gradeForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa</label>
                    <p id="studentName" class="text-gray-900"></p>
                </div>
                <div>
                    <label for="nilai" class="block text-sm font-medium text-gray-700 mb-2">Nilai (0-100) *</label>
                    <input type="number" 
                           id="nilai" 
                           name="nilai" 
                           min="0" 
                           max="100" 
                           step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                <div>
                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">Feedback</label>
                    <textarea id="feedback" 
                              name="feedback" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeGradeModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function gradeSubmission(submissionId, studentName, currentNilai) {
    document.getElementById('studentName').textContent = studentName;
    document.getElementById('nilai').value = currentNilai || '';
    // Build the correct route URL using the assignment ID and submission ID
    document.getElementById('gradeForm').action = '{{ url("/dosen/assignment") }}/{{ $assignment->id }}/grade-submission/' + submissionId;
    document.getElementById('gradeModal').classList.remove('hidden');
    document.getElementById('gradeModal').classList.add('flex');
}

function closeGradeModal() {
    document.getElementById('gradeModal').classList.add('hidden');
    document.getElementById('gradeModal').classList.remove('flex');
}

// Close modal on outside click
document.getElementById('gradeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGradeModal();
    }
});
</script>
@endsection

