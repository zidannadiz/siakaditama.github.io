<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\KRS;
use App\Models\Nilai;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal_id = request('jadwal_id');
        
        $jadwals = JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('status', 'aktif')
            ->with(['mataKuliah', 'semester'])
            ->get();

        $nilais = collect();
        
        if ($jadwal_id) {
            $nilais = Nilai::where('dosen_id', $dosen->id)
                ->where('jadwal_kuliah_id', $jadwal_id)
                ->with(['mahasiswa', 'jadwalKuliah.mataKuliah'])
                ->get();
        }

        return view('dosen.nilai.index', compact('jadwals', 'nilais', 'jadwal_id'));
    }

    public function create($jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->firstOrFail();

        $krs_list = KRS::where('jadwal_kuliah_id', $jadwal_id)
            ->where('status', 'disetujui')
            ->with('mahasiswa')
            ->get();

        return view('dosen.nilai.create', compact('jadwal', 'krs_list'));
    }

    public function store(Request $request, $jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $validated = $request->validate([
            'krs_id' => 'required|array',
            'krs_id.*' => 'required|exists:krs,id',
            'nilai_tugas' => 'required|array',
            'nilai_tugas.*' => 'nullable|numeric|min:0|max:100',
            'nilai_uts' => 'required|array',
            'nilai_uts.*' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'required|array',
            'nilai_uas.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['krs_id'] as $index => $krs_id) {
            $krs = KRS::findOrFail($krs_id);
            
            $nilai_tugas = $validated['nilai_tugas'][$index] ?? null;
            $nilai_uts = $validated['nilai_uts'][$index] ?? null;
            $nilai_uas = $validated['nilai_uas'][$index] ?? null;

            // Hitung nilai akhir (30% tugas + 30% UTS + 40% UAS)
            $nilai_akhir = null;
            if ($nilai_tugas !== null && $nilai_uts !== null && $nilai_uas !== null) {
                $nilai_akhir = ($nilai_tugas * 0.3) + ($nilai_uts * 0.3) + ($nilai_uas * 0.4);
            }

            // Konversi ke huruf mutu dan bobot
            $huruf_mutu = null;
            $bobot = null;
            if ($nilai_akhir !== null) {
                if ($nilai_akhir >= 85) {
                    $huruf_mutu = 'A';
                    $bobot = 4.00;
                } elseif ($nilai_akhir >= 80) {
                    $huruf_mutu = 'A-';
                    $bobot = 3.75;
                } elseif ($nilai_akhir >= 75) {
                    $huruf_mutu = 'B+';
                    $bobot = 3.50;
                } elseif ($nilai_akhir >= 70) {
                    $huruf_mutu = 'B';
                    $bobot = 3.00;
                } elseif ($nilai_akhir >= 65) {
                    $huruf_mutu = 'B-';
                    $bobot = 2.75;
                } elseif ($nilai_akhir >= 60) {
                    $huruf_mutu = 'C+';
                    $bobot = 2.50;
                } elseif ($nilai_akhir >= 55) {
                    $huruf_mutu = 'C';
                    $bobot = 2.00;
                } elseif ($nilai_akhir >= 50) {
                    $huruf_mutu = 'C-';
                    $bobot = 1.75;
                } elseif ($nilai_akhir >= 40) {
                    $huruf_mutu = 'D';
                    $bobot = 1.00;
                } else {
                    $huruf_mutu = 'E';
                    $bobot = 0.00;
                }
            }

            $status = ($nilai_akhir !== null) ? 'selesai' : 'sedang';

            Nilai::updateOrCreate(
                [
                    'krs_id' => $krs_id,
                    'mahasiswa_id' => $krs->mahasiswa_id,
                    'jadwal_kuliah_id' => $jadwal_id,
                    'dosen_id' => $dosen->id,
                ],
                [
                    'nilai_tugas' => $nilai_tugas,
                    'nilai_uts' => $nilai_uts,
                    'nilai_uas' => $nilai_uas,
                    'nilai_akhir' => $nilai_akhir,
                    'huruf_mutu' => $huruf_mutu,
                    'bobot' => $bobot,
                    'status' => $status,
                ]
            );
        }

        // Buat notifikasi untuk mahasiswa yang nilainya diinput
        foreach ($validated['krs_id'] as $krs_id) {
            $krs = KRS::findOrFail($krs_id);
            NotifikasiService::create(
                $krs->mahasiswa->user_id,
                'Nilai Baru',
                "Nilai untuk mata kuliah {$jadwal->mataKuliah->nama_mk} telah diinput.",
                'info',
                route('mahasiswa.khs.index')
            );
        }

        return redirect()->route('dosen.nilai.index', ['jadwal_id' => $jadwal_id])
            ->with('success', 'Nilai berhasil disimpan.');
    }

    public function edit(Nilai $nilai)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if ($nilai->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        $nilai->load(['mahasiswa', 'jadwalKuliah.mataKuliah', 'krs']);

        return view('dosen.nilai.edit', compact('nilai'));
    }

    public function update(Request $request, Nilai $nilai)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if ($nilai->dosen_id !== $dosen->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        // Hitung nilai akhir
        $nilai_akhir = null;
        if ($validated['nilai_tugas'] !== null && $validated['nilai_uts'] !== null && $validated['nilai_uas'] !== null) {
            $nilai_akhir = ($validated['nilai_tugas'] * 0.3) + ($validated['nilai_uts'] * 0.3) + ($validated['nilai_uas'] * 0.4);
        }

        // Konversi ke huruf mutu dan bobot
        $huruf_mutu = null;
        $bobot = null;
        if ($nilai_akhir !== null) {
            if ($nilai_akhir >= 85) {
                $huruf_mutu = 'A';
                $bobot = 4.00;
            } elseif ($nilai_akhir >= 80) {
                $huruf_mutu = 'A-';
                $bobot = 3.75;
            } elseif ($nilai_akhir >= 75) {
                $huruf_mutu = 'B+';
                $bobot = 3.50;
            } elseif ($nilai_akhir >= 70) {
                $huruf_mutu = 'B';
                $bobot = 3.00;
            } elseif ($nilai_akhir >= 65) {
                $huruf_mutu = 'B-';
                $bobot = 2.75;
            } elseif ($nilai_akhir >= 60) {
                $huruf_mutu = 'C+';
                $bobot = 2.50;
            } elseif ($nilai_akhir >= 55) {
                $huruf_mutu = 'C';
                $bobot = 2.00;
            } elseif ($nilai_akhir >= 50) {
                $huruf_mutu = 'C-';
                $bobot = 1.75;
            } elseif ($nilai_akhir >= 40) {
                $huruf_mutu = 'D';
                $bobot = 1.00;
            } else {
                $huruf_mutu = 'E';
                $bobot = 0.00;
            }
        }

        $status = ($nilai_akhir !== null) ? 'selesai' : 'sedang';

        $nilai->update([
            'nilai_tugas' => $validated['nilai_tugas'],
            'nilai_uts' => $validated['nilai_uts'],
            'nilai_uas' => $validated['nilai_uas'],
            'nilai_akhir' => $nilai_akhir,
            'huruf_mutu' => $huruf_mutu,
            'bobot' => $bobot,
            'status' => $status,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('dosen.nilai.index', ['jadwal_id' => $nilai->jadwal_kuliah_id])
            ->with('success', 'Nilai berhasil diperbarui.');
    }
}

