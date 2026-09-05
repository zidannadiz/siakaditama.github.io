<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\KRS;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();
        $semester_aktif = $request->filled('semester_id')
            ? Semester::find($request->semester_id)
            : Semester::where('status', 'aktif')->first();

        $jadwals = collect();

        if ($semester_aktif) {
            $krsList = KRS::where('mahasiswa_id', $mahasiswa->id)
                ->where('semester_id', $semester_aktif->id)
                ->where('status', 'disetujui')
                ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen'])
                ->get();

            $jadwals = $krsList->pluck('jadwalKuliah')->filter();

            // Sort by day and time
            $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
            $jadwals = $jadwals->sortBy(function ($item) use ($dayOrder) {
                $day = $dayOrder[$item->hari] ?? 8;
                return sprintf('%02d-%s', $day, $item->jam_mulai);
            });
        }

        return view('mahasiswa.jadwal.index', compact('jadwals', 'semesters', 'semester_aktif', 'mahasiswa'));
    }
}
