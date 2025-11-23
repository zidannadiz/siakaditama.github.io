<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::with('user')->latest()->paginate(15);
        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|in:umum,akademik,beasiswa,kegiatan',
            'target' => 'required|in:semua,mahasiswa,dosen,admin',
            'is_pinned' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['published_at'] = $validated['published_at'] ?? now();

        $pengumuman = Pengumuman::create($validated);

        // Buat notifikasi berdasarkan target
        if ($validated['target'] === 'semua') {
            NotifikasiService::createForRole('mahasiswa', $validated['judul'], $validated['isi'], 'info', route('mahasiswa.dashboard'));
            NotifikasiService::createForRole('dosen', $validated['judul'], $validated['isi'], 'info', route('dosen.dashboard'));
        } elseif ($validated['target'] === 'mahasiswa') {
            NotifikasiService::createForRole('mahasiswa', $validated['judul'], $validated['isi'], 'info', route('mahasiswa.dashboard'));
        } elseif ($validated['target'] === 'dosen') {
            NotifikasiService::createForRole('dosen', $validated['judul'], $validated['isi'], 'info', route('dosen.dashboard'));
        }

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|in:umum,akademik,beasiswa,kegiatan',
            'target' => 'required|in:semua,mahasiswa,dosen,admin',
            'is_pinned' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['published_at'] = $validated['published_at'] ?? $pengumuman->published_at ?? now();

        $pengumuman->update($validated);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}

