<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodis = Prodi::latest()->paginate(15);
        return view('admin.prodi.index', compact('prodis'));
    }

    public function create()
    {
        return view('admin.prodi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_prodi' => 'required|string|max:10|unique:prodis,kode_prodi',
            'nama_prodi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Prodi::create($validated);

        return redirect()->route('admin.prodi.index')
            ->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit(Prodi $prodi)
    {
        return view('admin.prodi.edit', compact('prodi'));
    }

    public function update(Request $request, Prodi $prodi)
    {
        $validated = $request->validate([
            'kode_prodi' => 'required|string|max:10|unique:prodis,kode_prodi,' . $prodi->id,
            'nama_prodi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $prodi->update($validated);

        return redirect()->route('admin.prodi.index')
            ->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();

        return redirect()->route('admin.prodi.index')
            ->with('success', 'Program Studi berhasil dihapus.');
    }
}

