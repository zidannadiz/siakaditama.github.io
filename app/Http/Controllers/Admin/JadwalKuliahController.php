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
        $query = JadwalKuliah::with(['mataKuliah.prodi', 'dosen', 'semester'])->latest();
        
        if (auth()->user()->isAdminProdi()) {
            $query->whereHas('mataKuliah', function($q) {
                $q->where('prodi_id', auth()->user()->prodi_id);
            });
        }
        
        $jadwalKuliahs = $query->paginate(15);
        return view('admin.jadwal-kuliah.index', compact('jadwalKuliahs'));
    }

    public function create()
    {
        $prodiId = auth()->user()->prodi_id;
        
        $mataKuliahs = auth()->user()->isAdminProdi() 
            ? MataKuliah::where('prodi_id', $prodiId)->get() 
            : MataKuliah::all();
            
        $dosens = auth()->user()->isAdminProdi()
            ? Dosen::where('status', 'aktif')->where('prodi_id', $prodiId)->get()
            : Dosen::where('status', 'aktif')->get();
            
        $semesters = Semester::all();
        return view('admin.jadwal-kuliah.create', compact('mataKuliahs', 'dosens', 'semesters'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->isAdminProdi()) {
            $mk = MataKuliah::find($request->mata_kuliah_id);
            if ($mk && $mk->prodi_id != auth()->user()->prodi_id) {
                abort(403, 'Anda hanya bisa membuat jadwal untuk mata kuliah di prodi Anda sendiri.');
            }
        }

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
        $this->authorizeProdi($jadwalKuliah);
        
        $jadwalKuliah->load(['mataKuliah', 'dosen', 'semester']);
        $prodiId = auth()->user()->prodi_id;
        
        $mataKuliahs = auth()->user()->isAdminProdi() 
            ? MataKuliah::where('prodi_id', $prodiId)->get() 
            : MataKuliah::all();
            
        $dosens = auth()->user()->isAdminProdi()
            ? Dosen::where('status', 'aktif')->where('prodi_id', $prodiId)->get()
            : Dosen::where('status', 'aktif')->get();
            
        $semesters = Semester::all();
        return view('admin.jadwal-kuliah.edit', compact('jadwalKuliah', 'mataKuliahs', 'dosens', 'semesters'));
    }

    public function update(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $this->authorizeProdi($jadwalKuliah);
        
        if (auth()->user()->isAdminProdi()) {
            $mk = MataKuliah::find($request->mata_kuliah_id);
            if ($mk && $mk->prodi_id != auth()->user()->prodi_id) {
                abort(403, 'Anda tidak bisa mengubah ke mata kuliah prodi lain.');
            }
        }

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
        $this->authorizeProdi($jadwalKuliah);
        
        $jadwalKuliah->delete();

        return redirect()->route('admin.jadwal-kuliah.index')
            ->with('success', 'Jadwal Kuliah berhasil dihapus.');
    }

    private function authorizeProdi(JadwalKuliah $jadwalKuliah): void
    {
        if (auth()->user()->isAdminProdi()) {
            $jadwalKuliah->loadMissing('mataKuliah');
            if ($jadwalKuliah->mataKuliah->prodi_id !== auth()->user()->prodi_id) {
                abort(403, 'Anda tidak berhak mengakses jadwal kuliah prodi lain.');
            }
        }
    }
}

