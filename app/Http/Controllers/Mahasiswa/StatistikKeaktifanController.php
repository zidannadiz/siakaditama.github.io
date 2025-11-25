<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\KRS;
use App\Models\Semester;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatistikKeaktifanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }
        
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();
        
        $selectedSemesterIds = $request->input('semester_ids', []);
        if (empty($selectedSemesterIds) && $semesters->count() >= 2) {
            // Default: pilih 2 semester terakhir
            $selectedSemesterIds = $semesters->take(2)->pluck('id')->toArray();
        }
        
        $data = [];
        if (!empty($selectedSemesterIds)) {
            // Ambil semua KRS yang disetujui untuk semester yang dipilih
            $krss = KRS::with(['jadwalKuliah.mataKuliah', 'semester'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'disetujui')
                ->whereIn('semester_id', $selectedSemesterIds)
                ->get();
            
            // Group by semester
            $dataBySemester = [];
            foreach ($krss as $krs) {
                $semesterId = $krs->semester_id;
                $semester = Semester::find($semesterId);
                $semesterName = $semester ? ($semester->nama_semester ?? ($semester->jenis . ' ' . $semester->tahun_ajaran)) : 'Semester ' . $semesterId;
                
                if (!isset($dataBySemester[$semesterId])) {
                    $dataBySemester[$semesterId] = [
                        'semester_name' => $semesterName,
                        'mata_kuliah' => [],
                    ];
                }
                
                // Hitung statistik presensi per mata kuliah
                $statistik = Presensi::where('jadwal_kuliah_id', $krs->jadwal_kuliah_id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->selectRaw('
                        COUNT(*) as total_presensi,
                        SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
                    ')
                    ->first();
                
                // Hitung persentase kehadiran
                $persentaseKehadiran = $statistik->total_presensi > 0 
                    ? round(($statistik->hadir / $statistik->total_presensi) * 100, 2)
                    : 0;
                
                $dataBySemester[$semesterId]['mata_kuliah'][] = [
                    'mata_kuliah' => $krs->jadwalKuliah->mataKuliah->nama_mk ?? 'N/A',
                    'kode_mk' => $krs->jadwalKuliah->mataKuliah->kode_mk ?? 'N/A',
                    'total_presensi' => $statistik->total_presensi ?? 0,
                    'hadir' => $statistik->hadir ?? 0,
                    'izin' => $statistik->izin ?? 0,
                    'sakit' => $statistik->sakit ?? 0,
                    'alpa' => $statistik->alpa ?? 0,
                    'persentase_kehadiran' => $persentaseKehadiran,
                ];
            }
            
            // Hitung rata-rata per semester
            foreach ($dataBySemester as $semesterId => $semesterData) {
                $totalMataKuliah = count($semesterData['mata_kuliah']);
                $totalPersentase = 0;
                $totalHadir = 0;
                $totalPresensi = 0;
                
                foreach ($semesterData['mata_kuliah'] as $mk) {
                    $totalPersentase += $mk['persentase_kehadiran'];
                    $totalHadir += $mk['hadir'];
                    $totalPresensi += $mk['total_presensi'];
                }
                
                $dataBySemester[$semesterId]['rata_rata_kehadiran'] = $totalMataKuliah > 0 
                    ? round($totalPersentase / $totalMataKuliah, 2)
                    : 0;
                $dataBySemester[$semesterId]['total_hadir'] = $totalHadir;
                $dataBySemester[$semesterId]['total_presensi'] = $totalPresensi;
            }
            
            $data = $dataBySemester;
        }
        
        return view('mahasiswa.statistik-keaktifan.index', compact('semesters', 'selectedSemesterIds', 'data'));
    }
}

