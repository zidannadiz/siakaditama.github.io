<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    public function index()
    {
        $dosens = Dosen::with('user')->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'dosens' => $dosens->map(function($dosen) {
                    return [
                        'id' => $dosen->id,
                        'nidn' => $dosen->nidn,
                        'nama' => $dosen->nama,
                        'email' => $dosen->user->email ?? null,
                        'status' => $dosen->status,
                    ];
                }),
                'pagination' => [
                    'current_page' => $dosens->currentPage(),
                    'last_page' => $dosens->lastPage(),
                    'total' => $dosens->total(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nidn' => 'required|string|max:20|unique:dosens,nidn',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'dosen',
        ]);

        $dosen = Dosen::create([
            'user_id' => $user->id,
            'nidn' => $validated['nidn'],
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil ditambahkan.',
            'data' => [
                'id' => $dosen->id,
                'nidn' => $dosen->nidn,
                'nama' => $dosen->nama,
            ],
        ], 201);
    }

    public function show(Dosen $dosen)
    {
        $dosen->load('user');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $dosen->id,
                'nidn' => $dosen->nidn,
                'nama' => $dosen->nama,
                'email' => $dosen->user->email ?? null,
                'status' => $dosen->status,
            ],
        ]);
    }

    public function update(Request $request, Dosen $dosen)
    {
        $validated = $request->validate([
            'nidn' => 'required|string|max:20|unique:dosens,nidn,' . $dosen->id,
            'nama' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $dosen->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil diperbarui.',
        ]);
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->user->delete();
        $dosen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil dihapus.',
        ]);
    }
}

