<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $mataKuliahs = MataKuliah::with('prodi')->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'mata_kuliahs' => $mataKuliahs->map(function($mk) {
                    return [
                        'id' => $mk->id,
                        'kode_mk' => $mk->kode_mk,
                        'nama' => $mk->nama,
                        'sks' => $mk->sks,
                        'prodi' => $mk->prodi->nama ?? null,
                    ];
                }),
                'pagination' => [
                    'current_page' => $mataKuliahs->currentPage(),
                    'last_page' => $mataKuliahs->lastPage(),
                    'total' => $mataKuliahs->total(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliahs,kode_mk',
            'nama' => 'required|string|max:255',
            'sks' => 'required|integer|min:1',
            'prodi_id' => 'required|exists:prodis,id',
            'semester' => 'nullable|integer|min:1|max:14',
            'deskripsi' => 'nullable|string',
            'jenis' => 'nullable|in:wajib,pilihan',
        ]);

        $mataKuliah = MataKuliah::create([
            'kode_mk' => $validated['kode_mk'],
            'nama_mk' => $validated['nama'],
            'sks' => $validated['sks'],
            'prodi_id' => $validated['prodi_id'],
            'semester' => $validated['semester'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'jenis' => $validated['jenis'] ?? 'wajib',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data' => [
                'id' => $mataKuliah->id,
                'kode_mk' => $mataKuliah->kode_mk,
                'nama' => $mataKuliah->nama,
            ],
        ], 201);
    }

    public function show(MataKuliah $mataKuliah)
    {
        $mataKuliah->load('prodi');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $mataKuliah->id,
                'kode_mk' => $mataKuliah->kode_mk,
                'nama' => $mataKuliah->nama,
                'sks' => $mataKuliah->sks,
                'prodi_id' => $mataKuliah->prodi_id,
                'prodi' => $mataKuliah->prodi->nama ?? null,
                'semester' => $mataKuliah->semester,
                'deskripsi' => $mataKuliah->deskripsi,
                'jenis' => $mataKuliah->jenis,
            ],
        ]);
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliahs,kode_mk,' . $mataKuliah->id,
            'nama' => 'required|string|max:255',
            'sks' => 'required|integer|min:1',
            'prodi_id' => 'required|exists:prodis,id',
            'semester' => 'nullable|integer|min:1|max:14',
            'deskripsi' => 'nullable|string',
            'jenis' => 'nullable|in:wajib,pilihan',
        ]);

        $mataKuliah->update([
            'kode_mk' => $validated['kode_mk'],
            'nama_mk' => $validated['nama'],
            'sks' => $validated['sks'],
            'prodi_id' => $validated['prodi_id'],
            'semester' => $validated['semester'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'jenis' => $validated['jenis'] ?? 'wajib',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil diperbarui.',
        ]);
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        $mataKuliah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil dihapus.',
        ]);
    }
}

