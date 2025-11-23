<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $mataKuliahs = MataKuliah::with('prodi')->latest()->paginate(15);
        return view('admin.mata-kuliah.index', compact('mataKuliahs'));
    }

    public function create()
    {
        $prodis = Prodi::all();
        return view('admin.mata-kuliah.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliahs,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'prodi_id' => 'required|exists:prodis,id',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:wajib,pilihan',
        ]);

        MataKuliah::create($validated);

        return redirect()->route('admin.mata-kuliah.index')
            ->with('success', 'Mata Kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $mataKuliah)
    {
        $mataKuliah->load('prodi');
        $prodis = Prodi::all();
        return view('admin.mata-kuliah.edit', compact('mataKuliah', 'prodis'));
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:mata_kuliahs,kode_mk,' . $mataKuliah->id,
            'nama_mk' => 'required|string|max:255',
            'prodi_id' => 'required|exists:prodis,id',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:wajib,pilihan',
        ]);

        $mataKuliah->update($validated);

        return redirect()->route('admin.mata-kuliah.index')
            ->with('success', 'Mata Kuliah berhasil diperbarui.');
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        $mataKuliah->delete();

        return redirect()->route('admin.mata-kuliah.index')
            ->with('success', 'Mata Kuliah berhasil dihapus.');
    }
}

