<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignmentController extends Controller
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

        $assignments = collect();
        
        if ($jadwal_id) {
            $assignments = Assignment::where('jadwal_kuliah_id', $jadwal_id)
                ->where('dosen_id', $dosen->id)
                ->with(['jadwalKuliah.mataKuliah', 'submissions'])
                ->orderBy('deadline', 'desc')
                ->get();
        }

        return view('dosen.assignment.index', compact('jadwals', 'assignments', 'jadwal_id'));
    }

    public function create()
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();
        
        $jadwal_id = request('jadwal_id');
        
        if (!$jadwal_id) {
            return redirect()->route('dosen.assignment.index')
                ->with('error', 'Pilih jadwal kuliah terlebih dahulu');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->firstOrFail();

        return view('dosen.assignment.create', compact('jadwal'));
    }

    public function store(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'jadwal_kuliah_id' => 'required|exists:jadwal_kuliahs,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240', // 10MB max
            'deadline' => 'required|date|after:now',
            'bobot' => 'required|integer|min:0|max:100',
            'status' => 'required|in:draft,published',
        ]);

        // Verify jadwal belongs to this dosen
        $jadwal = JadwalKuliah::where('id', $validated['jadwal_kuliah_id'])
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . Str::uuid()->toString() . '.' . $extension;
            $filePath = $file->storeAs('assignments', $safeFileName, 'local');
            $validated['file_path'] = $filePath;
        }

        $validated['dosen_id'] = $dosen->id;

        Assignment::create($validated);

        return redirect()->route('dosen.assignment.index', ['jadwal_id' => $validated['jadwal_kuliah_id']])
            ->with('success', 'Tugas berhasil dibuat');
    }

    public function show(Assignment $assignment)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        // Verify assignment belongs to this dosen
        if ($assignment->dosen_id !== $dosen->id) {
            abort(403);
        }

        $assignment->load(['jadwalKuliah.mataKuliah', 'submissions.mahasiswa']);

        return view('dosen.assignment.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($assignment->dosen_id !== $dosen->id) {
            abort(403);
        }

        $assignment->load('jadwalKuliah.mataKuliah');

        return view('dosen.assignment.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($assignment->dosen_id !== $dosen->id) {
            abort(403);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'deadline' => 'required|date|after:now',
            'bobot' => 'required|integer|min:0|max:100',
            'status' => 'required|in:draft,published',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($assignment->file_path && Storage::disk('local')->exists($assignment->file_path)) {
                Storage::disk('local')->delete($assignment->file_path);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . Str::uuid()->toString() . '.' . $extension;
            $filePath = $file->storeAs('assignments', $safeFileName, 'local');
            $validated['file_path'] = $filePath;
        }

        $assignment->update($validated);

        return redirect()->route('dosen.assignment.index', ['jadwal_id' => $assignment->jadwal_kuliah_id])
            ->with('success', 'Tugas berhasil diperbarui');
    }

    public function destroy(Assignment $assignment)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($assignment->dosen_id !== $dosen->id) {
            abort(403);
        }

        // Delete file
        if ($assignment->file_path && Storage::disk('local')->exists($assignment->file_path)) {
            Storage::disk('local')->delete($assignment->file_path);
        }

        $jadwal_id = $assignment->jadwal_kuliah_id;
        $assignment->delete();

        return redirect()->route('dosen.assignment.index', ['jadwal_id' => $jadwal_id])
            ->with('success', 'Tugas berhasil dihapus');
    }

    // Grade submission
    public function gradeSubmission(Request $request, Assignment $assignment, $submission_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        if ($assignment->dosen_id !== $dosen->id) {
            abort(403);
        }

        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission = $assignment->submissions()->findOrFail($submission_id);
        $submission->update($validated);

        return back()->with('success', 'Nilai berhasil disimpan');
    }
}
