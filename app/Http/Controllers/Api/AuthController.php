<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        // Load related data based on role
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
}

