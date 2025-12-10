<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswas = Mahasiswa::with(['prodi', 'user'])->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'mahasiswas' => $mahasiswas->map(function($mhs) {
                    return [
                        'id' => $mhs->id,
                        'nim' => $mhs->nim,
                        'nama' => $mhs->nama,
                        'email' => $mhs->user->email ?? null,
                        'prodi' => $mhs->prodi->nama ?? null,
                        'status' => $mhs->status,
                    ];
                }),
                'pagination' => [
                    'current_page' => $mahasiswas->currentPage(),
                    'last_page' => $mahasiswas->lastPage(),
                    'total' => $mahasiswas->total(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswas,nim',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'prodi_id' => 'required|exists:prodis,id',
            'jenis_kelamin' => 'required|in:L,P',
            'semester' => 'required|integer|min:1|max:14',
            'status' => 'required|in:aktif,nonaktif,lulus',
        ]);

        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $validated['nim'],
            'nama' => $validated['nama'],
            'prodi_id' => $validated['prodi_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'semester' => $validated['semester'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil ditambahkan.',
            'data' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
            ],
        ], 201);
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['prodi', 'user']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'email' => $mahasiswa->user->email ?? null,
                'prodi_id' => $mahasiswa->prodi_id,
                'prodi' => $mahasiswa->prodi->nama ?? null,
                'jenis_kelamin' => $mahasiswa->jenis_kelamin,
                'semester' => $mahasiswa->semester,
                'status' => $mahasiswa->status,
            ],
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswas,nim,' . $mahasiswa->id,
            'nama' => 'required|string|max:255',
            'prodi_id' => 'required|exists:prodis,id',
            'status' => 'required|in:aktif,nonaktif,lulus',
        ]);

        $mahasiswa->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil diperbarui.',
        ]);
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->user->delete();
        $mahasiswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil dihapus.',
        ]);
    }
}

