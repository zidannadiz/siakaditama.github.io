<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KalenderAkademik;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KalenderAkademikController extends Controller
{
    /**
     * Get kalender akademik events (untuk semua role)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'mahasiswa';
        
        $start = $request->input('start');
        $end = $request->input('end');
        $jenis = $request->input('jenis');
        $semesterId = $request->input('semester_id');

        $query = KalenderAkademik::with('semester')
            ->forRole($role);

        // Filter by date range
        if ($start) {
            $query->where(function($q) use ($start) {
                $q->where('tanggal_selesai', '>=', $start)
                  ->orWhere(function($query) use ($start) {
                      $query->whereNull('tanggal_selesai')
                            ->where('tanggal_mulai', '>=', $start);
                  });
            });
        }
        if ($end) {
            $query->where('tanggal_mulai', '<=', $end);
        }

        // Filter by jenis
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        // Filter by semester
        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $events = $query->orderBy('tanggal_mulai', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $events->map(function($event) {
                return [
                    'id' => $event->id,
                    'judul' => $event->judul,
                    'deskripsi' => $event->deskripsi,
                    'tanggal_mulai' => $event->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $event->tanggal_selesai?->format('Y-m-d'),
                    'jam_mulai' => $event->jam_mulai?->format('H:i'),
                    'jam_selesai' => $event->jam_selesai?->format('H:i'),
                    'jenis' => $event->jenis,
                    'target_role' => $event->target_role,
                    'warna' => $event->color,
                    'is_important' => $event->is_important,
                    'link' => $event->link,
                    'semester' => $event->semester ? [
                        'id' => $event->semester->id,
                        'nama' => $event->semester->nama_semester,
                        'tahun_ajaran' => $event->semester->tahun_ajaran,
                    ] : null,
                    'created_at' => $event->created_at->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * Get single event detail
     */
    public function show($id)
    {
        $user = Auth::user();
        $role = $user->role ?? 'mahasiswa';

        $event = KalenderAkademik::with('semester')
            ->forRole($role)
            ->find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $event->id,
                'judul' => $event->judul,
                'deskripsi' => $event->deskripsi,
                'tanggal_mulai' => $event->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $event->tanggal_selesai?->format('Y-m-d'),
                'jam_mulai' => $event->jam_mulai?->format('H:i'),
                'jam_selesai' => $event->jam_selesai?->format('H:i'),
                'jenis' => $event->jenis,
                'target_role' => $event->target_role,
                'warna' => $event->color,
                'is_important' => $event->is_important,
                'link' => $event->link,
                'semester' => $event->semester ? [
                    'id' => $event->semester->id,
                    'nama' => $event->semester->nama_semester,
                    'tahun_ajaran' => $event->semester->tahun_ajaran,
                ] : null,
                'created_at' => $event->created_at->toISOString(),
            ],
        ]);
    }
}
