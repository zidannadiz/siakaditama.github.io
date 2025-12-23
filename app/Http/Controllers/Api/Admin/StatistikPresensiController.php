<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\JadwalKuliah;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikPresensiController extends Controller
{
    /**
     * Get statistik presensi per mata kuliah
     */
    public function index(Request $request)
    {
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();
        
        $selectedSemesterId = $request->input('semester_id', $semesters->first()->id ?? null);
        
        $data = [];
        if ($selectedSemesterId) {
            // Get all jadwal kuliah for selected semester
            $jadwalKuliahs = JadwalKuliah::with(['mataKuliah', 'dosen'])
                ->where('semester_id', $selectedSemesterId)
                ->get();
            
            foreach ($jadwalKuliahs as $jadwal) {
                // Calculate presensi statistics per mata kuliah
                $statistik = Presensi::where('jadwal_kuliah_id', $jadwal->id)
                    ->selectRaw('
                        COUNT(*) as total_presensi,
                        SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
                    ')
                    ->first();
                
                // Count total mahasiswa taking this mata kuliah
                $totalMahasiswa = DB::table('krs')
                    ->where('jadwal_kuliah_id', $jadwal->id)
                    ->where('status', 'disetujui')
                    ->count();
                
                // Calculate attendance percentage
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
        
        return response()->json([
            'success' => true,
            'data' => [
                'statistik' => $data,
                'semesters' => $semesters->map(function($semester) {
                    return [
                        'id' => $semester->id,
                        'nama' => $semester->nama_semester,
                        'tahun_ajaran' => $semester->tahun_ajaran,
                        'jenis' => $semester->jenis,
                    ];
                }),
                'selected_semester_id' => $selectedSemesterId,
            ],
        ]);
    }
}
