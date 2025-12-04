@extends('layouts.app')

@section('title', $exam->judul)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $exam->judul }}</h1>
            <p class="text-gray-600 mt-1">{{ $exam->jadwalKuliah->mataKuliah->nama_mk }} - {{ $exam->jadwalKuliah->semester->nama_semester }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.exam.edit', $exam) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors cursor-pointer">
                Edit
            </a>
            <a href="{{ route('dosen.exam.results', $exam) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                Hasil Ujian
            </a>
            <a href="{{ route('dosen.exam.violation-rules', $exam) }}" 
               class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors cursor-pointer">
                Kriteria Pelanggaran
            </a>
            <a href="{{ route('dosen.exam.violations', $exam) }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors cursor-pointer">
                Daftar Pelanggaran
            </a>
            @php
                $isOngoing = $exam->isOngoing();
                $activeCount = $exam->sessions->where('status', 'started')->whereNull('finished_at')->count();
            @endphp
            @if($isOngoing && $activeCount > 0)
            <a href="{{ route('dosen.exam.active-students', $exam) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors cursor-pointer">
                Mahasiswa Aktif ({{ $activeCount }})
            </a>
            @endif
            <a href="{{ route('dosen.exam.index', ['jadwal_id' => $exam->jadwal_kuliah_id]) }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Exam Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Ujian</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Status</span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                            @if($exam->status === 'published') bg-green-100 text-green-800
                            @elseif($exam->status === 'draft') bg-gray-100 text-gray-800
                            @elseif($exam->status === 'ongoing') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Tipe</span>
                        <span class="text-sm text-gray-900">{{ ucfirst($exam->tipe) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Durasi</span>
                        <span class="text-sm text-gray-900">{{ $exam->durasi }} menit</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Jumlah Soal</span>
                        <span class="text-sm text-gray-900">{{ $exam->total_soal }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Bobot</span>
                        <span class="text-sm text-gray-900">{{ $exam->bobot }}%</span>
                    </div>
                    @if($exam->mulai)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Mulai</span>
                        <span class="text-sm text-gray-900">{{ $exam->mulai->format('d M Y, H:i') }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Selesai</span>
                        <span class="text-sm text-gray-900">{{ $exam->selesai->format('d M Y, H:i') }}</span>
                    </div>
                    @if($exam->deskripsi)
                    <div>
                        <span class="text-sm text-gray-500 block mb-1">Deskripsi</span>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $exam->deskripsi }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Questions List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Soal Ujian ({{ $exam->questions->count() }})</h2>
                    @if($exam->questions->count() === 0)
                        <button onclick="toggleGenerateQuestionsForm()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium cursor-pointer">
                            Atur Jumlah Soal
                        </button>
                    @else
                        <button onclick="toggleAddQuestionForm()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium cursor-pointer">
                            + Tambah Soal
                        </button>
                    @endif
                </div>

                <!-- Generate Questions Form -->
                @if($exam->questions->count() === 0)
                <div id="generateQuestionsForm" class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Atur Jumlah Soal</h3>
                    <form action="{{ route('dosen.exam.generate-questions', $exam) }}" method="POST">
                        @csrf
                        @if($exam->tipe === 'pilgan')
                            <div>
                                <label for="jumlah_pilgan" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Soal Pilihan Ganda *</label>
                                <select id="jumlah_pilgan" 
                                        name="jumlah_pilgan"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                    <option value="10">10 Soal</option>
                                    <option value="15">15 Soal</option>
                                    <option value="20">20 Soal</option>
                                    <option value="25">25 Soal</option>
                                    <option value="30">30 Soal</option>
                                </select>
                            </div>
                        @elseif($exam->tipe === 'essay')
                            <div>
                                <label for="jumlah_essay" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Soal Essay *</label>
                                <select id="jumlah_essay" 
                                        name="jumlah_essay"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                    <option value="5">5 Soal</option>
                                    <option value="10">10 Soal</option>
                                    <option value="15">15 Soal</option>
                                    <option value="20">20 Soal</option>
                                    <option value="25">25 Soal</option>
                                    <option value="30">30 Soal</option>
                                </select>
                            </div>
                        @elseif($exam->tipe === 'campuran')
                            <div class="space-y-4">
                                <div>
                                    <label for="jumlah_essay" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Soal Essay *</label>
                                    <select id="jumlah_essay" 
                                            name="jumlah_essay"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                        <option value="5" selected>5 Soal</option>
                                        <option value="10">10 Soal</option>
                                        <option value="15">15 Soal</option>
                                        <option value="20">20 Soal</option>
                                        <option value="25">25 Soal</option>
                                        <option value="30">30 Soal</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="jumlah_pilgan" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Soal Pilihan Ganda *</label>
                                    <select id="jumlah_pilgan" 
                                            name="jumlah_pilgan"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                        <option value="10" selected>10 Soal</option>
                                        <option value="15">15 Soal</option>
                                        <option value="20">20 Soal</option>
                                        <option value="25">25 Soal</option>
                                        <option value="30">30 Soal</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="mt-4 flex items-center justify-end space-x-4 pt-4 border-t border-blue-200">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                                Generate Soal
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Add Question Form -->
                <div id="addQuestionForm" class="hidden mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Soal Baru</h3>
                    <form action="{{ route('dosen.exam.add-question', $exam) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="tipe" class="block text-sm font-medium text-gray-700 mb-2">Tipe Soal *</label>
                                <select id="tipe" 
                                        name="tipe"
                                        onchange="toggleQuestionType()"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                    <option value="pilgan">Pilihan Ganda</option>
                                    <option value="essay">Essay</option>
                                </select>
                            </div>

                            <div>
                                <label for="pertanyaan" class="block text-sm font-medium text-gray-700 mb-2">Pertanyaan *</label>
                                <textarea id="pertanyaan" 
                                          name="pertanyaan" 
                                          rows="4"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          required></textarea>
                            </div>

                            <!-- Pilihan untuk Pilgan -->
                            <div id="pilganOptions">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Jawaban *</label>
                                <div class="space-y-2">
                                    @foreach(['A', 'B', 'C', 'D', 'E'] as $option)
                                    <div class="flex items-center space-x-2">
                                        <span class="w-8 text-sm font-medium text-gray-700">{{ $option }}.</span>
                                        <input type="text" 
                                               name="pilihan[{{ $option }}]" 
                                               placeholder="Pilihan {{ $option }}"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <label for="jawaban_benar" class="block text-sm font-medium text-gray-700 mb-2">Jawaban Benar *</label>
                                    <select id="jawaban_benar" 
                                            name="jawaban_benar"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                        <option value="">Pilih Jawaban Benar</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Kunci Jawaban untuk Essay -->
                            <div id="essayOptions" style="display: none;">
                                <label for="jawaban_benar_essay" class="block text-sm font-medium text-gray-700 mb-2">Kunci Jawaban (Opsional)</label>
                                <textarea id="jawaban_benar_essay" 
                                          name="jawaban_benar_essay" 
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>

                            <div>
                                <label for="bobot" class="block text-sm font-medium text-gray-700 mb-2">Bobot Soal *</label>
                                <input type="number" 
                                       id="bobot" 
                                       name="bobot" 
                                       value="1"
                                       min="0"
                                       step="0.01"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                            </div>

                            <div>
                                <label for="penjelasan" class="block text-sm font-medium text-gray-700 mb-2">Penjelasan (Opsional)</label>
                                <textarea id="penjelasan" 
                                          name="penjelasan" 
                                          rows="2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Penjelasan akan ditampilkan setelah ujian selesai"></textarea>
                            </div>

                            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                                <button type="button" 
                                        onclick="toggleAddQuestionForm()"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                                    Simpan Soal
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Questions List -->
                <div class="space-y-4">
                    @forelse($exam->questions as $index => $question)
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200" id="question-{{ $question->id }}">
                            <!-- View Mode -->
                            <div id="view-{{ $question->id }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span class="text-sm font-medium text-blue-600">Soal {{ $index + 1 }}</span>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                {{ $question->tipe === 'pilgan' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($question->tipe) }}
                                            </span>
                                            <span class="text-xs text-gray-500">Bobot: {{ $question->bobot }}</span>
                                        </div>
                                        <p class="text-gray-900 mb-2">{{ $question->pertanyaan }}</p>
                                        
                                        @if($question->isPilgan())
                                            <div class="mt-2 space-y-1">
                                                @foreach($question->pilihan ?? [] as $key => $value)
                                                    <p class="text-sm text-gray-600">
                                                        <span class="font-medium {{ $key === $question->jawaban_benar ? 'text-green-600' : '' }}">{{ $key }}.</span> {{ $value }}
                                                        @if($key === $question->jawaban_benar)
                                                            <span class="ml-2 text-xs text-green-600 font-medium">✓ Benar</span>
                                                        @endif
                                                    </p>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button onclick="toggleEditQuestion({{ $question->id }})" 
                                                class="p-2 text-gray-900 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer"
                                                title="Edit Soal">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <form action="{{ route('dosen.exam.delete-question', ['exam' => $exam, 'question' => $question]) }}" 
                                              method="POST" 
                                              class="inline delete-form"
                                              data-title="Hapus Soal"
                                              data-message="Apakah Anda yakin ingin menghapus soal ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors delete-btn cursor-pointer"
                                                    title="Hapus Soal">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <div id="edit-{{ $question->id }}" style="display: none;">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Edit Soal {{ $index + 1 }}</h4>
                                
                                @if($errors->any() && session('error_question_id') == $question->id)
                                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">Mohon perbaiki kesalahan berikut:</h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <ul class="list-disc list-inside space-y-1">
                                                        @foreach($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <form action="{{ route('dosen.exam.update-question', ['exam' => $exam, 'question' => $question]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="question_id" value="{{ $question->id }}">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="pertanyaan_{{ $question->id }}" class="block text-sm font-medium text-gray-700 mb-2">Pertanyaan *</label>
                                            <textarea id="pertanyaan_{{ $question->id }}" 
                                                      name="pertanyaan" 
                                                      rows="4"
                                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pertanyaan') border-red-500 @enderror"
                                                      required>{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
                                            @error('pertanyaan')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        @if($question->isPilgan())
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Jawaban *</label>
                                                <div class="space-y-2">
                                                    @foreach(['A', 'B', 'C', 'D', 'E'] as $option)
                                                    <div class="flex items-center space-x-2">
                                                        <span class="w-8 text-sm font-medium text-gray-700">{{ $option }}.</span>
                                                        <input type="text" 
                                                               name="pilihan[{{ $option }}]" 
                                                               value="{{ old('pilihan.' . $option, $question->pilihan[$option] ?? '') }}"
                                                               placeholder="Pilihan {{ $option }}"
                                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pilihan.' . $option) border-red-500 @enderror"
                                                               required>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @error('pilihan')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                                <div class="mt-2">
                                                    <label for="jawaban_benar_{{ $question->id }}" class="block text-sm font-medium text-gray-700 mb-2">Jawaban Benar *</label>
                                                    <select id="jawaban_benar_{{ $question->id }}" 
                                                            name="jawaban_benar"
                                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jawaban_benar') border-red-500 @enderror"
                                                            required>
                                                        <option value="">Pilih Jawaban Benar</option>
                                                        @foreach(['A', 'B', 'C', 'D', 'E'] as $option)
                                                            <option value="{{ $option }}" {{ old('jawaban_benar', $question->jawaban_benar) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('jawaban_benar')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <label for="jawaban_benar_essay_{{ $question->id }}" class="block text-sm font-medium text-gray-700 mb-2">Kunci Jawaban (Opsional)</label>
                                                <textarea id="jawaban_benar_essay_{{ $question->id }}" 
                                                          name="jawaban_benar_essay" 
                                                          rows="3"
                                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jawaban_benar_essay') border-red-500 @enderror">{{ old('jawaban_benar_essay', $question->jawaban_benar_essay) }}</textarea>
                                                @error('jawaban_benar_essay')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif

                                        <div>
                                            <label for="bobot_{{ $question->id }}" class="block text-sm font-medium text-gray-700 mb-2">Bobot Soal *</label>
                                            <input type="number" 
                                                   id="bobot_{{ $question->id }}" 
                                                   name="bobot" 
                                                   value="{{ old('bobot', $question->bobot) }}"
                                                   min="0"
                                                   step="0.01"
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bobot') border-red-500 @enderror"
                                                   required>
                                            @error('bobot')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="penjelasan_{{ $question->id }}" class="block text-sm font-medium text-gray-700 mb-2">Penjelasan (Opsional)</label>
                                            <textarea id="penjelasan_{{ $question->id }}" 
                                                      name="penjelasan" 
                                                      rows="2"
                                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('penjelasan') border-red-500 @enderror">{{ old('penjelasan', $question->penjelasan) }}</textarea>
                                            @error('penjelasan')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                                            <button type="button" 
                                                    onclick="toggleEditQuestion({{ $question->id }})"
                                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                                                Batal
                                            </button>
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>Belum ada soal. Tambahkan soal untuk memulai.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Total Soal</span>
                        <p class="text-2xl font-bold text-gray-900">{{ $exam->total_soal }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Yang Sudah Mengerjakan</span>
                        <p class="text-2xl font-bold text-gray-900">{{ $exam->sessions->where('status', '!=', 'started')->count() }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Sedang Mengerjakan</span>
                        <p class="text-2xl font-bold text-blue-600">{{ $exam->sessions->where('status', 'started')->count() }}</p>
                    </div>
                </div>
                @php
                    $activeCount = $exam->sessions->where('status', 'started')->whereNull('finished_at')->count();
                    $isOngoing = $exam->isOngoing();
                @endphp
                @if($isOngoing && $activeCount > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('dosen.exam.active-students', $exam) }}" 
                       class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center text-sm font-medium block cursor-pointer">
                        Lihat Mahasiswa yang Sedang Mengerjakan ({{ $activeCount }})
                    </a>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan</h3>
                <div class="space-y-2 text-sm">
                    @if($exam->prevent_copy_paste)
                        <p class="text-gray-600">✓ Copy/Paste dilarang</p>
                    @endif
                    @if($exam->prevent_new_tab)
                        <p class="text-gray-600">✓ Tab switch detection aktif</p>
                    @endif
                    @if($exam->fullscreen_mode)
                        <p class="text-gray-600">✓ Fullscreen mode wajib</p>
                    @endif
                    @if($exam->random_soal)
                        <p class="text-gray-600">✓ Soal diacak</p>
                    @endif
                    @if($exam->random_pilihan)
                        <p class="text-gray-600">✓ Pilihan diacak</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAddQuestionForm() {
    const form = document.getElementById('addQuestionForm');
    form.classList.toggle('hidden');
}

function toggleGenerateQuestionsForm() {
    const form = document.getElementById('generateQuestionsForm');
    if (form) {
        form.classList.toggle('hidden');
    }
}

function toggleQuestionType() {
    const tipe = document.getElementById('tipe').value;
    const pilganOptions = document.getElementById('pilganOptions');
    const essayOptions = document.getElementById('essayOptions');
    const jawabanBenar = document.getElementById('jawaban_benar');
    const pilihanInputs = pilganOptions.querySelectorAll('input[type="text"]');
    
    if (tipe === 'pilgan') {
        pilganOptions.style.display = 'block';
        essayOptions.style.display = 'none';
        jawabanBenar.required = true;
        pilihanInputs.forEach(input => input.required = true);
    } else {
        pilganOptions.style.display = 'none';
        essayOptions.style.display = 'block';
        jawabanBenar.required = false;
        pilihanInputs.forEach(input => input.required = false);
    }
}

function toggleEditQuestion(questionId) {
    const viewDiv = document.getElementById('view-' + questionId);
    const editDiv = document.getElementById('edit-' + questionId);
    
    if (viewDiv.style.display === 'none') {
        viewDiv.style.display = 'block';
        editDiv.style.display = 'none';
    } else {
        viewDiv.style.display = 'none';
        editDiv.style.display = 'block';
    }
}

// Auto-open edit form if there's an error
document.addEventListener('DOMContentLoaded', function() {
    @if(session('error_question_id'))
        const errorQuestionId = {{ session('error_question_id') }};
        toggleEditQuestion(errorQuestionId);
        // Scroll to the question
        const editDiv = document.getElementById('edit-' + errorQuestionId);
        if (editDiv) {
            editDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    @endif
});

// Delete question handler
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-form');
            const title = form.getAttribute('data-title');
            const message = form.getAttribute('data-message');
            
            showConfirm(
                title,
                message,
                function() {
                    form.submit();
                },
                function() {
                    closeUniversalModal();
                }
            );
        });
    });
});
</script>
@endsection

