<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun_mulai', 'desc')->paginate(10);
        return view('admin.tahun-ajaran.index', compact('tahunAjarans'));
    }

    public function create()
    {
        return view('admin.tahun-ajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:50|unique:tahun_ajarans,nama',
            'tahun_mulai'     => 'required|digits:4|integer',
            'tahun_selesai'   => 'required|digits:4|integer|gt:tahun_mulai',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:aktif,nonaktif',
            'keterangan'      => 'nullable|string',
        ]);

        // Jika status aktif, nonaktifkan yang lain
        if ($validated['status'] === 'aktif') {
            TahunAjaran::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        TahunAjaran::create($validated);

        return redirect()->route('admin.tahun-ajaran.index')
                         ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function show(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->load('semesters');
        return view('admin.tahun-ajaran.show', compact('tahunAjaran'));
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:50|unique:tahun_ajarans,nama,' . $tahunAjaran->id,
            'tahun_mulai'     => 'required|digits:4|integer',
            'tahun_selesai'   => 'required|digits:4|integer|gt:tahun_mulai',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:aktif,nonaktif',
            'keterangan'      => 'nullable|string',
        ]);

        // Jika status aktif, nonaktifkan yang lain
        if ($validated['status'] === 'aktif') {
            TahunAjaran::where('status', 'aktif')
                       ->where('id', '!=', $tahunAjaran->id)
                       ->update(['status' => 'nonaktif']);
        }

        $tahunAjaran->update($validated);

        return redirect()->route('admin.tahun-ajaran.index')
                         ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->semesters()->count() > 0) {
            return redirect()->route('admin.tahun-ajaran.index')
                             ->with('error', 'Tahun ajaran tidak bisa dihapus karena masih memiliki semester.');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')
                         ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}


