<?php

namespace App\Http\Controllers\KrsKhs;

use App\Http\Controllers\Controller;
use App\Services\WordTemplateService;
use App\Models\TemplateKrsKhs;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class GenerateKrsKhsController extends Controller
{
    protected $wordTemplateService;

    public function __construct(WordTemplateService $wordTemplateService)
    {
        $this->wordTemplateService = $wordTemplateService;
    }

    /**
     * Generate KRS atau KHS untuk mahasiswa
     */
    public function generate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:template_krs_khs,id',
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'semester_id' => 'nullable|exists:semesters,id',
        ]);

        $action = $request->input('action', 'download');
        $jenis = $request->input('jenis', 'krs');

        try {
            if ($action === 'cetak') {
                $mahasiswa = Mahasiswa::with('prodi')->findOrFail($request->mahasiswa_id);
                $semesterId = $request->semester_id;
                
                // Optional: Get Semester
                $semester = $semesterId ? \App\Models\Semester::find($semesterId) : \App\Models\Semester::where('status', 'aktif')->first();

                if ($jenis === 'khs') {
                    // Logic untuk KHS
                    $query = \App\Models\Nilai::where('mahasiswa_id', $mahasiswa->id)
                        ->with(['jadwalKuliah.mataKuliah', 'dosen', 'krs.semester']);
                    if ($semesterId) {
                        $query->whereHas('krs', function($q) use ($semesterId) {
                            $q->where('semester_id', $semesterId);
                        });
                    }
                    $dataList = $query->get();
                    $viewName = 'krs-khs.print-khs';
                } else {
                    // Logic untuk KRS
                    $query = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
                        ->where('status', 'disetujui');
                    if ($semesterId) {
                        $query->where('semester_id', $semesterId);
                    } else {
                        $semesterAktif = \App\Models\Semester::where('status', 'aktif')->first();
                        if ($semesterAktif) $query->where('semester_id', $semesterAktif->id);
                    }
                    $dataList = $query->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'semester'])->get();
                    $viewName = 'krs-khs.print-krs';
                }
                
                return view($viewName, compact('mahasiswa', 'dataList', 'semester'));
            }

            // Jika action == 'download', gunakan WordTemplateService
            $result = $this->wordTemplateService->generateDocument(
                $request->template_id,
                $request->mahasiswa_id,
                $request->semester_id
            );

            $mahasiswa = Mahasiswa::findOrFail($request->mahasiswa_id);
            $customFilename = strtoupper($jenis) . '_' . $mahasiswa->nim . '.docx';

            return response()->download($result['path'], $customFilename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Show form untuk generate KRS/KHS
     */
    public function showForm(Request $request)
    {
        $jenis = $request->jenis ?? 'krs';
        
        // Get active template
        $templates = TemplateKrsKhs::where('jenis', $jenis)
            ->where('is_active', true)
            ->get();

        // Get mahasiswa list (for admin) or current user (for mahasiswa)
        if (auth()->user()->role !== 'mahasiswa') {
            $query = Mahasiswa::with('prodi')->orderBy('nim');
            // Jika admin_prodi, batasi hanya mahasiswa di prodinya
            if (auth()->user()->role === 'admin_prodi') {
                $query->where('prodi_id', auth()->user()->prodi_id);
            }
            $mahasiswa = $query->get();
        } else {
            $mhs = auth()->user()->mahasiswa;
            $mahasiswa = $mhs ? [$mhs] : [];
        }

        return view('krs-khs.generate', compact('templates', 'mahasiswa', 'jenis'));
    }
}
