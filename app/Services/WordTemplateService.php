<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use App\Models\TemplateKrsKhs;
use App\Models\Mahasiswa;

class WordTemplateService
{
    /**
     * Generate KRS atau KHS dari template Word
     */
    public function generateDocument($templateId, $mahasiswaId, $semesterId = null, $tanggalCetak = null)
    {
        $template = TemplateKrsKhs::findOrFail($templateId);
        $mahasiswa = Mahasiswa::with(['prodi', 'nilais.jadwalKuliah.mataKuliah', 'nilais.dosen', 'nilais.semester'])
            ->findOrFail($mahasiswaId);
        
        // Get user from mahasiswa
        $mahasiswa->load('user');

        // Pastikan file template ada
        $templatePath = storage_path('app/' . $template->file_path);
        if (!file_exists($templatePath)) {
            throw new \Exception("Template file not found: {$template->file_path}");
        }

        // Buat TemplateProcessor
        $templateProcessor = new TemplateProcessor($templatePath);

        // Ganti placeholder dengan data mahasiswa
        $this->replacePlaceholders($templateProcessor, $mahasiswa, $semesterId, $template->jenis, $tanggalCetak);

        // Generate nama file output
        $jenis = strtoupper($template->jenis);
        $filename = "{$jenis}_{$mahasiswa->nim}_" . date('YmdHis') . '.docx';
        $outputPath = storage_path('app/public/generated/' . $filename);
        
        // Pastikan direktori ada
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        // Simpan file hasil
        $templateProcessor->saveAs($outputPath);

        return [
            'path' => $outputPath,
            'filename' => $filename,
            'url' => asset('storage/generated/' . $filename),
        ];
    }

    /**
     * Replace placeholder di template dengan data mahasiswa
     */
    protected function replacePlaceholders(TemplateProcessor $processor, $mahasiswa, $semesterId = null, $jenis = 'krs', $tanggalCetak = null)
    {
        // Data umum mahasiswa
        $processor->setValue('NIM', $mahasiswa->nim ?? '');
        $processor->setValue('NAMA', $mahasiswa->nama ?? '');
        $processor->setValue('PROGRAM_STUDI', $mahasiswa->prodi->nama_prodi ?? '');
        
        // Tanggal cetak - jika tidak ada, gunakan tanggal sekarang
        if ($tanggalCetak) {
            try {
                $tanggal = \Carbon\Carbon::parse($tanggalCetak)->format('d F Y');
            } catch (\Exception $e) {
                $tanggal = date('d F Y');
            }
        } else {
            $tanggal = date('d F Y');
        }
        $processor->setValue('TANGGAL_CETAK', $tanggal);
        
        // Tahun akademik dan nama semester
        if ($semesterId) {
            $semester = \App\Models\Semester::find($semesterId);
        } else {
            $semester = \App\Models\Semester::where('status', 'aktif')->first();
        }
        
        if ($semester) {
            $tahunAkademik = $semester->tahun_akademik ?? $semester->tahun_ajaran ?? (date('Y') . '/' . (date('Y') + 1));
            $namaSemester = $semester->nama_semester ?? $semester->jenis ?? 'Semester Aktif';
        } else {
            $tahunAkademik = date('Y') . '/' . (date('Y') + 1);
            $namaSemester = 'Semester Aktif';
        }
        
        $processor->setValue('TAHUN_AKADEMIK', $tahunAkademik);
        $processor->setValue('NAMA_SEMESTER', $namaSemester);
        $processor->setValue('SEMESTER', $namaSemester);

        if ($jenis === 'khs') {
            // Data untuk KHS
            $this->replaceKhsData($processor, $mahasiswa, $semesterId);
        } else {
            // Data untuk KRS
            $this->replaceKrsData($processor, $mahasiswa, $semesterId);
        }
    }

    /**
     * Replace data untuk KRS
     */
    protected function replaceKrsData(TemplateProcessor $processor, $mahasiswa, $semesterId = null)
    {
        // Get KRS yang sudah disetujui
        $query = \App\Models\KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui');
        
        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        } else {
            // Get semester aktif jika tidak ada semester yang dipilih
            $semesterAktif = \App\Models\Semester::where('status', 'aktif')->first();
            if ($semesterAktif) {
                $query->where('semester_id', $semesterAktif->id);
            }
        }
        
