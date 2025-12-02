<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSession;
use App\Models\ExamAnswer;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();
        
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
                ->get();
        }

        return view('dosen.exam.index', compact('jadwals', 'exams', 'jadwal_id'));
    }

    public function create()
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();
        
        $jadwal_id = request('jadwal_id');
        
        if (!$jadwal_id) {
            return redirect()->route('dosen.exam.index')
                ->with('error', 'Pilih jadwal kuliah terlebih dahulu');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->firstOrFail();

        return view('dosen.exam.create', compact('jadwal'));
    }

    public function store(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        // Get jadwal_id first to preserve it in redirect if validation fails
        $jadwal_id = $request->input('jadwal_kuliah_id');

        try {
            $validated = $request->validate([
                'jadwal_kuliah_id' => 'required|exists:jadwal_kuliahs,id',
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'tipe' => 'required|in:pilgan,essay,campuran',
                'durasi' => 'required|integer|min:1|max:600', // Max 10 hours
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
            ], [], [
                'jadwal_kuliah_id' => 'jadwal kuliah',
                'judul' => 'judul ujian',
                'tipe' => 'tipe ujian',
                'durasi' => 'durasi',
                'mulai' => 'waktu mulai',
                'selesai' => 'waktu selesai',
                'bobot' => 'bobot nilai',
                'status' => 'status',
            ]);

            // Additional validation: selesai must be after mulai if mulai is set
            if ($validated['mulai'] && $validated['selesai']) {
                if (\Carbon\Carbon::parse($validated['selesai'])->lte(\Carbon\Carbon::parse($validated['mulai']))) {
                    return redirect()->route('dosen.exam.create', ['jadwal_id' => $jadwal_id])
                        ->withInput()
                        ->withErrors(['selesai' => 'Waktu selesai harus setelah waktu mulai']);
                }
            }

            // Verify jadwal belongs to this dosen
            $jadwal = JadwalKuliah::where('id', $validated['jadwal_kuliah_id'])
                ->where('dosen_id', $dosen->id)
                ->firstOrFail();

            $validated['dosen_id'] = $dosen->id;
            $validated['total_soal'] = 0; // Will be updated when questions are added
            $validated['random_soal'] = $request->has('random_soal');
            $validated['random_pilihan'] = $request->has('random_pilihan');
            $validated['tampilkan_nilai'] = $request->has('tampilkan_nilai');
            $validated['prevent_copy_paste'] = $request->has('prevent_copy_paste');
            $validated['prevent_new_tab'] = $request->has('prevent_new_tab');
            $validated['fullscreen_mode'] = $request->has('fullscreen_mode');

            $exam = Exam::create($validated);

            return redirect()->route('dosen.exam.show', $exam)
                ->with('success', 'Ujian berhasil dibuat. Silakan tambahkan soal.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation failed - redirect back with input and jadwal_id
            return redirect()->route('dosen.exam.create', ['jadwal_id' => $jadwal_id])
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Mohon perbaiki kesalahan pada form di bawah ini.');
        } catch (\Exception $e) {
            // Other errors
            \Log::error('Error creating exam: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('dosen.exam.create', ['jadwal_id' => $jadwal_id ?? null])
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id) {
            abort(403);
        }

        $exam->load([
            'jadwalKuliah.mataKuliah',
            'questions' => function($query) {
                $query->orderBy('urutan');
            },
            'sessions.mahasiswa'
        ]);

        return view('dosen.exam.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id) {
            abort(403);
        }

        // Can't edit if exam has started
        if ($exam->sessions()->where('status', '!=', 'started')->exists()) {
            return back()->with('error', 'Ujian tidak dapat diedit karena sudah ada mahasiswa yang mengerjakan');
        }

        $exam->load('jadwalKuliah.mataKuliah');

        return view('dosen.exam.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id) {
            abort(403);
        }

        // Can't edit if exam has started
        if ($exam->sessions()->where('status', '!=', 'started')->exists()) {
            return back()->with('error', 'Ujian tidak dapat diedit karena sudah ada mahasiswa yang mengerjakan');
        }

        try {
            $validated = $request->validate([
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
            ], [], [
                'judul' => 'judul ujian',
                'tipe' => 'tipe ujian',
                'durasi' => 'durasi',
                'mulai' => 'waktu mulai',
                'selesai' => 'waktu selesai',
                'bobot' => 'bobot nilai',
                'status' => 'status',
            ]);

            // Additional validation: selesai must be after mulai if mulai is set
            if ($validated['mulai'] && $validated['selesai']) {
                if (\Carbon\Carbon::parse($validated['selesai'])->lte(\Carbon\Carbon::parse($validated['mulai']))) {
                    return redirect()->route('dosen.exam.edit', $exam)
                        ->withInput()
                        ->withErrors(['selesai' => 'Waktu selesai harus setelah waktu mulai']);
                }
            }

            $validated['random_soal'] = $request->has('random_soal');
            $validated['random_pilihan'] = $request->has('random_pilihan');
            $validated['tampilkan_nilai'] = $request->has('tampilkan_nilai');
            $validated['prevent_copy_paste'] = $request->has('prevent_copy_paste');
            $validated['prevent_new_tab'] = $request->has('prevent_new_tab');
            $validated['fullscreen_mode'] = $request->has('fullscreen_mode');

            $exam->update($validated);

            return redirect()->route('dosen.exam.show', $exam)
                ->with('success', 'Ujian berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('dosen.exam.edit', $exam)
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('dosen.exam.edit', $exam)
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id) {
            abort(403);
        }

        // Can't delete if exam has active sessions (started)
        $activeSessions = $exam->sessions()->where('status', 'started')->count();
        if ($activeSessions > 0) {
            return back()->with('error', 'Ujian tidak dapat dihapus karena masih ada mahasiswa yang sedang mengerjakan');
        }

        try {
            DB::beginTransaction();
            
            // Get jadwal_id and exam_id before deletion
            $jadwal_id = $exam->jadwal_kuliah_id;
            $exam_id = $exam->id;
            
            // Get all related IDs first
            $sessionIds = DB::table('exam_sessions')
                ->where('exam_id', $exam_id)
                ->pluck('id')
                ->toArray();
                
            $questionIds = DB::table('exam_questions')
                ->where('exam_id', $exam_id)
                ->pluck('id')
                ->toArray();
            
            // Delete all exam answers (they reference both sessions and questions)
            if (!empty($sessionIds)) {
                DB::table('exam_answers')
                    ->whereIn('exam_session_id', $sessionIds)
                    ->delete();
            }
            
            if (!empty($questionIds)) {
                DB::table('exam_answers')
                    ->whereIn('exam_question_id', $questionIds)
                    ->delete();
            }
            
            // Delete all sessions
            if (!empty($sessionIds)) {
                DB::table('exam_sessions')
                    ->whereIn('id', $sessionIds)
                    ->delete();
            }
            
            // Delete all questions
            if (!empty($questionIds)) {
                DB::table('exam_questions')
                    ->whereIn('id', $questionIds)
                    ->delete();
            }
            
            // Finally delete the exam
            $deleted = DB::table('exams')
                ->where('id', $exam_id)
                ->delete();
            
            if ($deleted === 0) {
                throw new \Exception('Gagal menghapus ujian. Ujian tidak ditemukan atau sudah dihapus.');
            }
            
            DB::commit();

            return redirect()->route('dosen.exam.index', ['jadwal_id' => $jadwal_id])
                ->with('success', 'Ujian berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            \Log::error('Database error deleting exam ID ' . ($exam->id ?? 'unknown') . ': ' . $errorMessage);
            \Log::error('SQL: ' . ($e->getSql() ?? 'N/A'));
            \Log::error('Bindings: ' . json_encode($e->getBindings() ?? []));
            
            // Check for foreign key constraint violation
            if (str_contains($errorMessage, 'foreign key') || str_contains($errorMessage, 'constraint')) {
                return back()->with('error', 'Ujian tidak dapat dihapus karena masih ada data yang terhubung. Silakan hubungi administrator.');
            }
            
            return back()->with('error', 'Terjadi kesalahan database saat menghapus ujian: ' . $errorMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting exam ID ' . ($exam->id ?? 'unknown') . ': ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan saat menghapus ujian: ' . $e->getMessage());
        }
    }

    // Add question to exam
    public function addQuestion(Request $request, Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id) {
            abort(403);
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

        // Get max urutan
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

        // Update total soal - reload relationship first
        $exam->load('questions');
        $exam->update(['total_soal' => $exam->questions->count()]);

        return back()->with('success', 'Soal berhasil ditambahkan');
    }

    // Generate questions based on count
    public function generateQuestions(Request $request, Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id) {
            abort(403);
        }

        // Check if exam already has questions
        if ($exam->questions()->count() > 0) {
            return back()->with('error', 'Ujian sudah memiliki soal. Hapus semua soal terlebih dahulu jika ingin mengatur ulang jumlah soal.');
        }

        $validated = $request->validate([
            'jumlah_pilgan' => 'nullable|integer|min:1|max:100',
            'jumlah_essay' => 'nullable|integer|min:1|max:100',
        ]);

        try {
            DB::beginTransaction();

            $urutan = 1;

            // Generate questions based on exam type
            if ($exam->tipe === 'pilgan') {
                $jumlah = $validated['jumlah_pilgan'] ?? 10;
                for ($i = 1; $i <= $jumlah; $i++) {
                    ExamQuestion::create([
                        'exam_id' => $exam->id,
                        'tipe' => 'pilgan',
                        'pertanyaan' => 'Soal Pilihan Ganda ' . $i . ' - Silakan edit pertanyaan ini',
                        'pilihan' => [
                            'A' => 'Pilihan A - Silakan edit',
                            'B' => 'Pilihan B - Silakan edit',
                            'C' => 'Pilihan C - Silakan edit',
                            'D' => 'Pilihan D - Silakan edit',
                            'E' => 'Pilihan E - Silakan edit',
                        ],
                        'jawaban_benar' => 'A',
                        'bobot' => 1,
                        'urutan' => $urutan++,
                    ]);
                }
            } elseif ($exam->tipe === 'essay') {
                $jumlah = $validated['jumlah_essay'] ?? 5;
                for ($i = 1; $i <= $jumlah; $i++) {
                    ExamQuestion::create([
                        'exam_id' => $exam->id,
                        'tipe' => 'essay',
                        'pertanyaan' => 'Soal Essay ' . $i . ' - Silakan edit pertanyaan ini',
                        'jawaban_benar_essay' => null,
                        'bobot' => 1,
                        'urutan' => $urutan++,
                    ]);
                }
            } elseif ($exam->tipe === 'campuran') {
                // Generate essay questions first
                $jumlahEssay = $validated['jumlah_essay'] ?? 5;
                for ($i = 1; $i <= $jumlahEssay; $i++) {
                    ExamQuestion::create([
                        'exam_id' => $exam->id,
                        'tipe' => 'essay',
                        'pertanyaan' => 'Soal Essay ' . $i . ' - Silakan edit pertanyaan ini',
                        'jawaban_benar_essay' => null,
                        'bobot' => 1,
                        'urutan' => $urutan++,
                    ]);
                }

                // Generate pilgan questions
                $jumlahPilgan = $validated['jumlah_pilgan'] ?? 10;
                for ($i = 1; $i <= $jumlahPilgan; $i++) {
                    ExamQuestion::create([
                        'exam_id' => $exam->id,
                        'tipe' => 'pilgan',
                        'pertanyaan' => 'Soal Pilihan Ganda ' . $i . ' - Silakan edit pertanyaan ini',
                        'pilihan' => [
                            'A' => 'Pilihan A - Silakan edit',
                            'B' => 'Pilihan B - Silakan edit',
                            'C' => 'Pilihan C - Silakan edit',
                            'D' => 'Pilihan D - Silakan edit',
                            'E' => 'Pilihan E - Silakan edit',
                        ],
                        'jawaban_benar' => 'A',
                        'bobot' => 1,
                        'urutan' => $urutan++,
                    ]);
                }
            }

            // Update total soal - reload relationship first
            $exam->load('questions');
            $exam->update(['total_soal' => $exam->questions->count()]);

            DB::commit();

            return redirect()->route('dosen.exam.show', $exam)
                ->with('success', 'Soal berhasil dibuat. Silakan edit setiap soal untuk mengisi pertanyaan dan jawaban.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error generating questions: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat soal: ' . $e->getMessage());
        }
    }

    // Update question
    public function updateQuestion(Request $request, Exam $exam, ExamQuestion $question)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id || $question->exam_id !== $exam->id) {
            abort(403);
        }

        // Build validation rules based on question type
        $rules = [
            'pertanyaan' => 'required|string',
            'bobot' => 'required|numeric|min:0',
            'penjelasan' => 'nullable|string',
        ];

        if ($question->tipe === 'pilgan') {
            $rules['pilihan'] = 'required|array';
            $rules['pilihan.*'] = 'required|string';
            $rules['jawaban_benar'] = 'required|string';
        } else {
            $rules['jawaban_benar_essay'] = 'nullable|string';
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error_question_id', $question->id);
        }

        $updateData = [
            'pertanyaan' => $validated['pertanyaan'],
            'bobot' => $validated['bobot'],
            'penjelasan' => $validated['penjelasan'] ?? null,
        ];

        if ($question->tipe === 'pilgan') {
            $updateData['pilihan'] = $validated['pilihan'];
            $updateData['jawaban_benar'] = $validated['jawaban_benar'];
            $updateData['jawaban_benar_essay'] = null;
        } else {
            $updateData['jawaban_benar_essay'] = $validated['jawaban_benar_essay'] ?? null;
            $updateData['pilihan'] = null;
            $updateData['jawaban_benar'] = null;
        }

        try {
            $question->update($updateData);

            return back()->with('success', 'Soal berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error updating question: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui soal: ' . $e->getMessage()])->with('error_question_id', $question->id);
        }
    }

    // Delete question
    public function deleteQuestion(Exam $exam, ExamQuestion $question)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id || $question->exam_id !== $exam->id) {
            abort(403);
        }

        $question->delete();

        // Update total soal - reload relationship first
        $exam->load('questions');
        $exam->update(['total_soal' => $exam->questions->count()]);

        return back()->with('success', 'Soal berhasil dihapus');
    }

    // View exam results
    public function results(Exam $exam)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id) {
            abort(403);
        }

        $sessions = ExamSession::where('exam_id', $exam->id)
            ->with(['mahasiswa', 'answers'])
            ->orderBy('finished_at', 'desc')
            ->get();

        return view('dosen.exam.results', compact('exam', 'sessions'));
    }

    // Show grade form for exam session
    public function showGradeSession(Exam $exam, ExamSession $session)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id || $session->exam_id !== $exam->id) {
            abort(403);
        }

        $exam->load(['jadwalKuliah.mataKuliah', 'questions']);
        $session->load(['mahasiswa', 'answers.examQuestion']);

        // Get only essay questions
        $essayAnswers = $session->answers->filter(function($answer) {
            return $answer->examQuestion->isEssay();
        });

        return view('dosen.exam.grade-session', compact('exam', 'session', 'essayAnswers'));
    }

    // Grade exam session (for essay questions)
    public function gradeSession(Request $request, Exam $exam, ExamSession $session)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($exam->dosen_id !== $dosen->id || $session->exam_id !== $exam->id) {
            abort(403);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.nilai' => 'required|numeric|min:0',
            'answers.*.feedback' => 'nullable|string',
        ]);

        foreach ($validated['answers'] as $answerId => $data) {
            $answer = $session->answers()->find($answerId);
            if ($answer && $answer->examQuestion->isEssay()) {
                $answer->update([
                    'nilai' => $data['nilai'],
                    'feedback' => $data['feedback'] ?? null,
                ]);
            }
        }

        // Calculate total score
        $totalScore = 0;
        $totalBobot = 0;
        
        foreach ($session->answers as $answer) {
            if ($answer->nilai !== null) {
                $totalScore += $answer->nilai * ($answer->examQuestion->bobot ?? 1);
                $totalBobot += $answer->examQuestion->bobot ?? 1;
            }
        }

        $finalScore = $totalBobot > 0 ? ($totalScore / $totalBobot) * 100 : 0;
        
        $session->update(['nilai' => $finalScore]);

        return back()->with('success', 'Nilai berhasil disimpan');
    }
}
