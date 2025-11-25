<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transkrip Nilai - {{ $mahasiswa->nim }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 20px 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            margin-left: 15px;
            margin-right: 15px;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 20pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12pt;
            color: #4b5563;
        }
        .student-info {
            margin-bottom: 25px;
            margin-left: 15px;
            margin-right: 15px;
            background: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 5px 10px;
            vertical-align: top;
        }
        .student-info .label {
            font-weight: bold;
            width: 120px;
            color: #374151;
        }
        .student-info .value {
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-left: 15px;
            margin-right: 15px;
        }
        thead {
            background-color: #1e40af;
            color: white;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #d1d5db;
        }
        th {
            font-weight: bold;
            font-size: 10pt;
        }
        td {
            font-size: 10pt;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .semester-section {
            margin-bottom: 30px;
            margin-left: 15px;
            margin-right: 15px;
            page-break-inside: avoid;
        }
        .semester-header {
            background-color: #3b82f6;
            color: white;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        .summary-row {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            margin-left: 15px;
            margin-right: 15px;
            padding-top: 20px;
            border-top: 2px solid #d1d5db;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }
        .signature-section {
            margin-top: 40px;
            margin-left: 15px;
            margin-right: 15px;
            display: table;
            width: calc(100% - 30px);
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 20px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TRANSKRIP NILAI</h1>
        <p>Sistem Informasi Akademik</p>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td class="label">NIM</td>
                <td class="value">: {{ $mahasiswa->nim }}</td>
                <td class="label">Program Studi</td>
                <td class="value">: {{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nama</td>
                <td class="value">: {{ $mahasiswa->nama }}</td>
                <td class="label">Tanggal Cetak</td>
                <td class="value">: {{ $generated_at->format('d F Y, H:i') }}</td>
            </tr>
        </table>
    </div>

    @foreach($semester_data as $data)
    <div class="semester-section">
        <div class="semester-header">
            {{ $data['semester']->nama_semester ?? $data['semester']->jenis ?? 'Semester' }} {{ $data['semester']->tahun_akademik ?? $data['semester']->tahun_ajaran ?? '' }}
            <span style="float: right;">IP: {{ number_format($data['ip'], 2) }} | Total SKS: {{ $data['total_sks'] }}</span>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Kode MK</th>
                    <th style="width: 30%;">Nama Mata Kuliah</th>
                    <th style="width: 5%;" class="text-center">SKS</th>
                    <th style="width: 8%;" class="text-center">Nilai</th>
                    <th style="width: 8%;" class="text-center">Huruf</th>
                    <th style="width: 8%;" class="text-center">Bobot</th>
                    <th style="width: 10%;" class="text-center">Nilai x SKS</th>
                    <th style="width: 17%;">Dosen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['nilais'] as $index => $nilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $nilai->jadwalKuliah->mataKuliah->kode_mk ?? '-' }}</td>
                    <td>{{ $nilai->jadwalKuliah->mataKuliah->nama_mk ?? '-' }}</td>
                    <td class="text-center">{{ $nilai->jadwalKuliah->mataKuliah->sks ?? 0 }}</td>
                    <td class="text-center">{{ $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 2) : '-' }}</td>
                    <td class="text-center">{{ $nilai->huruf_mutu ?? '-' }}</td>
                    <td class="text-center">{{ $nilai->bobot ? number_format($nilai->bobot, 2) : '-' }}</td>
                    <td class="text-center">
                        @php
                            $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                            $bobot = $nilai->bobot ?? 0;
                            $nilai_x_sks = $sks * $bobot;
                        @endphp
                        {{ $nilai_x_sks > 0 ? number_format($nilai_x_sks, 2) : '-' }}
                    </td>
                    <td>{{ $nilai->dosen->nama ?? '-' }}</td>
                </tr>
                @endforeach
                <tr class="summary-row">
                    <td colspan="3" class="text-right"><strong>Total Semester Ini:</strong></td>
                    <td class="text-center"><strong>{{ $data['total_sks'] }}</strong></td>
                    <td colspan="2"></td>
                    <td class="text-center"><strong>IP: {{ number_format($data['ip'], 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="semester-section" style="margin-top: 30px;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; margin-left: 15px; margin-right: 15px;">
            <colgroup>
                <col style="width: 4%;">
                <col style="width: 10%;">
                <col style="width: 30%;">
                <col style="width: 5%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 10%;">
                <col style="width: 17%;">
            </colgroup>
            <thead style="background-color: white; color: white;">
                <tr>
                    <th style="padding: 8px; text-align: left; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">No</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">Kode MK</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">Nama Mata Kuliah</th>
                    <th style="padding: 8px; text-align: center; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">SKS</th>
                    <th style="padding: 8px; text-align: center; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">Nilai</th>
                    <th style="padding: 8px; text-align: center; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">Huruf</th>
                    <th style="padding: 8px; text-align: center; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">Bobot</th>
                    <th style="padding: 8px; text-align: center; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">Nilai x SKS</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #d1d5db; font-weight: bold; font-size: 10pt;">Dosen</th>
                </tr>
            </thead>
            <tbody>
                <tr class="summary-row">
                    <td colspan="3" class="text-right" style="padding: 8px; text-align: right; border: 1px solid #d1d5db; font-size: 10pt;"><strong>Total SKS Kumulatif:</strong></td>
                    <td class="text-center" style="padding: 8px; text-align: center; border: 1px solid #d1d5db; font-size: 10pt;"><strong>{{ $total_sks }}</strong></td>
                    <td colspan="2" style="padding: 8px; border: 1px solid #d1d5db; font-size: 10pt;"></td>
                    <td style="padding: 8px; border: 1px solid #d1d5db; font-size: 10pt;"></td>
                    <td colspan="2" style="padding: 8px; border: 1px solid #d1d5db; font-size: 10pt;"></td>
                </tr>
                <tr class="summary-row">
                    <td colspan="3" class="text-right" style="padding: 8px; text-align: right; border: 1px solid #d1d5db; font-size: 10pt;"><strong>IPK (Indeks Prestasi Kumulatif):</strong></td>
                    <td class="text-center" style="padding: 8px; text-align: center; border: 1px solid #d1d5db; font-size: 10pt;"><strong style="font-size: 14pt; color: #1e40af;">{{ number_format($ipk, 2) }}</strong></td>
                    <td colspan="2" style="padding: 8px; border: 1px solid #d1d5db; font-size: 10pt;"></td>
                    <td style="padding: 8px; border: 1px solid #d1d5db; font-size: 10pt;"></td>
                    <td colspan="2" style="padding: 8px; border: 1px solid #d1d5db; font-size: 10pt;"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <p>Mahasiswa</p>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <p>Ketua Program Studi</p>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <p>Wakil Rektor Bidang Akademik</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari Sistem Informasi Akademik</p>
        <p>Tanggal: {{ $generated_at->format('d F Y, H:i:s') }}</p>
    </div>
</body>
</html>
