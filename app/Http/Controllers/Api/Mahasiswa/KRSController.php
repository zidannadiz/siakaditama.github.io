<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KRS;
use App\Models\JadwalKuliah;
use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KRSController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada semester aktif.',
            ], 404);
        }

        $krs_list = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semester_aktif->id)
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'semester'])
            ->get();

        $total_sks = $krs_list->sum(function($krs) {
            return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'krs_list' => $krs_list->map(function($krs) {
                    return [
                        'id' => $krs->id,
                        'mata_kuliah' => $krs->jadwalKuliah->mataKuliah->nama ?? null,
                        'kode_mk' => $krs->jadwalKuliah->mataKuliah->kode_mk ?? null,
                        'sks' => $krs->jadwalKuliah->mataKuliah->sks ?? 0,
                        'dosen' => $krs->jadwalKuliah->dosen->nama ?? null,
                        'hari' => $krs->jadwalKuliah->hari ?? null,
                        'jam_mulai' => $krs->jadwalKuliah->jam_mulai ?? null,
                        'jam_selesai' => $krs->jadwalKuliah->jam_selesai ?? null,
                        'ruangan' => $krs->jadwalKuliah->ruangan ?? null,
                        'status' => $krs->status,
                        'semester' => $krs->semester->nama ?? null,
                    ];
                }),
                'total_sks' => $total_sks,
                'semester_aktif' => [
                    'id' => $semester_aktif->id,
                    'nama' => $semester_aktif->nama,
                    'tahun_ajaran' => $semester_aktif->tahun_ajaran,
                ],
            ],
        ]);
    }

    public function create()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada semester aktif.',
            ], 404);
        }

        $jadwal_available = JadwalKuliah::where('semester_id', $semester_aktif->id)
            ->where('status', 'aktif')
            ->whereHas('mataKuliah', function($query) use ($mahasiswa) {
                $query->where('prodi_id', $mahasiswa->prodi_id);
            })
            ->with(['mataKuliah', 'dosen'])
            ->get()
            ->filter(function($jadwal) {
                return $jadwal->terisi < $jadwal->kuota;
            });

        $krs_terambil = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semester_aktif->id)
            ->pluck('jadwal_kuliah_id')
            ->toArray();

        $jadwal_available = $jadwal_available->reject(function($jadwal) use ($krs_terambil) {
            return in_array($jadwal->id, $krs_terambil);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'jadwal_available' => $jadwal_available->map(function($jadwal) {
                    return [
                        'id' => $jadwal->id,
                        'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                        'kode_mk' => $jadwal->mataKuliah->kode_mk ?? null,
                        'sks' => $jadwal->mataKuliah->sks ?? 0,
                        'dosen' => $jadwal->dosen->nama ?? null,
                        'hari' => $jadwal->hari,
                        'jam_mulai' => $jadwal->jam_mulai,
                        'jam_selesai' => $jadwal->jam_selesai,
                        'ruangan' => $jadwal->ruangan,
                        'kuota' => $jadwal->kuota,
                        'terisi' => $jadwal->terisi,
                        'sisa_kuota' => $jadwal->kuota - $jadwal->terisi,
                    ];
                })->values(),
                'semester_aktif' => [
                    'id' => $semester_aktif->id,
                    'nama' => $semester_aktif->nama,
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan',
            ], 404);
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada semester aktif.',
            ], 404);
        }

        $validated = $request->validate([
            'jadwal_kuliah_id' => 'required|exists:jadwal_kuliahs,id',
        ]);

        $existing = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $validated['jadwal_kuliah_id'])
            ->where('semester_id', $semester_aktif->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Mata kuliah ini sudah pernah diambil.',
            ], 422);
        }

        $jadwal = JadwalKuliah::with('mataKuliah')->findOrFail($validated['jadwal_kuliah_id']);
        if ($jadwal->terisi >= $jadwal->kuota) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota kelas sudah penuh.',
            ], 422);
        }

        $krs = KRS::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jadwal_kuliah_id' => $validated['jadwal_kuliah_id'],
            'semester_id' => $semester_aktif->id,
            'status' => 'pending',
        ]);

        $jadwal->increment('terisi');

        // Kirim notifikasi ke admin
        try {
            $mataKuliah = $jadwal->mataKuliah;
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            
            if ($adminUsers->count() > 0) {
                NotifikasiService::createForRole(
                    'admin',
                    'Pengajuan KRS Baru',
                    "Mahasiswa {$mahasiswa->nama} ({$mahasiswa->nim}) mengajukan KRS untuk mata kuliah {$mataKuliah->nama_mk}.",
                    'info',
                    route('admin.krs.index')
                );
            }
        } catch (\Exception $e) {
            \Log::error('Error creating notification for KRS: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil ditambahkan. Menunggu persetujuan.',
            'data' => [
                'id' => $krs->id,
                'status' => $krs->status,
            ],
        ], 201);
    }

    public function destroy(KRS $krs)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if ($krs->mahasiswa_id !== $mahasiswa->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $krs->jadwalKuliah->decrement('terisi');
        $krs->delete();

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil dihapus.',
        ]);
    }
}

