<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use App\Models\ExamSession;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Get list of exams for mahasiswa
     */
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        // Get exams from approved KRS
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.exams' => function($query) {
                $query->where('status', 'published')
                    ->orderBy('selesai', 'asc');
            }, 'jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
            ->get();

        $exams = collect();
        foreach ($krs as $k) {
            foreach ($k->jadwalKuliah->exams as $exam) {
                $session = ExamSession::where('exam_id', $exam->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->first();
                
                $exams->push([
                    'id' => $exam->id,
                    'judul' => $exam->judul,
                    'deskripsi' => $exam->deskripsi,
                    'tipe' => $exam->tipe,
                    'durasi' => $exam->durasi,
                    'mulai' => $exam->mulai?->toISOString(),
                    'selesai' => $exam->selesai?->toISOString(),
                    'total_soal' => $exam->total_soal,
                    'bobot' => $exam->bobot,
                    'mata_kuliah' => $k->jadwalKuliah->mataKuliah->nama ?? null,
                    'kode_mk' => $k->jadwalKuliah->mataKuliah->kode_mk ?? null,
                    'dosen' => $k->jadwalKuliah->dosen->nama ?? null,
                    'is_ongoing' => $exam->isOngoing(),
                    'is_finished' => $exam->isFinished(),
                    'session' => $session ? [
                        'id' => $session->id,
                        'status' => $session->status,
                        'started_at' => $session->started_at?->toISOString(),
                        'finished_at' => $session->finished_at?->toISOString(),
                        'nilai' => $session->nilai,
                    ] : null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $exams->values()
        ]);
    }

    /**
     * Get exam details
     */
    public function show(Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        // Check enrollment
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $exam->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar pada mata kuliah ini',
            ], 403);
        }

        $session = ExamSession::where('exam_id', $exam->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        $exam->load(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen']);

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
                    'mata_kuliah' => $exam->jadwalKuliah->mataKuliah->nama ?? null,
                    'kode_mk' => $exam->jadwalKuliah->mataKuliah->kode_mk ?? null,
                    'dosen' => $exam->jadwalKuliah->dosen->nama ?? null,
                    'is_ongoing' => $exam->isOngoing(),
                    'is_finished' => $exam->isFinished(),
                ],
                'session' => $session ? [
                    'id' => $session->id,
                    'status' => $session->status,
                    'started_at' => $session->started_at?->toISOString(),
                    'finished_at' => $session->finished_at?->toISOString(),
                    'waktu_tersisa' => $session->waktu_tersisa,
                    'nilai' => $session->nilai,
                ] : null,
            ],
        ]);
    }

    /**
     * Start exam - create session
     */
    public function start(Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        // Check enrollment
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $exam->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar pada mata kuliah ini',
            ], 403);
        }

        // Check if exam is published
        if (!$exam->isPublished()) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian belum dipublikasikan',
            ], 422);
        }

        // Check if exam has started
        if (!$exam->isOngoing() && !$exam->isFinished()) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian belum dimulai',
            ], 422);
        }

        // Check if already has active session
        $existingSession = ExamSession::where('exam_id', $exam->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'started')
            ->whereNull('finished_at')
            ->first();

        if ($existingSession) {
            return response()->json([
                'success' => true,
                'message' => 'Session sudah ada',
                'data' => [
                    'session_id' => $existingSession->id,
                ],
            ]);
        }

        // Create new session
        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'mahasiswa_id' => $mahasiswa->id,
            'started_at' => now(),
            'waktu_tersisa' => $exam->durasi * 60, // Convert minutes to seconds
            'status' => 'started',
        ]);

        // Create answer records for all questions
        $questions = $exam->questions()->get();
        
        // Randomize if needed
        if ($exam->random_soal) {
            $questions = $questions->shuffle();
        }

        foreach ($questions as $index => $question) {
            ExamAnswer::create([
                'exam_session_id' => $session->id,
                'exam_question_id' => $question->id,
                'is_answered' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ujian dimulai',
            'data' => [
                'session_id' => $session->id,
            ],
        ], 201);
    }

    /**
     * Get exam questions for taking exam
     */
    public function take(Exam $exam, ExamSession $session)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        // Verify session
        if ($session->mahasiswa_id !== $mahasiswa->id || $session->exam_id !== $exam->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check if session is active
        if (!$session->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak aktif',
            ], 422);
        }

        // Calculate time remaining
        $now = now();
        $elapsed = $session->started_at->diffInSeconds($now);
        $remaining = ($exam->durasi * 60) - $elapsed;

        if ($remaining <= 0) {
            // Auto-submit
            $this->autoSubmit($exam, $session);
            return response()->json([
                'success' => false,
                'message' => 'Waktu ujian telah habis',
            ], 422);
        }

        $session->update(['waktu_tersisa' => $remaining]);

        // Get questions with answers
        $questions = $exam->questions()->with(['answers' => function($query) use ($session) {
            $query->where('exam_session_id', $session->id);
        }])->get();

        // Randomize if needed
        if ($exam->random_soal) {
            $questions = $questions->shuffle();
        }

        // Randomize choices if needed (for pilgan)
        $questionsData = [];
        foreach ($questions as $question) {
            $answer = $question->answers->first();
            $pilihan = $question->pilihan ?? [];
            
            if ($exam->random_pilihan && $question->isPilgan() && !empty($pilihan)) {
                $keys = array_keys($pilihan);
                shuffle($keys);
                $shuffled = [];
                foreach ($keys as $key) {
                    $shuffled[$key] = $pilihan[$key];
                }
                $pilihan = $shuffled;
            }

            $questionsData[] = [
                'id' => $question->id,
                'tipe' => $question->tipe,
                'pertanyaan' => $question->pertanyaan,
                'pilihan' => $pilihan,
                'bobot' => $question->bobot,
                'urutan' => $question->urutan,
                'answer' => $answer ? [
                    'id' => $answer->id,
                    'jawaban_pilgan' => $answer->jawaban_pilgan,
                    'jawaban_essay' => $answer->jawaban_essay,
                    'is_answered' => $answer->is_answered,
                ] : null,
            ];
        }

        $exam->load(['jadwalKuliah.mataKuliah']);

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => [
                    'id' => $exam->id,
                    'judul' => $exam->judul,
                    'durasi' => $exam->durasi,
                    'total_soal' => $exam->total_soal,
                ],
                'session' => [
                    'id' => $session->id,
                    'started_at' => $session->started_at->toISOString(),
                    'waktu_tersisa' => $remaining,
                ],
                'questions' => $questionsData,
            ],
        ]);
    }

    /**
     * Save answer
     */
    public function saveAnswer(Request $request, Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
            'question_id' => 'required|exists:exam_questions,id',
            'jawaban_pilgan' => 'nullable|string',
            'jawaban_essay' => 'nullable|string',
        ]);

        $session = ExamSession::where('id', $validated['session_id'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('exam_id', $exam->id)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak ditemukan',
            ], 404);
        }

        if (!$session->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak aktif',
            ], 422);
        }

        $answer = ExamAnswer::where('exam_session_id', $session->id)
            ->where('exam_question_id', $validated['question_id'])
            ->first();

        if (!$answer) {
            return response()->json([
                'success' => false,
                'message' => 'Answer tidak ditemukan',
            ], 404);
        }

        $updateData = [
            'is_answered' => true,
        ];

        if (isset($validated['jawaban_pilgan'])) {
            $updateData['jawaban_pilgan'] = $validated['jawaban_pilgan'];
        }

        if (isset($validated['jawaban_essay'])) {
            $updateData['jawaban_essay'] = $validated['jawaban_essay'];
        }

        $answer->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban tersimpan',
        ]);
    }

    /**
     * Submit exam
     */
    public function submit(Request $request, Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $session = ExamSession::where('id', $validated['session_id'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('exam_id', $exam->id)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak ditemukan',
            ], 404);
        }

        if (!$session->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak aktif',
            ], 422);
        }

        // Calculate score
        $this->calculateScore($exam, $session);

        $session->update([
            'status' => 'submitted',
            'finished_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil disubmit',
            'data' => [
                'session_id' => $session->id,
                'nilai' => $session->nilai,
            ],
        ]);
    }

    /**
     * Get exam result
     */
    public function result(Exam $exam, ExamSession $session)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        if ($session->mahasiswa_id !== $mahasiswa->id || $session->exam_id !== $exam->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $exam->load('jadwalKuliah.mataKuliah');

        // Get questions with answers
        $questions = $exam->questions()->with(['answers' => function($query) use ($session) {
            $query->where('exam_session_id', $session->id);
        }])->orderBy('urutan')->get();

        $questionsData = [];
        foreach ($questions as $question) {
            $answer = $question->answers->first();
            $questionsData[] = [
                'id' => $question->id,
                'tipe' => $question->tipe,
                'pertanyaan' => $question->pertanyaan,
                'pilihan' => $question->pilihan ?? [],
                'jawaban_benar' => $question->jawaban_benar,
                'bobot' => $question->bobot,
                'urutan' => $question->urutan,
                'penjelasan' => $question->penjelasan,
                'answer' => $answer ? [
                    'id' => $answer->id,
                    'jawaban_pilgan' => $answer->jawaban_pilgan,
                    'jawaban_essay' => $answer->jawaban_essay,
                    'nilai' => $answer->nilai,
                    'is_answered' => $answer->is_answered,
                ] : null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => [
                    'id' => $exam->id,
                    'judul' => $exam->judul,
                    'mata_kuliah' => $exam->jadwalKuliah->mataKuliah->nama ?? null,
                    'tampilkan_nilai' => $exam->tampilkan_nilai,
                ],
                'session' => [
                    'id' => $session->id,
                    'status' => $session->status,
                    'started_at' => $session->started_at?->toISOString(),
                    'finished_at' => $session->finished_at?->toISOString(),
                    'nilai' => $session->nilai,
                ],
                'questions' => $questionsData,
            ],
        ]);
    }

    // Helper: Auto-submit when time runs out
    private function autoSubmit(Exam $exam, ExamSession $session)
    {
        $this->calculateScore($exam, $session);

        $session->update([
            'status' => 'auto_submitted',
            'finished_at' => now(),
        ]);
    }

    // Helper: Calculate score for pilgan questions
    private function calculateScore(Exam $exam, ExamSession $session)
    {
        $totalScore = 0;
        $totalBobot = 0;

        foreach ($session->answers as $answer) {
            $question = $answer->examQuestion;
            
            if ($question->isPilgan() && $answer->is_answered) {
                // Auto-check pilgan
                $isCorrect = ($answer->jawaban_pilgan === $question->jawaban_benar);
                $nilai = $isCorrect ? $question->bobot : 0;
                
                $answer->update(['nilai' => $nilai]);
                
                $totalScore += $nilai;
            }
            
            $totalBobot += $question->bobot;
        }

        // Calculate final score (percentage)
        $finalScore = $totalBobot > 0 ? ($totalScore / $totalBobot) * 100 : 0;
        
        $session->update(['nilai' => $finalScore]);
    }
}
