<?php

namespace App\Http\Controllers\Admin;

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
    public function index()
    {
        $kalenders = KalenderAkademik::with('semester')
            ->select('*') // Explicitly select all columns including deskripsi
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(20);
        
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return view('admin.kalender-akademik.index', compact('kalenders', 'semesters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return view('admin.kalender-akademik.create', compact('semesters'));
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
                // Kirim ke semua role
                NotifikasiService::createForRole('admin', $kalender->judul, $kalender->deskripsi ?? 'Event penting telah ditambahkan ke kalender akademik.', 'warning', route('admin.kalender-akademik.index'));
                NotifikasiService::createForRole('dosen', $kalender->judul, $kalender->deskripsi ?? 'Event penting telah ditambahkan ke kalender akademik.', 'warning', route('dosen.kalender-akademik.index'));
                NotifikasiService::createForRole('mahasiswa', $kalender->judul, $kalender->deskripsi ?? 'Event penting telah ditambahkan ke kalender akademik.', 'warning', route('mahasiswa.kalender-akademik.index'));
            } else {
                // Kirim ke role tertentu
                NotifikasiService::createForRole($targetRole, $kalender->judul, $kalender->deskripsi ?? 'Event penting telah ditambahkan ke kalender akademik.', 'warning', route($targetRole . '.kalender-akademik.index'));
            }
        }

        return redirect()->route('admin.kalender-akademik.index')
            ->with('success', 'Event kalender akademik berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KalenderAkademik $kalenderAkademik)
    {
        $kalenderAkademik->load('semester');
        return redirect()->route('admin.kalender-akademik.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KalenderAkademik $kalenderAkademik)
    {
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return view('admin.kalender-akademik.edit', compact('kalenderAkademik', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KalenderAkademik $kalenderAkademik)
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

        $kalenderAkademik->update($validated);

        return redirect()->route('admin.kalender-akademik.index')
            ->with('success', 'Event kalender akademik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KalenderAkademik $kalenderAkademik)
    {
        $kalenderAkademik->delete();

        return redirect()->route('admin.kalender-akademik.index')
            ->with('success', 'Event kalender akademik berhasil dihapus.');
    }

    /**
     * Get events for FullCalendar (JSON API)
     */
    public function getEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        $role = $request->input('role', 'admin');

        $events = KalenderAkademik::query()
            ->where(function($q) use ($start, $end) {
                if ($start) {
                    $q->where(function($query) use ($start) {
                        $query->where('tanggal_selesai', '>=', $start)
                              ->orWhere(function($q2) use ($start) {
                                  $q2->whereNull('tanggal_selesai')
                                     ->where('tanggal_mulai', '>=', $start);
                              });
                    });
                }
                if ($end) {
                    $q->where('tanggal_mulai', '<=', $end);
                }
            })
            // Admin bisa melihat semua event, tidak perlu filter berdasarkan target_role
            // ->where(function($q) use ($role) {
            //     $q->where('target_role', 'semua')
            //       ->orWhere('target_role', $role);
            // })
            ->get()
            ->map(function($event) {
                $endDate = $event->tanggal_selesai 
                    ? $event->tanggal_selesai->format('Y-m-d') 
                    : $event->tanggal_mulai->format('Y-m-d');
                
                // Jika ada jam, tambahkan waktu
                if ($event->jam_mulai) {
                    $jamMulai = is_string($event->jam_mulai) ? $event->jam_mulai : $event->jam_mulai->format('H:i:s');
                    $start = $event->tanggal_mulai->format('Y-m-d') . 'T' . $jamMulai;
                } else {
                    $start = $event->tanggal_mulai->format('Y-m-d');
                }
                
                if ($event->jam_selesai) {
                    $jamSelesai = is_string($event->jam_selesai) ? $event->jam_selesai : $event->jam_selesai->format('H:i:s');
                    $end = $endDate . 'T' . $jamSelesai;
                } else {
                    // Untuk event tanpa jam, end date adalah hari setelah tanggal selesai (exclusive)
                    // Tapi hanya jika ada tanggal_selesai yang berbeda dengan tanggal_mulai
                    if ($event->tanggal_selesai && $event->tanggal_selesai->format('Y-m-d') != $event->tanggal_mulai->format('Y-m-d')) {
                        // Event multi-day: end date = tanggal_selesai + 1 hari (exclusive) agar semua hari diwarnai
                        $end = date('Y-m-d', strtotime($endDate . ' +1 day'));
                    } else {
                        // Event satu hari: end date = tanggal_mulai + 1 hari (exclusive)
                        $end = date('Y-m-d', strtotime($event->tanggal_mulai->format('Y-m-d') . ' +1 day'));
                    }
                }

                return [
                    'id' => $event->id,
                    'title' => $event->judul,
                    'start' => $start,
                    'end' => $end,
                    'backgroundColor' => $event->color,
                    'borderColor' => $event->color,
                    'description' => $event->deskripsi,
                    'url' => $event->link,
                    'extendedProps' => [
                        'jenis' => $event->jenis,
                        'is_important' => $event->is_important,
                        'target_role' => $event->target_role,
                        'deskripsi' => $event->deskripsi,
                        'description' => $event->deskripsi,
                    ],
                ];
            });

        return response()->json($events);
    }
}