        $krsList = $query->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'semester'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Replace list mata kuliah di template
        $processor->cloneRow('NO', $krsList->count());
        
        foreach ($krsList as $index => $krs) {
            $no = $index + 1;
            $mk = $krs->jadwalKuliah->mataKuliah ?? null;
            $dosen = $krs->jadwalKuliah->dosen ?? null;
            
            if ($mk) {
                $processor->setValue("NO#{$no}", $no);
                $processor->setValue("KODE_MK#{$no}", $mk->kode_mk ?? '');
                $processor->setValue("NAMA_MK#{$no}", $mk->nama_mk ?? '');
                $processor->setValue("SKS#{$no}", $mk->sks ?? 0);
                $processor->setValue("SEMESTER#{$no}", $mk->semester ?? '');
                $processor->setValue("DOSEN#{$no}", $dosen ? ($dosen->nama ?? '') : '');
            }
        }

        // Total SKS
        $totalSks = $krsList->sum(function($krs) {
            return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
        });
        $processor->setValue('TOTAL_SKS', $totalSks);
    }

    /**
     * Replace data untuk KHS
     */
    protected function replaceKhsData(TemplateProcessor $processor, $mahasiswa, $semesterId = null)
    {
        // Get nilai mahasiswa melalui relasi KRS
        $query = \App\Models\Nilai::where('mahasiswa_id', $mahasiswa->id)
            ->with(['jadwalKuliah.mataKuliah', 'dosen', 'krs.semester']);
        
        if ($semesterId) {
            $query->whereHas('krs', function($q) use ($semesterId) {
                $q->where('semester_id', $semesterId);
            });
        }

        $nilais = $query->get();

        // Hitung IP dan total SKS
        $totalSks = 0;
        $totalNilaiBobot = 0;

        foreach ($nilais as $nilai) {
            $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
            $bobot = $nilai->bobot ?? 0;
            $totalSks += $sks;
            $totalNilaiBobot += ($sks * $bobot);
        }

        $ip = $totalSks > 0 ? $totalNilaiBobot / $totalSks : 0;

        // Replace data IP dan SKS
        $processor->setValue('IP', $totalSks > 0 ? number_format($ip, 2) : '0.00');
        $processor->setValue('TOTAL_SKS', $totalSks);
        
        // Replace data semester jika ada
        if ($semesterId && $nilais->first() && $nilais->first()->krs) {
            $semester = $nilais->first()->krs->semester;
            if ($semester) {
                $processor->setValue('SEMESTER', $semester->nama_semester ?? $semester->jenis ?? '');
                $processor->setValue('TAHUN_AKADEMIK', $semester->tahun_akademik ?? $semester->tahun_ajaran ?? '');
            }
        }

        // Replace list nilai di template
        $processor->cloneRow('NO', $nilais->count());
        
        foreach ($nilais as $index => $nilai) {
            $no = $index + 1;
            $mk = $nilai->jadwalKuliah->mataKuliah;
            $sks = $mk->sks ?? 0;
            $bobot = $nilai->bobot ?? 0;
            $nilaiXBobot = $sks * $bobot;

            $processor->setValue("NO#{$no}", $no);
            $processor->setValue("KODE_MK#{$no}", $mk->kode_mk ?? '');
            $processor->setValue("NAMA_MK#{$no}", $mk->nama_mk ?? '');
            $processor->setValue("SKS#{$no}", $sks);
            $processor->setValue("NILAI#{$no}", $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 2) : '-');
            $processor->setValue("HURUF#{$no}", $nilai->huruf_mutu ?? '-');
            $processor->setValue("BOBOT#{$no}", $bobot ? number_format($bobot, 2) : '-');
            $processor->setValue("NILAI_X_SKS#{$no}", $nilaiXBobot > 0 ? number_format($nilaiXBobot, 2) : '-');
            $processor->setValue("DOSEN#{$no}", $nilai->dosen->nama ?? '-');
        }
    }
}

