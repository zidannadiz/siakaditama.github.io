<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $data = [];

        switch ($role) {
            case 'admin':
                $data = $this->getAdminDashboard();
                break;
            case 'dosen':
                $data = $this->getDosenDashboard($user);
                break;
            case 'mahasiswa':
                $data = $this->getMahasiswaDashboard($user);
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function getAdminDashboard()
    {
        $totalMahasiswa = \App\Models\Mahasiswa::count();
        $totalDosen = \App\Models\Dosen::count();
        $totalProdi = \App\Models\Prodi::count();
        $totalMataKuliah = \App\Models\MataKuliah::count();
        $krsPending = \App\Models\KRS::where('status', 'pending')->count();

        return [
            'role' => 'admin',
            'statistics' => [
                'total_mahasiswa' => $totalMahasiswa,
                'total_dosen' => $totalDosen,
                'total_prodi' => $totalProdi,
                'total_mata_kuliah' => $totalMataKuliah,
                'krs_pending' => $krsPending,
            ],
        ];
    }

    private function getDosenDashboard($user)
    {
        $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
        
        if (!$dosen) {
            return [
                'role' => 'dosen',
                'error' => 'Data dosen tidak ditemukan',
            ];
        }

        $semesterAktif = \App\Models\Semester::where('status', 'aktif')->first();
        
        $jadwalKuliah = \App\Models\JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('semester_id', $semesterAktif?->id)
            ->where('status', 'aktif')
            ->with(['mataKuliah', 'semester'])
            ->get();

        $hariIni = now()->format('l');
        $hariIndonesia = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        $jadwalHariIni = $jadwalKuliah->filter(function($jadwal) use ($hariIndonesia, $hariIni) {
            return $jadwal->hari === ($hariIndonesia[$hariIni] ?? 'Senin');
        })->values();

        return [
            'role' => 'dosen',
            'dosen' => [
                'id' => $dosen->id,
                'nidn' => $dosen->nidn,
                'nama' => $dosen->nama,
            ],
            'semester_aktif' => $semesterAktif ? [
                'id' => $semesterAktif->id,
                'nama' => $semesterAktif->nama,
                'tahun_ajaran' => $semesterAktif->tahun_ajaran,
            ] : null,
            'jadwal_kuliah' => $jadwalKuliah->map(function($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                    'hari' => $jadwal->hari,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'ruangan' => $jadwal->ruangan,
                ];
            }),
            'jadwal_hari_ini' => $jadwalHariIni->map(function($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'ruangan' => $jadwal->ruangan,
                ];
            }),
        ];
    }

    private function getMahasiswaDashboard($user)
    {
        $mahasiswa = \App\Models\Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            return [
                'role' => 'mahasiswa',
                'error' => 'Data mahasiswa tidak ditemukan',
            ];
        }

        $semesterAktif = \App\Models\Semester::where('status', 'aktif')->first();

        $krsSemesterIni = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semesterAktif?->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
            ->get();

        $hariIni = now()->format('l');
        $hariIndonesia = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        $krsIds = $krsSemesterIni->pluck('jadwal_kuliah_id');
        $jadwalHariIni = \App\Models\JadwalKuliah::whereIn('id', $krsIds)
            ->where('hari', $hariIndonesia[$hariIni] ?? 'Senin')
            ->where('status', 'aktif')
            ->with(['mataKuliah', 'dosen'])
            ->orderBy('jam_mulai')
            ->get();

        $totalSks = $krsSemesterIni->sum(function($krs) {
            return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
        });

        $pengumumanTerbaru = \App\Models\Pengumuman::where(function($query) {
                $query->where('target', 'semua')
                    ->orWhere('target', 'mahasiswa');
            })
            ->where(function($query) {
                $query->where('published_at', '<=', now())
                    ->orWhereNull('published_at');
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'role' => 'mahasiswa',
            'mahasiswa' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'prodi' => $mahasiswa->prodi->nama ?? null,
            ],
            'semester_aktif' => $semesterAktif ? [
                'id' => $semesterAktif->id,
                'nama' => $semesterAktif->nama,
                'tahun_ajaran' => $semesterAktif->tahun_ajaran,
            ] : null,
            'krs_semester_ini' => $krsSemesterIni->map(function($krs) {
                return [
                    'id' => $krs->id,
                    'mata_kuliah' => $krs->jadwalKuliah->mataKuliah->nama ?? null,
                    'dosen' => $krs->jadwalKuliah->dosen->nama ?? null,
                    'hari' => $krs->jadwalKuliah->hari ?? null,
                    'jam_mulai' => $krs->jadwalKuliah->jam_mulai ?? null,
                    'jam_selesai' => $krs->jadwalKuliah->jam_selesai ?? null,
                    'ruangan' => $krs->jadwalKuliah->ruangan ?? null,
                    'sks' => $krs->jadwalKuliah->mataKuliah->sks ?? 0,
                ];
            }),
            'jadwal_hari_ini' => $jadwalHariIni->map(function($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                    'dosen' => $jadwal->dosen->nama ?? null,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'ruangan' => $jadwal->ruangan,
                ];
            }),
            'total_sks' => $totalSks,
            'pengumuman_terbaru' => $pengumumanTerbaru->map(function($pengumuman) {
                return [
                    'id' => $pengumuman->id,
                    'judul' => $pengumuman->judul,
                    'isi' => $pengumuman->isi,
                    'is_pinned' => $pengumuman->is_pinned,
                    'published_at' => $pengumuman->published_at?->toISOString(),
                ];
            }),
        ];
    }
}

