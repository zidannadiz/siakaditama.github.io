<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class GenerateTemplateExample extends Command
{
    protected $signature = 'template:generate-example {jenis=krs}';
    protected $description = 'Generate contoh template Word untuk KRS atau KHS';

    public function handle()
    {
        $jenis = strtolower($this->argument('jenis'));
        
        if (!in_array($jenis, ['krs', 'khs'])) {
            $this->error('Jenis harus krs atau khs');
            return 1;
        }

        $phpWord = new PhpWord();
        
        // Set document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator('SIAKAD System');
        $properties->setTitle('Template ' . strtoupper($jenis));
        $properties->setDescription('Contoh template untuk ' . strtoupper($jenis));

        $section = $phpWord->addSection([
            'marginLeft' => 1134,   // 2 cm
            'marginRight' => 1134,  // 2 cm
            'marginTop' => 1134,    // 2 cm
            'marginBottom' => 1134, // 2 cm
        ]);

        if ($jenis === 'krs') {
            $this->generateKrsTemplate($section);
        } else {
            $this->generateKhsTemplate($section);
        }

        // Save file
        $filename = 'Template_' . strtoupper($jenis) . '_Contoh.docx';
        $filePath = storage_path('app/public/' . $filename);
        
        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $phpWord->save($filePath, 'Word2007');

        $this->info("Template contoh berhasil dibuat: {$filename}");
        $this->info("Lokasi: storage/app/public/{$filename}");
        $this->info("Anda bisa download file ini dan gunakan sebagai template!");

        return 0;
    }

    protected function generateKrsTemplate($section)
    {
        // Title
        $section->addText('KARTU RENCANA STUDI (KRS)', [
            'bold' => true,
            'size' => 16,
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 240,
        ]);

        $section->addText('', [], ['spaceAfter' => 120]);

        // Student Info
        $section->addText('NIM            : {NIM}', [], ['spaceAfter' => 120]);
        $section->addText('Nama           : {NAMA}', [], ['spaceAfter' => 120]);
        $section->addText('Program Studi  : {PROGRAM_STUDI}', [], ['spaceAfter' => 120]);
        $section->addText('Tahun Akademik : {TAHUN_AKADEMIK}', [], ['spaceAfter' => 120]);
        $section->addText('Tanggal Cetak  : {TANGGAL_CETAK}', [], ['spaceAfter' => 240]);

        // Table Header
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);

        // Header Row
        $table->addRow();
        $table->addCell(1000)->addText('No', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText('Kode MK', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(4000)->addText('Nama Mata Kuliah', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(1500)->addText('SKS', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText('Semester', ['bold' => true], ['alignment' => Jc::CENTER]);

        // Data Row (Template row - akan di-clone)
        $table->addRow();
        $table->addCell(1000)->addText('{NO}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText('{KODE_MK}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(4000)->addText('{NAMA_MK}');
        $table->addCell(1500)->addText('{SKS}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(2000)->addText('{SEMESTER}', [], ['alignment' => Jc::CENTER]);
    }

    protected function generateKhsTemplate($section)
    {
        // Title
        $section->addText('KARTU HASIL STUDI (KHS)', [
            'bold' => true,
            'size' => 16,
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 240,
        ]);

        $section->addText('', [], ['spaceAfter' => 120]);

        // Student Info
        $section->addText('NIM            : {NIM}', [], ['spaceAfter' => 120]);
        $section->addText('Nama           : {NAMA}', [], ['spaceAfter' => 120]);
        $section->addText('Program Studi  : {PROGRAM_STUDI}', [], ['spaceAfter' => 120]);
        $section->addText('Semester       : {SEMESTER}', [], ['spaceAfter' => 120]);
        $section->addText('Tahun Akademik : {TAHUN_AKADEMIK}', [], ['spaceAfter' => 120]);
        $section->addText('Tanggal Cetak  : {TANGGAL_CETAK}', [], ['spaceAfter' => 240]);

        // IP and Total SKS
        $section->addText('IP Semester    : {IP}', ['bold' => true], ['spaceAfter' => 120]);
        $section->addText('Total SKS      : {TOTAL_SKS}', ['bold' => true], ['spaceAfter' => 240]);

        // Table Header
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ]);

        // Header Row
        $table->addRow();
        $table->addCell(800)->addText('No', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(1500)->addText('Kode MK', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(3000)->addText('Nama MK', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(800)->addText('SKS', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(1000)->addText('Nilai', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(1000)->addText('Huruf', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(1000)->addText('Bobot', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(1200)->addText('Nilai x SKS', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(2500)->addText('Dosen', ['bold' => true], ['alignment' => Jc::CENTER]);

        // Data Row (Template row - akan di-clone)
        $table->addRow();
        $table->addCell(800)->addText('{NO}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(1500)->addText('{KODE_MK}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(3000)->addText('{NAMA_MK}');
        $table->addCell(800)->addText('{SKS}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(1000)->addText('{NILAI}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(1000)->addText('{HURUF}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(1000)->addText('{BOBOT}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(1200)->addText('{NILAI_X_SKS}', [], ['alignment' => Jc::CENTER]);
        $table->addCell(2500)->addText('{DOSEN}');
    }
}
