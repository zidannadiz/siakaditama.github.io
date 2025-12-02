<?php

namespace App\Http\Controllers\Mahasiswa;

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
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();
        
        // Get exams from approved KRS
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.exams' => function($query) {
                $query->where('status', 'published')
                    ->where('selesai', '>=', now())
                    ->orderBy('selesai', 'asc');
            }])
            ->get();

        $exams = collect();
        foreach ($krs as $k) {
            foreach ($k->jadwalKuliah->exams as $exam) {
                $session = ExamSession::where('exam_id', $exam->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->first();
                
                $exam->session = $session;
                $exams->push($exam);
            }
        }

        return view('mahasiswa.exam.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        // Check if mahasiswa is enrolled
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $exam->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            abort(403, 'Anda tidak terdaftar pada mata kuliah ini');
        }

        $exam->load('jadwalKuliah.mataKuliah');

        // Check if exam is available
        if ($exam->status !== 'published') {
            abort(404, 'Ujian tidak tersedia');
        }

        // Check time window - ensure timezone consistency
        $now = Carbon::now(config('app.timezone'));
        
        if ($exam->mulai) {
            $mulai = Carbon::parse($exam->mulai)->setTimezone(config('app.timezone'));
            if ($now->isBefore($mulai)) {
                return view('mahasiswa.exam.not-started', compact('exam'));
            }
        }

        $selesai = Carbon::parse($exam->selesai)->setTimezone(config('app.timezone'));
        if ($now->isAfter($selesai)) {
            return view('mahasiswa.exam.ended', compact('exam'));
        }

        // Check if already has session
        $session = ExamSession::where('exam_id', $exam->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($session && $session->isFinished()) {
            return redirect()->route('mahasiswa.exam.result', ['exam' => $exam, 'session' => $session]);
        }

        return view('mahasiswa.exam.show', compact('exam', 'session'));
    }

    public function start(Request $request, Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        // Check enrollment
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $exam->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            abort(403);
        }

        // Check if exam is available
        if ($exam->status !== 'published') {
            return back()->with('error', 'Ujian tidak tersedia');
        }

        // Check time window - ensure timezone consistency
        $now = Carbon::now(config('app.timezone'));
        
        if ($exam->mulai) {
            $mulai = Carbon::parse($exam->mulai)->setTimezone(config('app.timezone'));
            if ($now->isBefore($mulai)) {
                return back()->with('error', 'Ujian belum dimulai');
            }
        }

        $selesai = Carbon::parse($exam->selesai)->setTimezone(config('app.timezone'));
        if ($now->isAfter($selesai)) {
            return back()->with('error', 'Ujian sudah berakhir');
        }

        // Check if already has session
        $existingSession = ExamSession::where('exam_id', $exam->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($existingSession && !$existingSession->isFinished()) {
            return redirect()->route('mahasiswa.exam.take', ['exam' => $exam, 'session' => $existingSession]);
        }

        if ($existingSession && $existingSession->isFinished()) {
            return redirect()->route('mahasiswa.exam.result', ['exam' => $exam, 'session' => $existingSession])
                ->with('info', 'Anda sudah menyelesaikan ujian ini');
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

        return redirect()->route('mahasiswa.exam.take', ['exam' => $exam, 'session' => $session]);
    }

    public function take(Exam $exam, ExamSession $session)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        // Verify session belongs to this mahasiswa and exam
        if ($session->mahasiswa_id !== $mahasiswa->id || $session->exam_id !== $exam->id) {
            abort(403);
        }

        // Check if session is active
        if (!$session->isActive()) {
            return redirect()->route('mahasiswa.exam.result', ['exam' => $exam, 'session' => $session]);
        }

        // Check time remaining
        $now = now();
        $elapsed = $session->started_at->diffInSeconds($now);
        $remaining = ($exam->durasi * 60) - $elapsed;

        if ($remaining <= 0) {
            // Auto-submit
            $this->autoSubmit($exam, $session);
            return redirect()->route('mahasiswa.exam.result', ['exam' => $exam, 'session' => $session])
                ->with('info', 'Waktu ujian telah habis. Jawaban Anda otomatis disubmit.');
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
        if ($exam->random_pilihan) {
            foreach ($questions as $question) {
                if ($question->isPilgan() && $question->pilihan) {
                    $choices = $question->pilihan;
                    $keys = array_keys($choices);
                    shuffle($keys);
                    $shuffled = [];
                    foreach ($keys as $key) {
                        $shuffled[$key] = $choices[$key];
                    }
                    $question->pilihan = $shuffled;
                }
            }
        }

        $exam->load('jadwalKuliah.mataKuliah');

        return view('mahasiswa.exam.take', compact('exam', 'session', 'questions', 'remaining'));
    }

    public function saveAnswer(Request $request, Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
            'question_id' => 'required|exists:exam_questions,id',
            'jawaban_pilgan' => 'nullable|string',
            'jawaban_essay' => 'nullable|string',
        ]);

        $session = ExamSession::where('id', $validated['session_id'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('exam_id', $exam->id)
            ->firstOrFail();

        if (!$session->isActive()) {
            return response()->json(['error' => 'Session tidak aktif'], 400);
        }

        $answer = ExamAnswer::where('exam_session_id', $session->id)
            ->where('exam_question_id', $validated['question_id'])
            ->firstOrFail();

        $updateData = [
            'is_answered' => true,
        ];

        if ($validated['jawaban_pilgan']) {
            $updateData['jawaban_pilgan'] = $validated['jawaban_pilgan'];
        }

        if ($validated['jawaban_essay']) {
            $updateData['jawaban_essay'] = $validated['jawaban_essay'];
        }

        $answer->update($updateData);

        return response()->json(['success' => true, 'message' => 'Jawaban tersimpan']);
    }

    public function submit(Request $request, Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
        ]);

        $session = ExamSession::where('id', $validated['session_id'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('exam_id', $exam->id)
            ->firstOrFail();

        if (!$session->isActive()) {
            return back()->with('error', 'Ujian sudah disubmit');
        }

        // Calculate score for pilgan questions
        $this->calculateScore($exam, $session);

        // Mark session as submitted
        $session->update([
            'status' => 'submitted',
            'finished_at' => now(),
        ]);

        return redirect()->route('mahasiswa.exam.result', ['exam' => $exam, 'session' => $session])
            ->with('success', 'Ujian berhasil disubmit');
    }

    public function logViolation(Request $request, Exam $exam)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'session_id' => 'required|exists:exam_sessions,id',
            'violation_type' => 'required|in:tab_switch,copy_paste,window_blur,fullscreen_exit',
        ]);

        $session = ExamSession::where('id', $validated['session_id'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('exam_id', $exam->id)
            ->firstOrFail();

        if (!$session->isActive()) {
            return response()->json(['error' => 'Session tidak aktif'], 400);
        }

        // Update counters
        if ($validated['violation_type'] === 'tab_switch') {
            $session->increment('tab_switch_count');
        } elseif ($validated['violation_type'] === 'copy_paste') {
            $session->increment('copy_paste_attempt_count');
        } elseif ($validated['violation_type'] === 'window_blur') {
            // Track window blur separately if needed
        } elseif ($validated['violation_type'] === 'fullscreen_exit') {
            // Track fullscreen exit separately if needed
        }

        // Log violation
        $session->addViolation($validated['violation_type'], [
            'timestamp' => now()->toISOString(),
            'user_agent' => $request->userAgent(),
        ]);

        // Calculate total violations (count from violations array)
        $totalViolations = count($session->violations ?? []);

        // If violations >= 3, terminate and delete session (must restart from beginning)
        if ($totalViolations >= 3) {
            // Delete the session so student must start from beginning
            $session->delete();
            
            return response()->json([
                'error' => 'Anda telah melakukan 3 kali pelanggaran. Ujian dihentikan dan Anda harus mengulang dari awal.',
                'terminated' => true,
                'must_restart' => true
            ], 403);
        }

        return response()->json([
            'success' => true, 
            'warning' => 'Pelanggaran dicatat',
            'total_violations' => $totalViolations,
            'remaining' => 3 - $totalViolations
        ]);
    }

    public function result(Exam $exam, ExamSession $session)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        if ($session->mahasiswa_id !== $mahasiswa->id || $session->exam_id !== $exam->id) {
            abort(403);
        }

        $exam->load('jadwalKuliah.mataKuliah');

        // Get questions with answers
        $questions = $exam->questions()->with(['answers' => function($query) use ($session) {
            $query->where('exam_session_id', $session->id);
        }])->orderBy('urutan')->get();

        return view('mahasiswa.exam.result', compact('exam', 'session', 'questions'));
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
                $isCorrect = $answer->isCorrect();
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
