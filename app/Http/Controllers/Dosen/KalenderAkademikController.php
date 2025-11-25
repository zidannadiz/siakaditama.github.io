<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KalenderAkademik;
use Illuminate\Http\Request;

class KalenderAkademikController extends Controller
{
    /**
     * Display kalender akademik untuk dosen
     */
    public function index()
    {
        return view('dosen.kalender-akademik.index');
    }

    /**
     * Get events for FullCalendar (JSON API)
     */
    public function getEvents(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $events = KalenderAkademik::query()
            ->forRole('dosen')
            ->when($start, function($q) use ($start) {
                $q->where('tanggal_selesai', '>=', $start)
                  ->orWhere(function($query) use ($start) {
                      $query->whereNull('tanggal_selesai')
                            ->where('tanggal_mulai', '>=', $start);
                  });
            })
            ->when($end, function($q) use ($end) {
                $q->where('tanggal_mulai', '<=', $end);
            })
            ->get()
            ->map(function($event) {
                $endDate = $event->tanggal_selesai 
                    ? $event->tanggal_selesai->format('Y-m-d') 
                    : $event->tanggal_mulai->format('Y-m-d');
                
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
                        'deskripsi' => $event->deskripsi,
                        'description' => $event->deskripsi,
                    ],
                ];
            });

        return response()->json($events);
    }
}
