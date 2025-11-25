<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\KRS;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        $jadwal_id = request('jadwal_id');
        $pertemuan = request('pertemuan');
        
        $jadwals = JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('status', 'aktif')
            ->with(['mataKuliah', 'semester'])
            ->get();

        $presensis = collect();
        $krs_list = collect();
        
        if ($jadwal_id) {
            $jadwal = JadwalKuliah::findOrFail($jadwal_id);
            
            $krs_list = KRS::where('jadwal_kuliah_id', $jadwal_id)
                ->where('status', 'disetujui')
                ->with('mahasiswa')
                ->get();

            if ($pertemuan) {
                $presensis = Presensi::where('jadwal_kuliah_id', $jadwal_id)
                    ->where('pertemuan', $pertemuan)
                    ->with('mahasiswa')
                    ->get()
                    ->keyBy('mahasiswa_id');
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'jadwals' => $jadwals->map(function($jadwal) {
                    return [
                        'id' => $jadwal->id,
                        'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                        'kode_mk' => $jadwal->mataKuliah->kode_mk ?? null,
                        'semester' => $jadwal->semester->nama ?? null,
                    ];
                }),
                'krs_list' => $krs_list->map(function($krs) {
                    return [
                        'id' => $krs->id,
                        'mahasiswa' => [
                            'id' => $krs->mahasiswa->id ?? null,
                            'nim' => $krs->mahasiswa->nim ?? null,
                            'nama' => $krs->mahasiswa->nama ?? null,
                        ],
                    ];
                }),
                'presensis' => $presensis->map(function($presensi) {
                    return [
                        'id' => $presensi->id,
                        'mahasiswa_id' => $presensi->mahasiswa_id,
                        'pertemuan' => $presensi->pertemuan,
                        'tanggal' => $presensi->tanggal?->toDateString(),
                        'status' => $presensi->status,
                        'catatan' => $presensi->catatan,
                    ];
                })->values(),
            ],
        ]);
    }

    public function create($jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->firstOrFail();

        $krs_list = KRS::where('jadwal_kuliah_id', $jadwal_id)
            ->where('status', 'disetujui')
            ->with('mahasiswa')
            ->get();

        $pertemuan_terakhir = Presensi::where('jadwal_kuliah_id', $jadwal_id)
            ->max('pertemuan') ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'jadwal' => [
                    'id' => $jadwal->id,
                    'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                    'kode_mk' => $jadwal->mataKuliah->kode_mk ?? null,
                    'semester' => $jadwal->semester->nama ?? null,
                ],
                'krs_list' => $krs_list->map(function($krs) {
                    return [
                        'id' => $krs->id,
                        'mahasiswa' => [
                            'id' => $krs->mahasiswa->id ?? null,
                            'nim' => $krs->mahasiswa->nim ?? null,
                            'nama' => $krs->mahasiswa->nama ?? null,
                        ],
                    ];
                }),
                'pertemuan_terakhir' => $pertemuan_terakhir,
            ],
        ]);
    }

    public function store(Request $request, $jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $validated = $request->validate([
            'pertemuan' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.mahasiswa_id' => 'required|exists:mahasiswas,id',
            'presensi.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'presensi.*.catatan' => 'nullable|string|max:255',
        ]);

        $presensis = [];
        foreach ($validated['presensi'] as $presensi_data) {
            $presensi = Presensi::updateOrCreate(
                [
                    'jadwal_kuliah_id' => $jadwal_id,
                    'mahasiswa_id' => $presensi_data['mahasiswa_id'],
                    'pertemuan' => $validated['pertemuan'],
                ],
                [
                    'tanggal' => $validated['tanggal'],
                    'status' => $presensi_data['status'],
                    'catatan' => $presensi_data['catatan'] ?? null,
                ]
            );
            $presensis[] = $presensi;
        }

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil disimpan.',
            'data' => [
                'presensis' => collect($presensis)->map(function($presensi) {
                    return [
                        'id' => $presensi->id,
                        'pertemuan' => $presensi->pertemuan,
                        'status' => $presensi->status,
                    ];
                }),
            ],
        ], 201);
    }

    public function show($jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan',
            ], 404);
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->firstOrFail();

        $krs_list = KRS::where('jadwal_kuliah_id', $jadwal_id)
            ->where('status', 'disetujui')
            ->with('mahasiswa')
            ->get();

        $presensis = Presensi::where('jadwal_kuliah_id', $jadwal_id)
            ->with('mahasiswa')
            ->orderBy('pertemuan')
            ->orderBy('mahasiswa_id')
            ->get()
            ->groupBy('pertemuan');

        $statistik = [];
        foreach ($krs_list as $krs) {
            $mahasiswa_id = $krs->mahasiswa_id;
            $presensi_mahasiswa = Presensi::where('jadwal_kuliah_id', $jadwal_id)
                ->where('mahasiswa_id', $mahasiswa_id)
                ->get();

            $statistik[$mahasiswa_id] = [
                'mahasiswa' => [
                    'id' => $krs->mahasiswa->id ?? null,
                    'nim' => $krs->mahasiswa->nim ?? null,
                    'nama' => $krs->mahasiswa->nama ?? null,
                ],
                'hadir' => $presensi_mahasiswa->where('status', 'hadir')->count(),
                'izin' => $presensi_mahasiswa->where('status', 'izin')->count(),
                'sakit' => $presensi_mahasiswa->where('status', 'sakit')->count(),
                'alpa' => $presensi_mahasiswa->where('status', 'alpa')->count(),
                'total' => $presensi_mahasiswa->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'jadwal' => [
                    'id' => $jadwal->id,
                    'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                    'kode_mk' => $jadwal->mataKuliah->kode_mk ?? null,
                ],
                'presensis' => $presensis->map(function($group, $pertemuan) {
                    return [
                        'pertemuan' => $pertemuan,
                        'presensi' => $group->map(function($presensi) {
                            return [
                                'id' => $presensi->id,
                                'mahasiswa' => [
                                    'id' => $presensi->mahasiswa->id ?? null,
                                    'nim' => $presensi->mahasiswa->nim ?? null,
                                    'nama' => $presensi->mahasiswa->nama ?? null,
                                ],
                                'tanggal' => $presensi->tanggal?->toDateString(),
                                'status' => $presensi->status,
                                'catatan' => $presensi->catatan,
                            ];
                        })->values(),
                    ];
                })->values(),
                'statistik' => array_values($statistik),
            ],
        ]);
    }
}

