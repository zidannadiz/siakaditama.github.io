<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\JadwalKuliah;
use App\Models\KRS;
use App\Models\Pengumuman;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\Nilai;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'total_mahasiswa' => Mahasiswa::where('status', 'aktif')->count(),
            'total_dosen' => Dosen::where('status', 'aktif')->count(),
            'total_mata_kuliah' => MataKuliah::count(),
            'total_jadwal' => JadwalKuliah::where('status', 'aktif')->count(),
            'krs_pending' => KRS::where('status', 'pending')->count(),
            'krs_approved' => KRS::where('status', 'disetujui')->count(),
            'total_presensi' => Presensi::count(),
        ];

        // Grafik Mahasiswa per Prodi
        $mahasiswa_per_prodi = Prodi::withCount(['mahasiswas' => function($query) {
            $query->where('status', 'aktif');
        }])->get()->map(function($prodi) {
            return [
                'label' => $prodi->nama_prodi,
                'value' => $prodi->mahasiswas_count
            ];
        });

        // Grafik KRS per Semester
        $krs_per_semester = Semester::withCount('krs')->orderBy('tahun_akademik')->orderBy('nama_semester')->get()->map(function($semester) {
            return [
                'label' => $semester->nama_semester . ' ' . $semester->tahun_akademik,
                'value' => $semester->krs_count
            ];
        });

        // Grafik Distribusi Nilai (Huruf Mutu)
        $distribusi_nilai = Nilai::select('huruf_mutu', DB::raw('count(*) as total'))
            ->whereNotNull('huruf_mutu')
            ->groupBy('huruf_mutu')
            ->orderByRaw("CASE huruf_mutu 
                WHEN 'A' THEN 1 
                WHEN 'A-' THEN 2 
                WHEN 'B+' THEN 3 
                WHEN 'B' THEN 4 
                WHEN 'B-' THEN 5 
                WHEN 'C+' THEN 6 
                WHEN 'C' THEN 7 
                WHEN 'C-' THEN 8 
                WHEN 'D' THEN 9 
                WHEN 'E' THEN 10 
                ELSE 11 END")
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->huruf_mutu ?? 'Belum',
                    'value' => $item->total
                ];
            });

        // Grafik Status Presensi
        $status_presensi = Presensi::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'label' => ucfirst($item->status),
                    'value' => $item->total
                ];
            });

        // Grafik KRS per Status
        $krs_per_status = KRS::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'label' => ucfirst($item->status),
                    'value' => $item->total
                ];
            });

        // Statistik Nilai (Rata-rata IPK)
        $rata_rata_ipk = Nilai::whereNotNull('bobot')
            ->select(DB::raw('AVG(bobot) as avg_ipk'))
            ->first();

        $pengumuman_terbaru = Pengumuman::where(function($query) {
                $query->where('published_at', '<=', now())
                    ->orWhereNull('published_at');
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact(
            'stats', 
            'pengumuman_terbaru',
            'mahasiswa_per_prodi',
            'krs_per_semester',
            'distribusi_nilai',
            'status_presensi',
            'krs_per_status',
            'rata_rata_ipk'
        ));
    }
}

