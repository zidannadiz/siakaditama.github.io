<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodis = Prodi::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $prodis->map(function($prodi) {
                return [
                    'id' => $prodi->id,
                    'kode' => $prodi->kode,
                    'nama' => $prodi->nama,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:prodis,kode',
            'nama' => 'required|string|max:255',
        ]);

        $prodi = Prodi::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Prodi berhasil ditambahkan.',
            'data' => [
                'id' => $prodi->id,
                'kode' => $prodi->kode,
                'nama' => $prodi->nama,
            ],
        ], 201);
    }

    public function show(Prodi $prodi)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $prodi->id,
                'kode' => $prodi->kode,
                'nama' => $prodi->nama,
            ],
        ]);
    }

    public function update(Request $request, Prodi $prodi)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:prodis,kode,' . $prodi->id,
            'nama' => 'required|string|max:255',
        ]);

        $prodi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Prodi berhasil diperbarui.',
        ]);
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prodi berhasil dihapus.',
        ]);
    }
}

