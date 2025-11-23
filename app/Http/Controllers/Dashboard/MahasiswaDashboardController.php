<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\KRS;
use App\Models\JadwalKuliah;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaDashboardController extends Controller
{
    public function __invoke()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $semester_aktif = \App\Models\Semester::where('status', 'aktif')->first();

        $krs_semester_ini = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semester_aktif?->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
            ->get();

        $jadwal_hari_ini = collect();
        if ($semester_aktif) {
            $krs_ids = $krs_semester_ini->pluck('jadwal_kuliah_id');
            $hari_ini = now()->format('l');
            $hari_indonesia = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu',
            ];
            
            $jadwal_hari_ini = JadwalKuliah::whereIn('id', $krs_ids)
                ->where('hari', $hari_indonesia[$hari_ini] ?? 'Senin')
                ->where('status', 'aktif')
                ->with(['mataKuliah', 'dosen'])
                ->orderBy('jam_mulai')
                ->get();
        }

        $total_sks = $krs_semester_ini->sum(function($krs) {
            return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
        });

        $pengumuman_terbaru = Pengumuman::where(function($query) {
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

        return view('dashboard.mahasiswa', compact('mahasiswa', 'krs_semester_ini', 'jadwal_hari_ini', 'total_sks', 'pengumuman_terbaru', 'semester_aktif'));
    }
}

