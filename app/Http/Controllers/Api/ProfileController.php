<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        if ($user->role === 'mahasiswa') {
            $mahasiswa = \App\Models\Mahasiswa::where('user_id', $user->id)->first();
            if ($mahasiswa) {
                $userData['mahasiswa'] = [
                    'id' => $mahasiswa->id,
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->nama,
                    'prodi' => $mahasiswa->prodi->nama ?? null,
                ];
            }
        } elseif ($user->role === 'dosen') {
            $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
            if ($dosen) {
                $userData['dosen'] = [
                    'id' => $dosen->id,
                    'nidn' => $dosen->nidn,
                    'nama' => $dosen->nama,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $userData,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }
}

