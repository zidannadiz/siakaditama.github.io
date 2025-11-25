<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanAkademikExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Mahasiswa::with(['prodi', 'user'])
            ->where('status', 'aktif');

        if (isset($this->filters['prodi_id']) && $this->filters['prodi_id']) {
            $query->where('prodi_id', $this->filters['prodi_id']);
        }

        if (isset($this->filters['semester_id']) && $this->filters['semester_id']) {
            $semester = Semester::find($this->filters['semester_id']);
        } else {
            $semester = Semester::where('status', 'aktif')->first();
        }

        $mahasiswas = $query->orderBy('nim')->get();

        // Calculate IPK for each
        $mahasiswas->transform(function($mahasiswa) use ($semester) {
            $nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
                ->with(['jadwalKuliah.mataKuliah', 'krs.semester']);

            if ($semester) {
                $nilais->whereHas('krs', function($q) use ($semester) {
                    $q->where('semester_id', $semester->id);
                });
            }

            $nilais = $nilais->get();

            $total_sks = $nilais->sum(function($nilai) {
                return $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
            });

            $total_bobot = $nilais->sum(function($nilai) {
                $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $nilai->bobot ?? 0;
                return $sks * $bobot;
            });

            $ipk = $total_sks > 0 ? $total_bobot / $total_sks : 0;

            // Cumulative IPK
            $all_nilais = Nilai::where('mahasiswa_id', $mahasiswa->id)
                ->with(['jadwalKuliah.mataKuliah'])
                ->get();

            $cumulative_sks = $all_nilais->sum(function($nilai) {
                return $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
            });

            $cumulative_bobot = $all_nilais->sum(function($nilai) {
                $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $nilai->bobot ?? 0;
                return $sks * $bobot;
            });

            $ipk_cumulative = $cumulative_sks > 0 ? $cumulative_bobot / $cumulative_sks : 0;

            $mahasiswa->ipk = round($ipk, 2);
            $mahasiswa->ipk_cumulative = round($ipk_cumulative, 2);
            $mahasiswa->total_sks = $total_sks;
            $mahasiswa->cumulative_sks = $cumulative_sks;

            return $mahasiswa;
        });

        return $mahasiswas;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama',
            'Program Studi',
            'Email',
            'IPK Semester',
            'IPK Kumulatif',
            'SKS Semester',
            'SKS Kumulatif',
            'Status Kelulusan',
        ];
    }

    public function map($mahasiswa): array
    {
        static $no = 1;
        $status = ($mahasiswa->ipk_cumulative >= 2.00 && $mahasiswa->cumulative_sks >= 144) ? 'Lulus' : 'Belum Lulus';
        
        return [
            $no++,
            $mahasiswa->nim,
            $mahasiswa->nama,
            $mahasiswa->prodi->nama_prodi ?? '-',
            $mahasiswa->user->email ?? '-',
            $mahasiswa->ipk ?? 0,
            $mahasiswa->ipk_cumulative ?? 0,
            $mahasiswa->total_sks ?? 0,
            $mahasiswa->cumulative_sks ?? 0,
            $status,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 25,
            'E' => 25,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 18,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}

