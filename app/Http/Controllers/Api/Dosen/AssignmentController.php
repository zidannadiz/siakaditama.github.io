<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignmentController extends Controller
{
    /**
     * Get list of jadwal kuliah and assignments
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

        $assignments = collect();
        
        if ($jadwal_id) {
            $assignments = Assignment::where('jadwal_kuliah_id', $jadwal_id)
                ->where('dosen_id', $dosen->id)
                ->with(['jadwalKuliah.mataKuliah', 'submissions'])
                ->orderBy('deadline', 'desc')
                ->get()
                ->map(function($assignment) {
                    return [
                        'id' => $assignment->id,
                        'judul' => $assignment->judul,
                        'deskripsi' => $assignment->deskripsi,
                        'deadline' => $assignment->deadline?->toISOString(),
                        'bobot' => $assignment->bobot,
                        'status' => $assignment->status,
                        'has_file' => $assignment->file_path != null,
                        'mata_kuliah' => $assignment->jadwalKuliah->mataKuliah->nama ?? null,
                        'submission_count' => $assignment->submissions->count(),
                        'graded_count' => $assignment->submissions->whereNotNull('nilai')->count(),
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
                'assignments' => $assignments->values(),
                'selected_jadwal_id' => $jadwal_id,
            ],
        ]);
    }

    /**
     * Get assignment details with submissions
     */
    public function show(Assignment $assignment)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($assignment->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $assignment->load(['jadwalKuliah.mataKuliah', 'submissions.mahasiswa']);

        $submissions = $assignment->submissions->map(function($submission) {
            return [
                'id' => $submission->id,
                'mahasiswa' => [
                    'id' => $submission->mahasiswa->id,
                    'nama' => $submission->mahasiswa->nama,
                    'nim' => $submission->mahasiswa->nim,
                ],
                'jawaban' => $submission->jawaban,
                'file_path' => $submission->file_path,
                'file_name' => $submission->file_path ? basename($submission->file_path) : null,
                'submitted_at' => $submission->submitted_at?->toISOString(),
                'nilai' => $submission->nilai,
                'feedback' => $submission->feedback,
                'is_late' => $submission->isLate(),
            ];
        });

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
                    'has_file' => $assignment->file_path != null,
                    'mata_kuliah' => $assignment->jadwalKuliah->mataKuliah->nama ?? null,
                    'kode_mk' => $assignment->jadwalKuliah->mataKuliah->kode_mk ?? null,
                ],
                'submissions' => $submissions->values(),
            ],
        ]);
    }

    /**
     * Create new assignment
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
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'deadline' => 'required|date|after:now',
            'bobot' => 'required|integer|min:0|max:100',
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

        $assignment = Assignment::create($validated);
        $assignment->load(['jadwalKuliah.mataKuliah']);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dibuat',
            'data' => [
                'id' => $assignment->id,
                'judul' => $assignment->judul,
            ],
        ], 201);
    }

    /**
     * Update assignment
     */
    public function update(Request $request, Assignment $assignment)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($assignment->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'deadline' => 'sometimes|required|date',
            'bobot' => 'sometimes|required|integer|min:0|max:100',
            'status' => 'sometimes|required|in:draft,published',
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

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diperbarui',
            'data' => [
                'id' => $assignment->id,
                'judul' => $assignment->judul,
            ],
        ]);
    }

    /**
     * Delete assignment
     */
    public function destroy(Assignment $assignment)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($assignment->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Delete file
        if ($assignment->file_path && Storage::disk('local')->exists($assignment->file_path)) {
            Storage::disk('local')->delete($assignment->file_path);
        }

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus',
        ]);
    }

    /**
     * Grade submission
     */
    public function gradeSubmission(Request $request, Assignment $assignment, $submission_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        if ($assignment->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission = $assignment->submissions()->find($submission_id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission tidak ditemukan',
            ], 404);
        }

        $submission->nilai = $validated['nilai'];
        $submission->feedback = $validated['feedback'] ?? null;
        $submission->save();

        return response()->json([
            'success' => true,
            'message' => 'Nilai berhasil disimpan',
            'data' => [
                'id' => $submission->id,
                'nilai' => $submission->nilai,
            ],
        ]);
    }
}
