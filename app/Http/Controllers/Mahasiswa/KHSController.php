<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KHSController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $semester_id = request('semester_id');
        
        if ($semester_id) {
            $semester = Semester::findOrFail($semester_id);
        } else {
            $semester = Semester::where('status', 'aktif')->first();
        }

        if (!$semester) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Tidak ada semester aktif.');
        }

        $nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('krs', function($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->with(['jadwalKuliah.mataKuliah', 'dosen'])
            ->get();

        $total_sks = $nilais->sum(function($nilai) {
            return $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
        });

        $total_bobot = $nilais->sum(function($nilai) {
            $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
            $bobot = $nilai->bobot ?? 0;
            return $sks * $bobot;
        });

        $ipk = $total_sks > 0 ? $total_bobot / $total_sks : 0;

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();

        return view('mahasiswa.khs.index', compact('nilais', 'semester', 'semesters', 'total_sks', 'ipk', 'mahasiswa'));
    }
}

