<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KRS;
use App\Models\JadwalKuliah;
use App\Models\Mahasiswa;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KRSController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Tidak ada semester aktif.');
        }

        $krs_list = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semester_aktif->id)
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'semester'])
            ->get();

        $total_sks = $krs_list->sum(function($krs) {
            return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
        });

        return view('mahasiswa.krs.index', compact('krs_list', 'semester_aktif', 'total_sks', 'mahasiswa'));
    }

    public function create()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Tidak ada semester aktif.');
        }

        // Ambil jadwal yang sesuai dengan prodi dan semester mahasiswa
        $jadwal_available = JadwalKuliah::where('semester_id', $semester_aktif->id)
            ->where('status', 'aktif')
            ->whereHas('mataKuliah', function($query) use ($mahasiswa) {
                $query->where('prodi_id', $mahasiswa->prodi_id);
            })
            ->with(['mataKuliah', 'dosen'])
            ->get()
            ->filter(function($jadwal) {
                return $jadwal->terisi < $jadwal->kuota;
            });

        // Ambil jadwal yang sudah diambil
        $krs_terambil = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semester_aktif->id)
            ->pluck('jadwal_kuliah_id')
            ->toArray();

        $jadwal_available = $jadwal_available->reject(function($jadwal) use ($krs_terambil) {
            return in_array($jadwal->id, $krs_terambil);
        });

        return view('mahasiswa.krs.create', compact('jadwal_available', 'semester_aktif', 'mahasiswa'));
    }

    public function store(Request $request)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Tidak ada semester aktif.');
        }

        $validated = $request->validate([
            'jadwal_kuliah_id' => 'required|exists:jadwal_kuliahs,id',
        ]);

        // Cek apakah sudah pernah mengambil
        $existing = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $validated['jadwal_kuliah_id'])
            ->where('semester_id', $semester_aktif->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Mata kuliah ini sudah pernah diambil.');
        }

        // Cek kuota
        $jadwal = JadwalKuliah::findOrFail($validated['jadwal_kuliah_id']);
        if ($jadwal->terisi >= $jadwal->kuota) {
            return back()->with('error', 'Kuota kelas sudah penuh.');
        }

        KRS::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jadwal_kuliah_id' => $validated['jadwal_kuliah_id'],
            'semester_id' => $semester_aktif->id,
            'status' => 'pending',
        ]);

        // Update terisi
        $jadwal->increment('terisi');

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'KRS berhasil ditambahkan. Menunggu persetujuan.');
    }

    public function destroy(KRS $krs)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if ($krs->mahasiswa_id !== $mahasiswa->id) {
            abort(403, 'Unauthorized');
        }

        // Update terisi
        $krs->jadwalKuliah->decrement('terisi');

        $krs->delete();

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'KRS berhasil dihapus.');
    }
}

