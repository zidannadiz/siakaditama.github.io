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
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'semester_id' => 'nullable|exists:semester,id',
        ]);

        try {
            $result = $this->wordTemplateService->generateDocument(
                $request->template_id,
                $request->mahasiswa_id,
                $request->semester_id
            );

            return response()->download($result['path'], $result['filename']);
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
        if (auth()->user()->role === 'admin') {
            $mahasiswa = Mahasiswa::with('prodi')->orderBy('nim')->get();
        } else {
            $mahasiswa = [auth()->user()->mahasiswa];
        }

        return view('krs-khs.generate', compact('templates', 'mahasiswa', 'jenis'));
    }
}
