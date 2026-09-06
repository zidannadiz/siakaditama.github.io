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
        $query = MataKuliah::with('prodi')->latest();
        
        if (auth()->user()->isAdminProdi()) {
            $query->where('prodi_id', auth()->user()->prodi_id);
        }
        
        $mataKuliahs = $query->paginate(15);
        return view('admin.mata-kuliah.index', compact('mataKuliahs'));
    }

    public function create()
    {
        abort_if(auth()->user()->role === 'kaprodi', 403, 'Anda tidak memiliki wewenang untuk menambah data ini.');

        $prodis = auth()->user()->isAdminProdi()
            ? Prodi::where('id', auth()->user()->prodi_id)->get()
            : Prodi::all();
            
        return view('admin.mata-kuliah.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role === 'kaprodi', 403, 'Anda tidak memiliki wewenang untuk menyimpan data ini.');

        if (auth()->user()->isAdminProdi() && $request->prodi_id != auth()->user()->prodi_id) {
            abort(403, 'Anda hanya bisa menambahkan mata kuliah di prodi Anda sendiri.');
        }

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
        $this->authorizeProdi($mataKuliah);
        
        $mataKuliah->load('prodi');
        $prodis = auth()->user()->isAdminProdi()
            ? Prodi::where('id', auth()->user()->prodi_id)->get()
            : Prodi::all();
            
        return view('admin.mata-kuliah.edit', compact('mataKuliah', 'prodis'));
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $this->authorizeProdi($mataKuliah);
        
        if (auth()->user()->isAdminProdi() && $request->prodi_id != auth()->user()->prodi_id) {
            abort(403, 'Anda tidak bisa memindahkan mata kuliah ke prodi lain.');
        }

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
        $this->authorizeProdi($mataKuliah);
        
        $mataKuliah->delete();

        return redirect()->route('admin.mata-kuliah.index')
            ->with('success', 'Mata Kuliah berhasil dihapus.');
    }

    private function authorizeProdi(MataKuliah $mataKuliah): void
    {
        if (auth()->user()->isAdminProdi() && $mataKuliah->prodi_id !== auth()->user()->prodi_id) {
            abort(403, 'Anda tidak berhak mengakses mata kuliah prodi lain.');
        }
    }
}

