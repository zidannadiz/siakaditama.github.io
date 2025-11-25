<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\JadwalKuliah;
use App\Models\Semester;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikPresensiController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();
        
        $selectedSemesterId = $request->input('semester_id', $semesters->first()->id ?? null);
        
        $data = [];
        if ($selectedSemesterId) {
            // Ambil semua jadwal kuliah untuk semester yang dipilih
            $jadwalKuliahs = JadwalKuliah::with(['mataKuliah', 'dosen'])
                ->where('semester_id', $selectedSemesterId)
                ->get();
            
            foreach ($jadwalKuliahs as $jadwal) {
                // Hitung statistik presensi per mata kuliah
                $statistik = Presensi::where('jadwal_kuliah_id', $jadwal->id)
                    ->selectRaw('
                        COUNT(*) as total_presensi,
                        SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
                    ')
                    ->first();
                
                // Hitung total mahasiswa yang mengambil mata kuliah ini
                $totalMahasiswa = DB::table('krs')
                    ->where('jadwal_kuliah_id', $jadwal->id)
                    ->where('status', 'disetujui')
                    ->count();
                
                // Hitung persentase kehadiran
                $persentaseKehadiran = $statistik->total_presensi > 0 
                    ? round(($statistik->hadir / $statistik->total_presensi) * 100, 2)
                    : 0;
                
                $data[] = [
                    'mata_kuliah' => $jadwal->mataKuliah->nama_mk ?? 'N/A',
                    'kode_mk' => $jadwal->mataKuliah->kode_mk ?? 'N/A',
                    'dosen' => $jadwal->dosen->nama ?? 'N/A',
                    'total_mahasiswa' => $totalMahasiswa,
                    'total_presensi' => $statistik->total_presensi ?? 0,
                    'hadir' => $statistik->hadir ?? 0,
                    'izin' => $statistik->izin ?? 0,
                    'sakit' => $statistik->sakit ?? 0,
                    'alpa' => $statistik->alpa ?? 0,
                    'persentase_kehadiran' => $persentaseKehadiran,
                ];
            }
        }
        
        return view('admin.statistik-presensi.index', compact('semesters', 'selectedSemesterId', 'data'));
    }
}

