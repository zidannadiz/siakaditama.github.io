<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\KalenderAkademik;
use App\Models\Semester;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class KalenderAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KalenderAkademik::with('semester')
            ->orderBy('tanggal_mulai', 'desc');

        // Filter by jenis
        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by semester
        if ($request->semester_id) {
            $query->where('semester_id', $request->semester_id);
        }

        // Filter by target_role
        if ($request->target_role) {
            $query->where('target_role', $request->target_role);
        }

        // Search
        if ($request->search) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $kalenders = $query->paginate(20);
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'kalenders' => $kalenders->map(function($kalender) {
                    return [
                        'id' => $kalender->id,
                        'judul' => $kalender->judul,
                        'deskripsi' => $kalender->deskripsi,
                        'tanggal_mulai' => $kalender->tanggal_mulai->format('Y-m-d'),
                        'tanggal_selesai' => $kalender->tanggal_selesai?->format('Y-m-d'),
                        'jam_mulai' => $kalender->jam_mulai?->format('H:i'),
                        'jam_selesai' => $kalender->jam_selesai?->format('H:i'),
                        'jenis' => $kalender->jenis,
                        'target_role' => $kalender->target_role,
                        'warna' => $kalender->color,
                        'is_important' => $kalender->is_important,
                        'link' => $kalender->link,
                        'semester' => $kalender->semester ? [
                            'id' => $kalender->semester->id,
                            'nama' => $kalender->semester->nama_semester,
                            'tahun_ajaran' => $kalender->semester->tahun_ajaran,
                        ] : null,
                        'created_at' => $kalender->created_at->toISOString(),
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
                'pagination' => [
                    'current_page' => $kalenders->currentPage(),
                    'last_page' => $kalenders->lastPage(),
                    'total' => $kalenders->total(),
                ],
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'jenis' => 'required|in:semester,krs,pembayaran,ujian,libur,kegiatan,pengumuman,lainnya',
            'target_role' => 'required|in:semua,admin,dosen,mahasiswa',
            'semester_id' => 'nullable|exists:semesters,id',
            'warna' => 'nullable|string|max:7',
            'is_important' => 'boolean',
            'link' => 'nullable|url',
        ]);

        $kalender = KalenderAkademik::create($validated);

        // Kirim notifikasi jika event penting
        if ($kalender->is_important) {
            $targetRole = $kalender->target_role;
            
            if ($targetRole === 'semua') {
                NotifikasiService::createForRole('admin', $kalender->judul, $kalender->deskripsi ?? 'Event penting telah ditambahkan ke kalender akademik.', 'warning', '/kalender-akademik');
                NotifikasiService::createForRole('dosen', $kalender->judul, $kalender->deskripsi ?? 'Event penting telah ditambahkan ke kalender akademik.', 'warning', '/kalender-akademik');
                NotifikasiService::createForRole('mahasiswa', $kalender->judul, $kalender->deskripsi ?? 'Event penting telah ditambahkan ke kalender akademik.', 'warning', '/kalender-akademik');
            } else {
                NotifikasiService::createForRole($targetRole, $kalender->judul, $kalender->deskripsi ?? 'Event penting telah ditambahkan ke kalender akademik.', 'warning', '/kalender-akademik');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Event kalender akademik berhasil ditambahkan',
            'data' => [
                'id' => $kalender->id,
                'judul' => $kalender->judul,
                'tanggal_mulai' => $kalender->tanggal_mulai->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kalender = KalenderAkademik::with('semester')->find($id);

        if (!$kalender) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $kalender->id,
                'judul' => $kalender->judul,
                'deskripsi' => $kalender->deskripsi,
                'tanggal_mulai' => $kalender->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $kalender->tanggal_selesai?->format('Y-m-d'),
                'jam_mulai' => $kalender->jam_mulai?->format('H:i'),
                'jam_selesai' => $kalender->jam_selesai?->format('H:i'),
                'jenis' => $kalender->jenis,
                'target_role' => $kalender->target_role,
                'warna' => $kalender->color,
                'is_important' => $kalender->is_important,
                'link' => $kalender->link,
                'semester' => $kalender->semester ? [
                    'id' => $kalender->semester->id,
                    'nama' => $kalender->semester->nama_semester,
                    'tahun_ajaran' => $kalender->semester->tahun_ajaran,
                ] : null,
                'created_at' => $kalender->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kalender = KalenderAkademik::find($id);

        if (!$kalender) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'jenis' => 'required|in:semester,krs,pembayaran,ujian,libur,kegiatan,pengumuman,lainnya',
            'target_role' => 'required|in:semua,admin,dosen,mahasiswa',
            'semester_id' => 'nullable|exists:semesters,id',
            'warna' => 'nullable|string|max:7',
            'is_important' => 'boolean',
            'link' => 'nullable|url',
        ]);

        $kalender->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event kalender akademik berhasil diperbarui',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kalender = KalenderAkademik::find($id);

        if (!$kalender) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan',
            ], 404);
        }

        $kalender->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event kalender akademik berhasil dihapus',
        ]);
    }
}
