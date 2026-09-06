<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak KHS - {{ $mahasiswa->nim }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2, .header h3 { margin: 0; padding: 2px; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 4px; vertical-align: top; }
        .info-table td:first-child { width: 120px; font-weight: bold; }
        .info-table td:nth-child(2) { width: 10px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 8px; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .footer { width: 100%; margin-top: 50px; }
        .footer-left { float: left; width: 50%; }
        .footer-right { float: right; width: 50%; text-align: center; }
        @media print {
            body { padding: 0; }
            button.print-btn { display: none; }
        }
        .print-btn { padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">🖨️ Cetak Dokumen</button>
    
    <div class="header">
        <h2>KARTU HASIL STUDI (KHS)</h2>
        <h3>{{ config('app.name', 'SIAKAD ITAMA') }}</h3>
    </div>

    <table class="info-table">
        <tr>
            <td>Nama Mahasiswa</td><td>:</td><td>{{ $mahasiswa->nama }}</td>
            <td>Tahun Akademik</td><td>:</td><td>{{ $semester->tahun_ajaran ?? ($semester->tahun_akademik ?? '-') }}</td>
        </tr>
        <tr>
            <td>NIM</td><td>:</td><td>{{ $mahasiswa->nim }}</td>
            <td>Semester</td><td>:</td><td>{{ $semester->nama_semester ?? ($semester->jenis ?? '-') }}</td>
        </tr>
        <tr>
            <td>Program Studi</td><td>:</td><td>{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
            <td></td><td></td><td></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode MK</th>
                <th width="35%">Mata Kuliah</th>
                <th width="10%">SKS</th>
                <th width="10%">Nilai Angka</th>
                <th width="10%">Nilai Huruf</th>
                <th width="15%">Nilai x SKS</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSks = 0; $totalNilaiSks = 0; @endphp
            @forelse($dataList as $index => $nilai)
                @php
                    $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                    $bobot = $nilai->bobot ?? 0;
                    $totalSks += $sks;
                    $n_x_sks = $sks * $bobot;
                    $totalNilaiSks += $n_x_sks;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $nilai->jadwalKuliah->mataKuliah->kode_mk ?? '-' }}</td>
                    <td>{{ $nilai->jadwalKuliah->mataKuliah->nama_mk ?? '-' }}</td>
                    <td class="text-center">{{ $sks }}</td>
                    <td class="text-center">{{ $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 2) : '-' }}</td>
                    <td class="text-center">{{ $nilai->huruf_mutu ?? '-' }}</td>
                    <td class="text-center">{{ $n_x_sks > 0 ? number_format($n_x_sks, 2) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data nilai KHS pada semester ini.</td>
                </tr>
            @endforelse
            <tr>
                <th colspan="3" style="text-align: right;">Total SKS / IPS :</th>
                <th class="text-center">{{ $totalSks }}</th>
                <th colspan="2"></th>
                <th class="text-center">{{ $totalSks > 0 ? number_format($totalNilaiSks / $totalSks, 2) : '0.00' }}</th>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-left">
        </div>
        <div class="footer-right">
            <p>__________, {{ date('d F Y') }}</p>
            <p>Ketua Program Studi</p>
            <br><br><br>
            <p><strong>________________________</strong></p>
        </div>
    </div>
    
    <script>
        // Otomatis muncul dialog print saat halaman dibuka
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
