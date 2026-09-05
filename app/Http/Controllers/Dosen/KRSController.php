<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KRS;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KRSController extends Controller
{
    /**
     * Tampilkan daftar KRS mahasiswa pada kelas yang diajar dosen.
     * Dosen hanya bisa melihat (View Only) — tidak ada aksi CRUD.
     */
    public function index(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        $query = KRS::with(['mahasiswa.prodi', 'jadwalKuliah.mataKuliah', 'semester'])
            ->whereHas('jadwalKuliah', function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id);
            })
            ->latest();

        // Filter opsional berdasarkan semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Filter opsional berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $krs_list  = $query->paginate(20)->withQueryString();
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('dosen.krs.index', compact('krs_list', 'semesters', 'dosen'));
    }
}
