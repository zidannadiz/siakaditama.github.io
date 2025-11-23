<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::latest()->paginate(15);
        return view('admin.semester.index', compact('semesters'));
    }

    public function create()
    {
        return view('admin.semester.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_semester' => 'required|string|max:255',
            'jenis' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|integer|min:2020|max:2100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Jika status aktif, nonaktifkan yang lain
        if ($validated['status'] === 'aktif') {
            Semester::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        Semester::create($validated);

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(Semester $semester)
    {
        return view('admin.semester.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'nama_semester' => 'required|string|max:255',
            'jenis' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|integer|min:2020|max:2100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Jika status aktif, nonaktifkan yang lain
        if ($validated['status'] === 'aktif' && $semester->status !== 'aktif') {
            Semester::where('id', '!=', $semester->id)
                ->where('status', 'aktif')
                ->update(['status' => 'nonaktif']);
        }

        $semester->update($validated);

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil dihapus.');
    }
}

