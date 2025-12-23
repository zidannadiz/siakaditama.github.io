<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanAkademikController extends Controller
{
    /**
     * Get laporan akademik with filters
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['prodi', 'user'])
            ->where('status', 'aktif');

        // Filter by prodi
        if ($request->prodi_id) {
            $query->where('prodi_id', $request->prodi_id);
        }

        // Filter by semester
        if ($request->semester_id) {
            $semester = Semester::find($request->semester_id);
        } else {
            $semester = Semester::where('status', 'aktif')->first();
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nim', 'like', '%' . $request->search . '%')
                  ->orWhere('nama', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $perPage = $request->per_page ?? 20;
        $mahasiswas = $query->orderBy('nim')->paginate($perPage);

        // Calculate IPK for each mahasiswa
        $mahasiswas->getCollection()->transform(function($mahasiswa) use ($semester) {
            $nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
                ->with(['jadwalKuliah.mataKuliah', 'krs.semester']);

            if ($semester) {
                $nilais->whereHas('krs', function($q) use ($semester) {
                    $q->where('semester_id', $semester->id);
                });
            }

            $nilais = $nilais->get();

            $total_sks = $nilais->sum(function($nilai) {
                return $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
            });

            $total_bobot = $nilais->sum(function($nilai) {
                $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $nilai->bobot ?? 0;
                return $sks * $bobot;
            });

            $ipk = $total_sks > 0 ? $total_bobot / $total_sks : 0;

            // Calculate cumulative IPK (all semesters)
            $all_nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
                ->with(['jadwalKuliah.mataKuliah'])
                ->get();

            $cumulative_sks = $all_nilais->sum(function($nilai) {
                return $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
            });

            $cumulative_bobot = $all_nilais->sum(function($nilai) {
                $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $nilai->bobot ?? 0;
                return $sks * $bobot;
            });

            $ipk_cumulative = $cumulative_sks > 0 ? $cumulative_bobot / $cumulative_sks : 0;

            return [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'prodi' => $mahasiswa->prodi ? [
                    'id' => $mahasiswa->prodi->id,
                    'nama' => $mahasiswa->prodi->nama_prodi,
                ] : null,
                'ipk' => round($ipk, 2),
                'ipk_cumulative' => round($ipk_cumulative, 2),
                'total_sks' => $total_sks,
                'cumulative_sks' => $cumulative_sks,
            ];
        });

        // Statistics
        $stats = [
            'total_mahasiswa' => Mahasiswa::where('status', 'aktif')->count(),
            'total_prodi' => Prodi::count(),
            'avg_ipk' => 0,
            'lulus' => 0,
            'tidak_lulus' => 0,
        ];

        // Calculate average IPK and graduation status
        $all_mahasiswas = Mahasiswa::where('status', 'aktif');
        if ($request->prodi_id) {
            $all_mahasiswas->where('prodi_id', $request->prodi_id);
        }
        $all_mahasiswas = $all_mahasiswas->get();
        
        $total_ipk = 0;
        $count_with_ipk = 0;

        foreach ($all_mahasiswas as $mhs) {
            $all_nilais = Nilai::where('mahasiswa_id', $mhs->id)
                ->with(['jadwalKuliah.mataKuliah'])
                ->get();

            $cumulative_sks = $all_nilais->sum(function($nilai) {
                return $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
            });

            $cumulative_bobot = $all_nilais->sum(function($nilai) {
                $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $nilai->bobot ?? 0;
                return $sks * $bobot;
            });

            if ($cumulative_sks > 0) {
                $ipk = $cumulative_bobot / $cumulative_sks;
                $total_ipk += $ipk;
                $count_with_ipk++;

                // Assume lulus if IPK >= 2.00 and SKS >= 144
                if ($ipk >= 2.00 && $cumulative_sks >= 144) {
                    $stats['lulus']++;
                } else {
                    $stats['tidak_lulus']++;
                }
            }
        }

        $stats['avg_ipk'] = $count_with_ipk > 0 ? round($total_ipk / $count_with_ipk, 2) : 0;
        $stats['total_mahasiswa'] = $all_mahasiswas->count();

        $prodis = Prodi::orderBy('nama_prodi')->get();
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'mahasiswas' => $mahasiswas->items(),
                'pagination' => [
                    'current_page' => $mahasiswas->currentPage(),
                    'last_page' => $mahasiswas->lastPage(),
                    'per_page' => $mahasiswas->perPage(),
                    'total' => $mahasiswas->total(),
                ],
                'stats' => $stats,
                'semester' => $semester ? [
                    'id' => $semester->id,
                    'nama' => $semester->nama_semester,
                    'tahun_ajaran' => $semester->tahun_ajaran,
                ] : null,
                'prodis' => $prodis->map(function($prodi) {
                    return [
                        'id' => $prodi->id,
                        'nama' => $prodi->nama_prodi,
                    ];
                }),
                'semesters' => $semesters->map(function($semester) {
                    return [
                        'id' => $semester->id,
                        'nama' => $semester->nama_semester,
                        'tahun_ajaran' => $semester->tahun_ajaran,
                        'jenis' => $semester->jenis,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get statistik presensi per semester
     */
    public function statistikPresensi(Request $request)
    {
        $semester_id = $request->semester_id;
        
        if ($semester_id) {
            $semester = Semester::find($semester_id);
        } else {
            $semester = Semester::where('status', 'aktif')->first();
        }

        if (!$semester) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada semester aktif.',
            ], 404);
        }

        // Get presensi data per jadwal
        $presensis = DB::table('presensis')
            ->join('jadwal_kuliahs', 'presensis.jadwal_kuliah_id', '=', 'jadwal_kuliahs.id')
            ->join('krs', function($join) use ($semester) {
                $join->on('presensis.mahasiswa_id', '=', 'krs.mahasiswa_id')
                     ->on('presensis.jadwal_kuliah_id', '=', 'krs.jadwal_kuliah_id')
                     ->where('krs.semester_id', $semester->id);
            })
            ->join('mahasiswas', 'presensis.mahasiswa_id', '=', 'mahasiswas.id')
            ->join('mata_kuliahs', 'jadwal_kuliahs.mata_kuliah_id', '=', 'mata_kuliahs.id')
            ->select(
                'presensis.mahasiswa_id',
                'mahasiswas.nim',
                'mahasiswas.nama',
                'mata_kuliahs.nama_mk as nama_mata_kuliah',
                'jadwal_kuliahs.id as jadwal_id',
                DB::raw('COUNT(*) as total_presensi'),
                DB::raw('SUM(CASE WHEN presensis.status = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN presensis.status = "izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN presensis.status = "sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN presensis.status = "alpha" THEN 1 ELSE 0 END) as alpha')
            )
            ->groupBy('presensis.mahasiswa_id', 'mahasiswas.nim', 'mahasiswas.nama', 'mata_kuliahs.nama_mk', 'jadwal_kuliahs.id')
            ->get();

        // Group by mahasiswa
        $statistik = [];
        foreach ($presensis as $presensi) {
            if (!isset($statistik[$presensi->mahasiswa_id])) {
                $statistik[$presensi->mahasiswa_id] = [
                    'nim' => $presensi->nim,
                    'nama' => $presensi->nama,
                    'total_presensi' => 0,
                    'total_hadir' => 0,
                    'total_izin' => 0,
                    'total_sakit' => 0,
                    'total_alpha' => 0,
                    'mata_kuliah' => [],
                ];
            }

            $statistik[$presensi->mahasiswa_id]['total_presensi'] += $presensi->total_presensi;
            $statistik[$presensi->mahasiswa_id]['total_hadir'] += $presensi->hadir;
            $statistik[$presensi->mahasiswa_id]['total_izin'] += $presensi->izin;
            $statistik[$presensi->mahasiswa_id]['total_sakit'] += $presensi->sakit;
            $statistik[$presensi->mahasiswa_id]['total_alpha'] += $presensi->alpha;

            $statistik[$presensi->mahasiswa_id]['mata_kuliah'][] = [
                'nama' => $presensi->nama_mata_kuliah,
                'hadir' => $presensi->hadir,
                'izin' => $presensi->izin,
                'sakit' => $presensi->sakit,
                'alpha' => $presensi->alpha,
                'total' => $presensi->total_presensi,
            ];
        }

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'statistik' => array_values($statistik),
                'semester' => [
                    'id' => $semester->id,
                    'nama' => $semester->nama_semester,
                    'tahun_ajaran' => $semester->tahun_ajaran,
                ],
                'semesters' => $semesters->map(function($sem) {
                    return [
                        'id' => $sem->id,
                        'nama' => $sem->nama_semester,
                        'tahun_ajaran' => $sem->tahun_ajaran,
                        'jenis' => $sem->jenis,
                    ];
                }),
            ],
        ]);
    }
}
