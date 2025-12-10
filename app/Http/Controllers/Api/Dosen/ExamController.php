<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSession;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Get list of jadwal kuliah and exams
     */
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        $jadwal_id = request('jadwal_id');
        
        $jadwals = JadwalKuliah::where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->orderBy('semester_id', 'desc')
            ->orderBy('hari', 'asc')
            ->get();

        $exams = collect();
        
        if ($jadwal_id) {
            $exams = Exam::where('jadwal_kuliah_id', $jadwal_id)
                ->where('dosen_id', $dosen->id)
                ->with(['jadwalKuliah.mataKuliah', 'questions'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($exam) {
                    return [
                        'id' => $exam->id,
                        'judul' => $exam->judul,
                        'deskripsi' => $exam->deskripsi,
                        'tipe' => $exam->tipe,
                        'durasi' => $exam->durasi,
                        'mulai' => $exam->mulai?->toISOString(),
                        'selesai' => $exam->selesai?->toISOString(),
                        'total_soal' => $exam->total_soal,
                        'bobot' => $exam->bobot,
                        'status' => $exam->status,
                        'mata_kuliah' => $exam->jadwalKuliah->mataKuliah->nama ?? null,
                        'is_ongoing' => $exam->isOngoing(),
                        'is_finished' => $exam->isFinished(),
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'data' => [
                'jadwals' => $jadwals->map(function($jadwal) {
                    return [
                        'id' => $jadwal->id,
                        'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                        'kode_mk' => $jadwal->mataKuliah->kode_mk ?? null,
                        'semester' => $jadwal->semester->nama ?? null,
                        'hari' => $jadwal->hari,
                        'jam' => $jadwal->jam,
                    ];
                }),
                'exams' => $exams->values(),
                'selected_jadwal_id' => $jadwal_id,
            ],
        ]);
    }

    /**
     * Get exam details with questions
     */
    public function show(Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $exam->load([
            'jadwalKuliah.mataKuliah',
            'questions' => function($query) {
                $query->orderBy('urutan');
            },
        ]);

        $questions = $exam->questions->map(function($question) {
            return [
                'id' => $question->id,
                'tipe' => $question->tipe,
                'pertanyaan' => $question->pertanyaan,
                'pilihan' => $question->pilihan ?? [],
                'jawaban_benar' => $question->jawaban_benar,
                'jawaban_benar_essay' => $question->jawaban_benar_essay,
                'bobot' => $question->bobot,
                'urutan' => $question->urutan,
                'penjelasan' => $question->penjelasan,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => [
                    'id' => $exam->id,
                    'judul' => $exam->judul,
                    'deskripsi' => $exam->deskripsi,
                    'tipe' => $exam->tipe,
                    'durasi' => $exam->durasi,
                    'mulai' => $exam->mulai?->toISOString(),
                    'selesai' => $exam->selesai?->toISOString(),
                    'total_soal' => $exam->total_soal,
                    'bobot' => $exam->bobot,
                    'random_soal' => $exam->random_soal,
                    'random_pilihan' => $exam->random_pilihan,
                    'tampilkan_nilai' => $exam->tampilkan_nilai,
                    'prevent_copy_paste' => $exam->prevent_copy_paste,
                    'prevent_new_tab' => $exam->prevent_new_tab,
                    'fullscreen_mode' => $exam->fullscreen_mode,
                    'status' => $exam->status,
                    'mata_kuliah' => $exam->jadwalKuliah->mataKuliah->nama ?? null,
                    'kode_mk' => $exam->jadwalKuliah->mataKuliah->kode_mk ?? null,
                    'is_ongoing' => $exam->isOngoing(),
                    'is_finished' => $exam->isFinished(),
                ],
                'questions' => $questions->values(),
            ],
        ]);
    }

    /**
     * Create new exam
     */
    public function store(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'jadwal_kuliah_id' => 'required|exists:jadwal_kuliahs,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:pilgan,essay,campuran',
            'durasi' => 'required|integer|min:1|max:600',
            'mulai' => 'nullable|date',
            'selesai' => 'required|date',
            'bobot' => 'required|numeric|min:0|max:100',
            'random_soal' => 'nullable|boolean',
            'random_pilihan' => 'nullable|boolean',
            'tampilkan_nilai' => 'nullable|boolean',
            'prevent_copy_paste' => 'nullable|boolean',
            'prevent_new_tab' => 'nullable|boolean',
            'fullscreen_mode' => 'nullable|boolean',
            'status' => 'required|in:draft,published',
        ]);

        // Verify jadwal belongs to this dosen
        $jadwal = JadwalKuliah::where('id', $validated['jadwal_kuliah_id'])
            ->where('dosen_id', $dosen->id)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal kuliah tidak ditemukan',
            ], 404);
        }

        // Additional validation
        if ($validated['mulai'] && $validated['selesai']) {
            if (\Carbon\Carbon::parse($validated['selesai'])->lte(\Carbon\Carbon::parse($validated['mulai']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu selesai harus setelah waktu mulai',
                ], 422);
            }
        }

        $validated['dosen_id'] = $dosen->id;
        $validated['total_soal'] = 0;
        $validated['random_soal'] = $request->has('random_soal') ? true : false;
        $validated['random_pilihan'] = $request->has('random_pilihan') ? true : false;
        $validated['tampilkan_nilai'] = $request->has('tampilkan_nilai') ? true : false;
        $validated['prevent_copy_paste'] = $request->has('prevent_copy_paste') ? true : false;
        $validated['prevent_new_tab'] = $request->has('prevent_new_tab') ? true : false;
        $validated['fullscreen_mode'] = $request->has('fullscreen_mode') ? true : false;

        $exam = Exam::create($validated);

        // Create default violation rules
        \App\Models\ExamViolationRule::create([
            'exam_id' => $exam->id,
            ...\App\Models\ExamViolationRule::getDefaults(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil dibuat',
            'data' => [
                'id' => $exam->id,
                'judul' => $exam->judul,
            ],
        ], 201);
    }

    /**
     * Update exam
     */
    public function update(Request $request, Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Can't edit if exam has started
        if ($exam->sessions()->where('status', '!=', 'started')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian tidak dapat diedit karena sudah ada mahasiswa yang mengerjakan',
            ], 422);
        }

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe' => 'sometimes|required|in:pilgan,essay,campuran',
            'durasi' => 'sometimes|required|integer|min:1|max:600',
            'mulai' => 'nullable|date',
            'selesai' => 'sometimes|required|date',
            'bobot' => 'sometimes|required|numeric|min:0|max:100',
            'random_soal' => 'nullable|boolean',
            'random_pilihan' => 'nullable|boolean',
            'tampilkan_nilai' => 'nullable|boolean',
            'prevent_copy_paste' => 'nullable|boolean',
            'prevent_new_tab' => 'nullable|boolean',
            'fullscreen_mode' => 'nullable|boolean',
            'status' => 'sometimes|required|in:draft,published',
        ]);

        // Additional validation
        if (isset($validated['mulai']) && isset($validated['selesai'])) {
            if (\Carbon\Carbon::parse($validated['selesai'])->lte(\Carbon\Carbon::parse($validated['mulai']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu selesai harus setelah waktu mulai',
                ], 422);
            }
        }

        if (isset($validated['random_soal'])) {
            $validated['random_soal'] = $request->has('random_soal') ? true : false;
        }
        if (isset($validated['random_pilihan'])) {
            $validated['random_pilihan'] = $request->has('random_pilihan') ? true : false;
        }
        if (isset($validated['tampilkan_nilai'])) {
            $validated['tampilkan_nilai'] = $request->has('tampilkan_nilai') ? true : false;
        }
        if (isset($validated['prevent_copy_paste'])) {
            $validated['prevent_copy_paste'] = $request->has('prevent_copy_paste') ? true : false;
        }
        if (isset($validated['prevent_new_tab'])) {
            $validated['prevent_new_tab'] = $request->has('prevent_new_tab') ? true : false;
        }
        if (isset($validated['fullscreen_mode'])) {
            $validated['fullscreen_mode'] = $request->has('fullscreen_mode') ? true : false;
        }

        $exam->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil diperbarui',
            'data' => [
                'id' => $exam->id,
                'judul' => $exam->judul,
            ],
        ]);
    }

    /**
     * Delete exam
     */
    public function destroy(Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Can't delete if exam has sessions
        if ($exam->sessions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian tidak dapat dihapus karena sudah ada mahasiswa yang mengerjakan',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Delete questions
            $exam->questions()->delete();
            
            // Delete violation rules
            $exam->violationRule()->delete();
            
            // Delete exam
            $exam->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ujian berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus ujian: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add question to exam
     */
    public function addQuestion(Request $request, Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'tipe' => 'required|in:pilgan,essay',
            'pertanyaan' => 'required|string',
            'pilihan' => 'required_if:tipe,pilgan|array',
            'pilihan.*' => 'required|string',
            'jawaban_benar' => 'required_if:tipe,pilgan|string',
            'jawaban_benar_essay' => 'nullable|string',
            'bobot' => 'required|numeric|min:0',
            'penjelasan' => 'nullable|string',
        ]);

        $maxUrutan = $exam->questions()->max('urutan') ?? 0;

        $question = ExamQuestion::create([
            'exam_id' => $exam->id,
            'tipe' => $validated['tipe'],
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan' => $validated['tipe'] === 'pilgan' ? $validated['pilihan'] : null,
            'jawaban_benar' => $validated['tipe'] === 'pilgan' ? $validated['jawaban_benar'] : null,
            'jawaban_benar_essay' => $validated['tipe'] === 'essay' ? ($validated['jawaban_benar_essay'] ?? null) : null,
            'bobot' => $validated['bobot'],
            'urutan' => $maxUrutan + 1,
            'penjelasan' => $validated['penjelasan'] ?? null,
        ]);

        $exam->load('questions');
        $exam->update(['total_soal' => $exam->questions->count()]);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil ditambahkan',
            'data' => [
                'id' => $question->id,
                'pertanyaan' => $question->pertanyaan,
            ],
        ], 201);
    }

    /**
     * Update question
     */
    public function updateQuestion(Request $request, Exam $exam, ExamQuestion $question)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id || $question->exam_id !== $exam->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'tipe' => 'sometimes|required|in:pilgan,essay',
            'pertanyaan' => 'sometimes|required|string',
            'pilihan' => 'required_if:tipe,pilgan|array',
            'pilihan.*' => 'required|string',
            'jawaban_benar' => 'required_if:tipe,pilgan|string',
            'jawaban_benar_essay' => 'nullable|string',
            'bobot' => 'sometimes|required|numeric|min:0',
            'penjelasan' => 'nullable|string',
        ]);

        $question->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil diperbarui',
            'data' => [
                'id' => $question->id,
                'pertanyaan' => $question->pertanyaan,
            ],
        ]);
    }

    /**
     * Delete question
     */
    public function deleteQuestion(Exam $exam, ExamQuestion $question)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id || $question->exam_id !== $exam->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $question->delete();

        $exam->load('questions');
        $exam->update(['total_soal' => $exam->questions->count()]);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil dihapus',
        ]);
    }

    /**
     * Get exam results
     */
    public function results(Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $sessions = ExamSession::where('exam_id', $exam->id)
            ->with(['mahasiswa', 'answers'])
            ->orderBy('finished_at', 'desc')
            ->get()
            ->map(function($session) {
                return [
                    'id' => $session->id,
                    'mahasiswa' => [
                        'id' => $session->mahasiswa->id,
                        'nama' => $session->mahasiswa->nama,
                        'nim' => $session->mahasiswa->nim,
                    ],
                    'started_at' => $session->started_at?->toISOString(),
                    'finished_at' => $session->finished_at?->toISOString(),
                    'status' => $session->status,
                    'nilai' => $session->nilai,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => [
                    'id' => $exam->id,
                    'judul' => $exam->judul,
                    'mata_kuliah' => $exam->jadwalKuliah->mataKuliah->nama ?? null,
                ],
                'sessions' => $sessions->values(),
            ],
        ]);
    }

    /**
     * Get exam session details for grading
     */
    public function showGradeSession(Exam $exam, ExamSession $session)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id || $session->exam_id !== $exam->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $exam->load(['jadwalKuliah.mataKuliah', 'questions']);
        $session->load(['mahasiswa', 'answers.examQuestion']);

        // Get essay answers only
        $essayAnswers = $session->answers->filter(function($answer) {
            return $answer->examQuestion->isEssay();
        })->map(function($answer) {
            return [
                'id' => $answer->id,
                'question' => [
                    'id' => $answer->examQuestion->id,
                    'pertanyaan' => $answer->examQuestion->pertanyaan,
                    'bobot' => $answer->examQuestion->bobot,
                ],
                'jawaban_essay' => $answer->jawaban_essay,
                'nilai' => $answer->nilai,
                'feedback' => $answer->feedback,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => [
                    'id' => $exam->id,
                    'judul' => $exam->judul,
                    'mata_kuliah' => $exam->jadwalKuliah->mataKuliah->nama ?? null,
                ],
                'session' => [
                    'id' => $session->id,
                    'mahasiswa' => [
                        'id' => $session->mahasiswa->id,
                        'nama' => $session->mahasiswa->nama,
                        'nim' => $session->mahasiswa->nim,
                    ],
                    'started_at' => $session->started_at?->toISOString(),
                    'finished_at' => $session->finished_at?->toISOString(),
                    'status' => $session->status,
                    'nilai' => $session->nilai,
                ],
                'essay_answers' => $essayAnswers->values(),
            ],
        ]);
    }

    /**
     * Grade exam session (for essay questions)
     */
    public function gradeSession(Request $request, Exam $exam, ExamSession $session)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($exam->dosen_id !== $dosen->id || $session->exam_id !== $exam->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.id' => 'required|exists:exam_answers,id',
            'answers.*.nilai' => 'required|numeric|min:0',
            'answers.*.feedback' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update essay answers
            foreach ($validated['answers'] as $answerData) {
                $answer = $session->answers()->find($answerData['id']);
                if ($answer && $answer->examQuestion->isEssay()) {
                    $answer->nilai = $answerData['nilai'];
                    $answer->feedback = $answerData['feedback'] ?? null;
                    $answer->save();
                }
            }

            // Recalculate total score
            $totalScore = 0;
            $totalBobot = 0;

            foreach ($session->answers as $answer) {
                $question = $answer->examQuestion;
                if ($answer->nilai !== null) {
                    $totalScore += $answer->nilai;
                }
                $totalBobot += $question->bobot;
            }

            $finalScore = $totalBobot > 0 ? ($totalScore / $totalBobot) * 100 : 0;
            $session->update(['nilai' => $finalScore]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil disimpan',
                'data' => [
                    'session_id' => $session->id,
                    'nilai' => $session->nilai,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}

