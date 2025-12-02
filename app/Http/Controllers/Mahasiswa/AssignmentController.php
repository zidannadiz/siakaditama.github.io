<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignmentController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();
        
        // Get assignments from approved KRS
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.assignments' => function($query) {
                $query->where('status', 'published')
                    ->orderBy('deadline', 'desc');
            }])
            ->get();

        $assignments = collect();
        foreach ($krs as $k) {
            foreach ($k->jadwalKuliah->assignments as $assignment) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->first();
                
                $assignment->submission = $submission;
                $assignments->push($assignment);
            }
        }

        return view('mahasiswa.assignment.index', compact('assignments'));
    }

    public function show(Assignment $assignment)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        // Check if mahasiswa is enrolled in this jadwal
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $assignment->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            abort(403, 'Anda tidak terdaftar pada mata kuliah ini');
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        $assignment->load('jadwalKuliah.mataKuliah');

        return view('mahasiswa.assignment.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        // Check if mahasiswa is enrolled
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $assignment->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            abort(403);
        }

        // Check if assignment is still open
        if ($assignment->isExpired() && $assignment->status !== 'closed') {
            return back()->with('error', 'Batas waktu pengumpulan tugas telah berakhir');
        }

        $validated = $request->validate([
            'jawaban' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
        ]);

        // Check if already submitted
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($submission) {
            return back()->with('error', 'Anda sudah mengumpulkan tugas ini');
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . Str::uuid()->toString() . '.' . $extension;
            $filePath = $file->storeAs('assignment-submissions', $safeFileName, 'local');
            $validated['file_path'] = $filePath;
        }

        $validated['assignment_id'] = $assignment->id;
        $validated['mahasiswa_id'] = $mahasiswa->id;
        $validated['submitted_at'] = now();

        AssignmentSubmission::create($validated);

        return redirect()->route('mahasiswa.assignment.show', $assignment)
            ->with('success', 'Tugas berhasil dikumpulkan');
    }

    public function updateSubmission(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        if ($submission->mahasiswa_id !== $mahasiswa->id || $submission->assignment_id !== $assignment->id) {
            abort(403);
        }

        // Check if assignment is still open
        if ($assignment->isExpired()) {
            return back()->with('error', 'Batas waktu pengumpulan tugas telah berakhir');
        }

        $validated = $request->validate([
            'jawaban' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($submission->file_path && Storage::disk('local')->exists($submission->file_path)) {
                Storage::disk('local')->delete($submission->file_path);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . Str::uuid()->toString() . '.' . $extension;
            $filePath = $file->storeAs('assignment-submissions', $safeFileName, 'local');
            $validated['file_path'] = $filePath;
        }

        $validated['submitted_at'] = now();

        $submission->update($validated);

        return redirect()->route('mahasiswa.assignment.show', $assignment)
            ->with('success', 'Tugas berhasil diperbarui');
    }

    public function downloadFile(Assignment $assignment)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        // Check enrollment
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $assignment->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs || !$assignment->file_path) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($assignment->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('local')->download($assignment->file_path);
    }
}
