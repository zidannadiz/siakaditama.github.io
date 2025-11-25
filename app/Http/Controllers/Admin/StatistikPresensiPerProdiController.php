<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistikPresensiPerProdiController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua tahun akademik yang tersedia
        $tahunAkademiks = Semester::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran')
            ->toArray();
        
        // Ambil tahun akademik yang dipilih (default: tahun terbaru)
        $selectedTahun = $request->input('tahun_ajaran', $tahunAkademiks[0] ?? null);
        
        // Ambil semua prodi
        $prodis = Prodi::orderBy('nama_prodi')->get();
        
        // Ambil prodi yang dipilih (default: semua prodi)
        $selectedProdiId = $request->input('prodi_id');
        
        $data = [];
        
        if ($selectedTahun) {
            // Filter prodi jika dipilih
            $prodisToProcess = $selectedProdiId 
                ? $prodis->where('id', $selectedProdiId)
                : $prodis;
            
            foreach ($prodisToProcess as $prodi) {
                // Untuk setiap semester mahasiswa (1-8)
                for ($semesterMahasiswa = 1; $semesterMahasiswa <= 8; $semesterMahasiswa++) {
                    // Ambil mahasiswa di prodi ini dengan semester tertentu
                    $mahasiswas = Mahasiswa::where('prodi_id', $prodi->id)
                        ->where('semester', $semesterMahasiswa)
                        ->where('status', 'aktif')
                        ->pluck('id');
                    
                    if ($mahasiswas->isEmpty()) {
                        continue;
                    }
                    
                    // Ambil presensi mahasiswa-mahasiswa ini untuk tahun akademik tertentu
                    // Caranya: cari jadwal kuliah yang terkait dengan semester akademik tahun tertentu
                    // dan mahasiswa yang mengambilnya melalui KRS
                    $semesterAkademiks = Semester::where('tahun_ajaran', $selectedTahun)
                        ->pluck('id');
                    
                    if ($semesterAkademiks->isEmpty()) {
                        continue;
                    }
                    
                    // Ambil KRS mahasiswa di semester akademik ini
                    $krsList = DB::table('krs')
                        ->whereIn('mahasiswa_id', $mahasiswas)
                        ->whereIn('semester_id', $semesterAkademiks)
                        ->where('status', 'disetujui')
                        ->pluck('jadwal_kuliah_id', 'mahasiswa_id');
                    
                    if ($krsList->isEmpty()) {
                        continue;
                    }
                    
                    $jadwalKuliahIds = $krsList->values()->unique();
                    
                    // Hitung statistik presensi untuk mahasiswa-mahasiswa ini di jadwal kuliah yang mereka ambil
                    $statistik = Presensi::whereIn('jadwal_kuliah_id', $jadwalKuliahIds)
                        ->whereIn('mahasiswa_id', $mahasiswas)
                        ->selectRaw('
                            COUNT(*) as total_presensi,
                            SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                            SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                            SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                            SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
                        ')
                        ->first();
                    
                    // Hitung total mahasiswa
                    $totalMahasiswa = $mahasiswas->count();
                    
                    // Hitung persentase kehadiran
                    $persentaseKehadiran = $statistik->total_presensi > 0 
                        ? round(($statistik->hadir / $statistik->total_presensi) * 100, 2)
                        : 0;
                    
                    if (!isset($data[$prodi->id])) {
                        $data[$prodi->id] = [
                            'prodi' => $prodi->nama_prodi,
                            'kode_prodi' => $prodi->kode_prodi,
                            'semesters' => []
                        ];
                    }
                    
                    $data[$prodi->id]['semesters'][$semesterMahasiswa] = [
                        'semester' => $semesterMahasiswa,
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
        }
        
        return view('admin.statistik-presensi-per-prodi.index', compact(
            'tahunAkademiks',
            'selectedTahun',
            'prodis',
            'selectedProdiId',
            'data'
        ));
    }
}

