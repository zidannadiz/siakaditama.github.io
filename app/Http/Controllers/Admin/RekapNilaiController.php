<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;

class RekapNilaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Nilai::with([
            'mahasiswa.prodi',
            'jadwalKuliah.mataKuliah',
            'dosen',
            'krs.semester'
        ])->latest();

        // If kaprodi, scope to their prodi
        if (auth()->user()->isKaprodi()) {
            $query->whereHas('mahasiswa', function ($q) {
                $q->where('prodi_id', auth()->user()->prodi_id);
            });
        }

        // Filter by semester
        if ($request->filled('semester_id')) {
            $query->whereHas('krs', function ($q) use ($request) {
                $q->where('semester_id', $request->semester_id);
            });
        }

        // Search by mahasiswa name or NIM or course
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', function ($mq) use ($search) {
                    $mq->where('nama', 'like', "%{$search}%")
                       ->orWhere('nim', 'like', "%{$search}%");
                })->orWhereHas('jadwalKuliah.mataKuliah', function ($mkq) use ($search) {
                    $mkq->where('nama_mk', 'like', "%{$search}%")
                        ->orWhere('kode_mk', 'like', "%{$search}%");
                });
            });
        }

        $nilais = $query->paginate(20)->withQueryString();
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('admin.rekap-nilai.index', compact('nilais', 'semesters'));
    }
}
