<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKuliah;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Semester;
use Illuminate\Http\Request;

class JadwalKuliahController extends Controller
{
    public function index()
    {
        $jadwalKuliahs = JadwalKuliah::with(['mataKuliah', 'dosen', 'semester'])
            ->latest()
            ->paginate(15);
        return view('admin.jadwal-kuliah.index', compact('jadwalKuliahs'));
    }

    public function create()
    {
        $mataKuliahs = MataKuliah::all();
        $dosens = Dosen::where('status', 'aktif')->get();
        $semesters = Semester::all();
        return view('admin.jadwal-kuliah.create', compact('mataKuliahs', 'dosens', 'semesters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:dosens,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $validated['terisi'] = 0;

        JadwalKuliah::create($validated);

        return redirect()->route('admin.jadwal-kuliah.index')
            ->with('success', 'Jadwal Kuliah berhasil ditambahkan.');
    }

    public function edit(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->load(['mataKuliah', 'dosen', 'semester']);
        $mataKuliahs = MataKuliah::all();
        $dosens = Dosen::where('status', 'aktif')->get();
        $semesters = Semester::all();
        return view('admin.jadwal-kuliah.edit', compact('jadwalKuliah', 'mataKuliahs', 'dosens', 'semesters'));
    }

    public function update(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:dosens,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $jadwalKuliah->update($validated);

        return redirect()->route('admin.jadwal-kuliah.index')
            ->with('success', 'Jadwal Kuliah berhasil diperbarui.');
    }

    public function destroy(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->delete();

        return redirect()->route('admin.jadwal-kuliah.index')
            ->with('success', 'Jadwal Kuliah berhasil dihapus.');
    }
}

