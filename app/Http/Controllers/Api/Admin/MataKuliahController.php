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
        ]);

        $mataKuliah = MataKuliah::create($validated);

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
                'prodi' => $mataKuliah->prodi->nama ?? null,
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
        ]);

        $mataKuliah->update($validated);

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

