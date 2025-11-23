<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\KRS;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ExportController extends Controller
{
    public function exportKRS($semester_id = null)
    {
        $mahasiswa = Mahasiswa::with('prodi')->where('user_id', Auth::id())->firstOrFail();
        
        if ($semester_id) {
            $semester = Semester::findOrFail($semester_id);
        } else {
            $semester = Semester::where('status', 'aktif')->firstOrFail();
        }

        $krs_list = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semester->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'semester'])
            ->get();

        $total_sks = $krs_list->sum(function($krs) {
            return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
        });

        $pdf = PDF::loadView('mahasiswa.export.krs', compact('mahasiswa', 'krs_list', 'semester', 'total_sks'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('margin-top', 15);
        $pdf->setOption('margin-bottom', 15);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);
        
        $filename = 'KRS_' . $mahasiswa->nim . '_' . str_replace(['/', '\\'], '-', $semester->nama_semester) . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
        
        return $pdf->download($filename);
    }

    public function exportKHS($semester_id = null)
    {
        $mahasiswa = Mahasiswa::with('prodi')->where('user_id', Auth::id())->firstOrFail();
        
        if ($semester_id) {
            $semester = Semester::findOrFail($semester_id);
        } else {
            $semester = Semester::where('status', 'aktif')->firstOrFail();
        }

        $nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('krs', function($query) use ($semester) {
                $query->where('semester_id', $semester->id);
            })
            ->with(['jadwalKuliah.mataKuliah', 'dosen', 'krs'])
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

        $pdf = PDF::loadView('mahasiswa.export.khs', compact('mahasiswa', 'nilais', 'semester', 'total_sks', 'ipk'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('margin-top', 15);
        $pdf->setOption('margin-bottom', 15);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('margin-right', 15);
        
        $filename = 'KHS_' . $mahasiswa->nim . '_' . str_replace(['/', '\\'], '-', $semester->nama_semester) . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
        
        return $pdf->download($filename);
    }
}

