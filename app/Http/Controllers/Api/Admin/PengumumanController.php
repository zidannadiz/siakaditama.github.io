<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'pengumumans' => $pengumumans->map(function($pengumuman) {
                    return [
                        'id' => $pengumuman->id,
                        'judul' => $pengumuman->judul,
                        'isi' => $pengumuman->isi,
                        'target' => $pengumuman->target,
                        'is_pinned' => $pengumuman->is_pinned,
                        'published_at' => $pengumuman->published_at?->toISOString(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $pengumumans->currentPage(),
                    'last_page' => $pengumumans->lastPage(),
                    'total' => $pengumumans->total(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'target' => 'required|in:semua,mahasiswa,dosen',
            'is_pinned' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $pengumuman = Pengumuman::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil ditambahkan.',
            'data' => [
                'id' => $pengumuman->id,
                'judul' => $pengumuman->judul,
            ],
        ], 201);
    }

    public function show(Pengumuman $pengumuman)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pengumuman->id,
                'judul' => $pengumuman->judul,
                'isi' => $pengumuman->isi,
                'target' => $pengumuman->target,
                'is_pinned' => $pengumuman->is_pinned,
                'published_at' => $pengumuman->published_at?->toISOString(),
            ],
        ]);
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'target' => 'required|in:semua,mahasiswa,dosen',
            'is_pinned' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $pengumuman->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diperbarui.',
        ]);
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus.',
        ]);
    }
}

