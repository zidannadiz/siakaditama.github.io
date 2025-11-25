<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KRS;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        $jadwal_id = request('jadwal_id');
        
        $krs_list = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.semester'])
            ->get();

        $presensis = collect();
        $statistik = null;
        
        if ($jadwal_id) {
            $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
                ->where('jadwal_kuliah_id', $jadwal_id)
                ->where('status', 'disetujui')
                ->firstOrFail();

            $presensis = Presensi::where('jadwal_kuliah_id', $jadwal_id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->orderBy('pertemuan')
                ->get();

            $statistik = [
                'hadir' => $presensis->where('status', 'hadir')->count(),
                'izin' => $presensis->where('status', 'izin')->count(),
                'sakit' => $presensis->where('status', 'sakit')->count(),
                'alpa' => $presensis->where('status', 'alpa')->count(),
                'total' => $presensis->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'krs_list' => $krs_list->map(function($krs) {
                    return [
                        'id' => $krs->jadwalKuliah->id ?? null,
                        'mata_kuliah' => $krs->jadwalKuliah->mataKuliah->nama ?? null,
                        'kode_mk' => $krs->jadwalKuliah->mataKuliah->kode_mk ?? null,
                        'dosen' => $krs->jadwalKuliah->dosen->nama ?? null,
                        'hari' => $krs->jadwalKuliah->hari ?? null,
                        'jam_mulai' => $krs->jadwalKuliah->jam_mulai ?? null,
                        'jam_selesai' => $krs->jadwalKuliah->jam_selesai ?? null,
                    ];
                }),
                'presensis' => $presensis->map(function($presensi) {
                    return [
                        'id' => $presensi->id,
                        'pertemuan' => $presensi->pertemuan,
                        'tanggal' => $presensi->tanggal?->toDateString(),
                        'status' => $presensi->status,
                        'catatan' => $presensi->catatan,
                    ];
                }),
                'statistik' => $statistik,
            ],
        ]);
    }

    public function show($jadwal_id)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $jadwal_id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
            ->firstOrFail();

        $presensis = Presensi::where('jadwal_kuliah_id', $jadwal_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('pertemuan')
            ->get();

        $statistik = [
            'hadir' => $presensis->where('status', 'hadir')->count(),
            'izin' => $presensis->where('status', 'izin')->count(),
            'sakit' => $presensis->where('status', 'sakit')->count(),
            'alpa' => $presensis->where('status', 'alpa')->count(),
            'total' => $presensis->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'jadwal' => [
                    'id' => $krs->jadwalKuliah->id ?? null,
                    'mata_kuliah' => $krs->jadwalKuliah->mataKuliah->nama ?? null,
                    'kode_mk' => $krs->jadwalKuliah->mataKuliah->kode_mk ?? null,
                    'dosen' => $krs->jadwalKuliah->dosen->nama ?? null,
                ],
                'presensis' => $presensis->map(function($presensi) {
                    return [
                        'id' => $presensi->id,
                        'pertemuan' => $presensi->pertemuan,
                        'tanggal' => $presensi->tanggal?->toDateString(),
                        'status' => $presensi->status,
                        'catatan' => $presensi->catatan,
                    ];
                }),
                'statistik' => $statistik,
            ],
        ]);
    }
}

