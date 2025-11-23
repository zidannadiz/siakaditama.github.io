<?php

namespace App\Http\Controllers\Admin;

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
        return view('admin.dosen.index', compact('dosens'));
    }

    public function create()
    {
        return view('admin.dosen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nidn' => 'required|string|max:20|unique:dosens,nidn',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $user = User::create([
            'name' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'dosen',
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'nidn' => $validated['nidn'],
            'nama' => $validated['nama'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function edit(Dosen $dosen)
    {
        $dosen->load('user');
        return view('admin.dosen.edit', compact('dosen'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $validated = $request->validate([
            'nidn' => 'required|string|max:20|unique:dosens,nidn,' . $dosen->id,
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->user_id,
            'password' => 'nullable|string|min:8',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $dosen->user->update([
            'name' => $validated['nama'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $dosen->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $dosen->update([
            'nidn' => $validated['nidn'],
            'nama' => $validated['nama'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->user->delete();
        $dosen->delete();

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil dihapus.');
    }
}

