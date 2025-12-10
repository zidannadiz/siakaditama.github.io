<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /**
     * Get list of pengumuman for all roles
     * Filtered by target (semua, mahasiswa, dosen) based on user role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $query = Pengumuman::query();

        // Filter berdasarkan target
        $query->where(function($q) use ($role) {
            $q->where('target', 'semua');
            if ($role === 'mahasiswa') {
                $q->orWhere('target', 'mahasiswa');
            } elseif ($role === 'dosen') {
                $q->orWhere('target', 'dosen');
            } elseif ($role === 'admin') {
                $q->orWhere('target', 'admin');
            }
        });

        // Filter berdasarkan published_at
        $query->where(function($q) {
            $q->where('published_at', '<=', now())
              ->orWhereNull('published_at');
        });

        // Filter by kategori jika ada
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        // Order: pinned first, then by published_at
        $pengumumans = $query->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'pengumumans' => $pengumumans->map(function($pengumuman) {
                    return [
                        'id' => $pengumuman->id,
                        'judul' => $pengumuman->judul,
                        'isi' => $pengumuman->isi,
                        'kategori' => $pengumuman->kategori,
                        'target' => $pengumuman->target,
                        'is_pinned' => $pengumuman->is_pinned,
                        'published_at' => $pengumuman->published_at?->toISOString(),
                        'created_at' => $pengumuman->created_at->toISOString(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $pengumumans->currentPage(),
                    'last_page' => $pengumumans->lastPage(),
                    'per_page' => $pengumumans->perPage(),
                    'total' => $pengumumans->total(),
                ],
            ],
        ]);
    }

    /**
     * Get detail pengumuman
     */
    public function show(Pengumuman $pengumuman)
    {
        $user = Auth::user();
        $role = $user->role;

        // Check if user can access this pengumuman
        $canAccess = $pengumuman->target === 'semua' 
            || ($pengumuman->target === 'mahasiswa' && $role === 'mahasiswa')
            || ($pengumuman->target === 'dosen' && $role === 'dosen')
            || ($pengumuman->target === 'admin' && $role === 'admin');

        if (!$canAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pengumuman ini.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pengumuman->id,
                'judul' => $pengumuman->judul,
                'isi' => $pengumuman->isi,
                'kategori' => $pengumuman->kategori,
                'target' => $pengumuman->target,
                'is_pinned' => $pengumuman->is_pinned,
                'published_at' => $pengumuman->published_at?->toISOString(),
                'created_at' => $pengumuman->created_at->toISOString(),
                'user' => $pengumuman->user ? [
                    'id' => $pengumuman->user->id,
                    'name' => $pengumuman->user->name,
                ] : null,
            ],
        ]);
    }
}
