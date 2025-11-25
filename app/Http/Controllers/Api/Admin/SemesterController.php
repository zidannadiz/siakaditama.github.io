<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $semesters->map(function($semester) {
                return [
                    'id' => $semester->id,
                    'nama' => $semester->nama,
                    'tahun_ajaran' => $semester->tahun_ajaran,
                    'jenis' => $semester->jenis,
                    'status' => $semester->status,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:20',
            'jenis' => 'required|in:ganjil,genap',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Jika status aktif, nonaktifkan yang lain
        if ($validated['status'] === 'aktif') {
            Semester::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        $semester = Semester::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil ditambahkan.',
            'data' => [
                'id' => $semester->id,
                'nama' => $semester->nama,
            ],
        ], 201);
    }

    public function show(Semester $semester)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $semester->id,
                'nama' => $semester->nama,
                'tahun_ajaran' => $semester->tahun_ajaran,
                'jenis' => $semester->jenis,
                'status' => $semester->status,
            ],
        ]);
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:20',
            'jenis' => 'required|in:ganjil,genap',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Jika status aktif, nonaktifkan yang lain
        if ($validated['status'] === 'aktif') {
            Semester::where('status', 'aktif')
                ->where('id', '!=', $semester->id)
                ->update(['status' => 'nonaktif']);
        }

        $semester->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil diperbarui.',
        ]);
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();

        return response()->json([
            'success' => true,
            'message' => 'Semester berhasil dihapus.',
        ]);
    }
}

