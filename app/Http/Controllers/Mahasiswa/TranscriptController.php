<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TranscriptController extends Controller
{
    /**
     * Show transcript page
     */
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        return view('mahasiswa.transcript.index', compact('mahasiswa'));
    }

    /**
     * Generate and download transcript PDF
     */
    public function download()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())
            ->with(['prodi', 'user'])
            ->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        // Get all nilai grouped by semester
        $nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->with(['jadwalKuliah.mataKuliah', 'dosen', 'krs.semester'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(function($nilai) {
                return $nilai->krs->semester_id ?? 0;
            });

        // Calculate statistics
        $total_sks = 0;
        $total_bobot = 0;
        $semester_data = [];

        foreach ($nilais as $semester_id => $nilai_group) {
            $semester = Semester::find($semester_id);
            if (!$semester) continue;

            $semester_sks = 0;
            $semester_bobot = 0;

            foreach ($nilai_group as $nilai) {
                $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $nilai->bobot ?? 0;
                
                if ($sks > 0 && $bobot > 0) {
                    $semester_sks += $sks;
                    $semester_bobot += ($sks * $bobot);
                    
                    $total_sks += $sks;
                    $total_bobot += ($sks * $bobot);
                }
            }

            $ip_semester = $semester_sks > 0 ? $semester_bobot / $semester_sks : 0;

            $semester_data[] = [
                'semester' => $semester,
                'nilais' => $nilai_group,
                'total_sks' => $semester_sks,
                'ip' => round($ip_semester, 2),
            ];
        }

        // Sort by semester (check available fields)
        usort($semester_data, function($a, $b) {
            $semA = $a['semester'];
            $semB = $b['semester'];
            
            // Try tahun_akademik first, then tahun_ajaran
            $yearA = $semA->tahun_akademik ?? $semA->tahun_ajaran ?? '';
            $yearB = $semB->tahun_akademik ?? $semB->tahun_ajaran ?? '';
            
            if ($yearA != $yearB) {
                return strcmp($yearB, $yearA); // Descending
            }
            
            // Try nama_semester first, then jenis
            $nameA = $semA->nama_semester ?? $semA->jenis ?? '';
            $nameB = $semB->nama_semester ?? $semB->jenis ?? '';
            return strcmp($nameB, $nameA); // Descending
        });

        // Calculate IPK (cumulative)
        $ipk = $total_sks > 0 ? $total_bobot / $total_sks : 0;

        // Generate PDF
        $pdf = Pdf::loadView('mahasiswa.transcript.pdf', [
            'mahasiswa' => $mahasiswa,
            'semester_data' => $semester_data,
            'total_sks' => $total_sks,
            'ipk' => round($ipk, 2),
            'generated_at' => now(),
        ]);

        $filename = 'Transkrip_Nilai_' . $mahasiswa->nim . '_' . date('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }
}

