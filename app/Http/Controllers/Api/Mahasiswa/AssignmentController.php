<?php

namespace App\Http\Controllers\Api\Mahasiswa;

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
    /**
     * Get list of assignments for mahasiswa
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

        // Get assignments from approved KRS
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.assignments' => function($query) {
                $query->where('status', 'published')
                    ->orderBy('deadline', 'desc');
            }, 'jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
            ->get();

        $assignments = collect();
        foreach ($krs as $k) {
            foreach ($k->jadwalKuliah->assignments as $assignment) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->first();
                
                $assignments->push([
                    'id' => $assignment->id,
                    'judul' => $assignment->judul,
                    'deskripsi' => $assignment->deskripsi,
                    'deadline' => $assignment->deadline?->toISOString(),
                    'bobot' => $assignment->bobot,
                    'status' => $assignment->status,
                    'is_expired' => $assignment->isExpired(),
                    'mata_kuliah' => $k->jadwalKuliah->mataKuliah->nama ?? null,
                    'kode_mk' => $k->jadwalKuliah->mataKuliah->kode_mk ?? null,
                    'dosen' => $k->jadwalKuliah->dosen->nama ?? null,
                    'submission' => $submission ? [
                        'id' => $submission->id,
                        'submitted_at' => $submission->submitted_at?->toISOString(),
                        'nilai' => $submission->nilai,
                        'feedback' => $submission->feedback,
                        'is_late' => $submission->isLate(),
                    ] : null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $assignments->values()
        ]);
    }

    /**
     * Get assignment details
     */
    public function show(Assignment $assignment)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        // Check if mahasiswa is enrolled
        $krs = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $assignment->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar pada mata kuliah ini',
            ], 403);
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        $assignment->load(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'dosen']);

        return response()->json([
            'success' => true,
            'data' => [
                'assignment' => [
                    'id' => $assignment->id,
                    'judul' => $assignment->judul,
                    'deskripsi' => $assignment->deskripsi,
                    'deadline' => $assignment->deadline?->toISOString(),
                    'bobot' => $assignment->bobot,
                    'status' => $assignment->status,
                    'is_expired' => $assignment->isExpired(),
                    'has_file' => $assignment->file_path != null,
                    'mata_kuliah' => $assignment->jadwalKuliah->mataKuliah->nama ?? null,
                    'kode_mk' => $assignment->jadwalKuliah->mataKuliah->kode_mk ?? null,
                    'dosen' => $assignment->jadwalKuliah->dosen->nama ?? null,
                ],
                'submission' => $submission ? [
                    'id' => $submission->id,
                    'jawaban' => $submission->jawaban,
                    'file_path' => $submission->file_path,
                    'file_name' => $submission->file_path ? basename($submission->file_path) : null,
                    'submitted_at' => $submission->submitted_at?->toISOString(),
                    'nilai' => $submission->nilai,
                    'feedback' => $submission->feedback,
                    'is_late' => $submission->isLate(),
                ] : null,
            ],
        ]);
    }

    /**
     * Submit assignment
     */
    public function submit(Request $request, Assignment $assignment)
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
            ->where('jadwal_kuliah_id', $assignment->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar pada mata kuliah ini',
            ], 403);
        }

        // Check if assignment is still open
        if ($assignment->isExpired() && $assignment->status !== 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pengumpulan tugas telah berakhir',
            ], 422);
        }

        // Check if already submitted
        $existingSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($existingSubmission) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengumpulkan tugas ini',
            ], 422);
        }

        $validated = $request->validate([
            'jawaban' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . Str::uuid()->toString() . '.' . $extension;
            $filePath = $file->storeAs('assignment-submissions', $safeFileName, 'local');
        }

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'mahasiswa_id' => $mahasiswa->id,
            'jawaban' => $validated['jawaban'] ?? null,
            'file_path' => $filePath,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dikumpulkan',
            'data' => [
                'id' => $submission->id,
                'submitted_at' => $submission->submitted_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * Update submission
     */
    public function updateSubmission(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        if ($submission->mahasiswa_id !== $mahasiswa->id || $submission->assignment_id !== $assignment->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Check if assignment is still open
        if ($assignment->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pengumpulan tugas telah berakhir',
            ], 422);
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

        $submission->update([
            'jawaban' => $validated['jawaban'] ?? $submission->jawaban,
            'file_path' => $validated['file_path'] ?? $submission->file_path,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diperbarui',
            'data' => [
                'id' => $submission->id,
                'submitted_at' => $submission->submitted_at->toISOString(),
            ],
        ]);
    }

    /**
     * Get assignment file download info
     */
    public function downloadFile(Assignment $assignment)
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
            ->where('jadwal_kuliah_id', $assignment->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs || !$assignment->file_path) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan',
            ], 404);
        }

        if (!Storage::disk('local')->exists($assignment->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan',
            ], 404);
        }

        // Return file info
        return response()->json([
            'success' => true,
            'data' => [
                'file_path' => $assignment->file_path,
                'file_name' => basename($assignment->file_path),
            ],
        ]);
    }
}
