<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Prodi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAkademikExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanAkademikController extends Controller
{
    /**
     * Display laporan akademik page
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

        $mahasiswas = $query->orderBy('nim')->paginate(20);

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

            $mahasiswa->ipk = round($ipk, 2);
            $mahasiswa->ipk_cumulative = round($ipk_cumulative, 2);
            $mahasiswa->total_sks = $total_sks;
            $mahasiswa->cumulative_sks = $cumulative_sks;

            return $mahasiswa;
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
        $all_mahasiswas = Mahasiswa::where('status', 'aktif')->get();
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

                // Assume lulus if IPK >= 2.00 and SKS >= 144 (adjust as needed)
                if ($ipk >= 2.00 && $cumulative_sks >= 144) {
                    $stats['lulus']++;
                } else {
                    $stats['tidak_lulus']++;
                }
            }
        }

        $stats['avg_ipk'] = $count_with_ipk > 0 ? round($total_ipk / $count_with_ipk, 2) : 0;

        // Filter stats by prodi
        if ($request->prodi_id) {
            $prodi_mahasiswas = Mahasiswa::where('status', 'aktif')
                ->where('prodi_id', $request->prodi_id)
                ->get();

            $stats['total_mahasiswa'] = $prodi_mahasiswas->count();
            $total_ipk = 0;
            $count_with_ipk = 0;
            $stats['lulus'] = 0;
            $stats['tidak_lulus'] = 0;

            foreach ($prodi_mahasiswas as $mhs) {
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

                    if ($ipk >= 2.00 && $cumulative_sks >= 144) {
                        $stats['lulus']++;
                    } else {
                        $stats['tidak_lulus']++;
                    }
                }
            }

            $stats['avg_ipk'] = $count_with_ipk > 0 ? round($total_ipk / $count_with_ipk, 2) : 0;
        }

        $prodis = Prodi::orderBy('nama_prodi')->get();
        $semesters = Semester::orderBy('tahun_akademik', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return view('admin.laporan.akademik.index', compact(
            'mahasiswas', 
            'stats', 
            'prodis', 
            'semesters', 
            'semester'
        ));
    }

    /**
     * Export laporan akademik to Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new LaporanAkademikExport($request->all()),
            'laporan-akademik-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export laporan akademik to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Mahasiswa::with(['prodi', 'user'])
            ->where('status', 'aktif');

        if ($request->prodi_id) {
            $query->where('prodi_id', $request->prodi_id);
        }

        if ($request->semester_id) {
            $semester = Semester::find($request->semester_id);
        } else {
            $semester = Semester::where('status', 'aktif')->first();
        }

        $mahasiswas = $query->orderBy('nim')->get();

        // Calculate IPK for each
        $mahasiswas->transform(function($mahasiswa) use ($semester) {
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

            // Cumulative IPK
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

            $mahasiswa->ipk = round($ipk, 2);
            $mahasiswa->ipk_cumulative = round($ipk_cumulative, 2);
            $mahasiswa->total_sks = $total_sks;
            $mahasiswa->cumulative_sks = $cumulative_sks;

            return $mahasiswa;
        });

        $pdf = Pdf::loadView('admin.laporan.akademik.pdf', [
            'mahasiswas' => $mahasiswas,
            'semester' => $semester,
            'filters' => $request->all(),
        ]);

        return $pdf->download('laporan-akademik-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Statistik presensi per semester
     */
    public function statistikPresensi(Request $request)
    {
        $semester_id = $request->semester_id;
        
        if ($semester_id) {
            $semester = Semester::findOrFail($semester_id);
        } else {
            $semester = Semester::where('status', 'aktif')->first();
        }

        if (!$semester) {
            return redirect()->route('admin.laporan.akademik.index')
                ->with('error', 'Tidak ada semester aktif.');
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

        $semesters = Semester::orderBy('tahun_akademik', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return view('admin.laporan.akademik.presensi', compact('statistik', 'semester', 'semesters'));
    }
}

