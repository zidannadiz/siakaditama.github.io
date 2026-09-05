<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        $query = JadwalKuliah::where('dosen_id', $dosen->id)
            ->with(['mataKuliah.prodi', 'semester'])
            ->where('status', 'aktif');

        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        $jadwals = $query->orderByRaw("CASE 
            WHEN hari = 'Senin' THEN 1
            WHEN hari = 'Selasa' THEN 2
            WHEN hari = 'Rabu' THEN 3
            WHEN hari = 'Kamis' THEN 4
            WHEN hari = 'Jumat' THEN 5
            WHEN hari = 'Sabtu' THEN 6
            WHEN hari = 'Minggu' THEN 7
            ELSE 8 END")
            ->orderBy('jam_mulai')
            ->paginate(15);

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('dosen.jadwal.index', compact('jadwals', 'semesters', 'dosen'));
    }
}
