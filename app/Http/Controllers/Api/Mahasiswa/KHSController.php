<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KHSController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'semesters' => $semesters->map(function($semester) {
                    return [
                        'id' => $semester->id,
                        'nama' => $semester->nama,
                        'tahun_ajaran' => $semester->tahun_ajaran,
                        'jenis' => $semester->jenis,
                        'status' => $semester->status,
                    ];
                }),
            ],
        ]);
    }

    public function show($semester_id = null)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        if ($semester_id) {
            $semester = Semester::findOrFail($semester_id);
        } else {
            $semester = Semester::where('status', 'aktif')->first();
        }

        if (!$semester) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada semester aktif.',
            ], 404);
        }

        $nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('krs', function($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->with(['jadwalKuliah.mataKuliah', 'dosen', 'krs'])
            ->get();

        $total_sks = $nilais->sum(function($nilai) {
            return $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
        });

        $total_bobot = $nilais->sum(function($nilai) {
            $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
            $bobot = $nilai->bobot ?? 0;
            return $sks * $bobot;
        });

        $ipk = $total_sks > 0 ? $total_bobot / $total_sks : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'semester' => [
                    'id' => $semester->id,
                    'nama' => $semester->nama,
                    'tahun_ajaran' => $semester->tahun_ajaran,
                    'jenis' => $semester->jenis,
                ],
                'nilais' => $nilais->map(function($nilai) {
                    return [
                        'id' => $nilai->id,
                        'mata_kuliah' => $nilai->jadwalKuliah->mataKuliah->nama ?? null,
                        'kode_mk' => $nilai->jadwalKuliah->mataKuliah->kode_mk ?? null,
                        'sks' => $nilai->jadwalKuliah->mataKuliah->sks ?? 0,
                        'dosen' => $nilai->dosen->nama ?? null,
                        'nilai_tugas' => $nilai->nilai_tugas,
                        'nilai_uts' => $nilai->nilai_uts,
                        'nilai_uas' => $nilai->nilai_uas,
                        'nilai_akhir' => $nilai->nilai_akhir,
                        'huruf_mutu' => $nilai->huruf_mutu,
                        'bobot' => $nilai->bobot,
                        'status' => $nilai->status,
                    ];
                }),
                'total_sks' => $total_sks,
                'ipk' => round($ipk, 2),
            ],
        ]);
    }
}

