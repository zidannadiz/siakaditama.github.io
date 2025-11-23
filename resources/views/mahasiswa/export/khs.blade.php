<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KHS - {{ $mahasiswa->nim }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 30px 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 18px;
            color: #3b82f6;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            display: table-cell;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background-color: #2563eb;
            color: white;
        }
        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1e40af;
        }
        td {
            padding: 10px 8px;
            border: 1px solid #ddd;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        .summary-row {
            background-color: #dbeafe;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KARTU HASIL STUDI (KHS)</h1>
        <h2>Sistem Informasi Akademik</h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">NIM</div>
            <div class="info-value">: {{ $mahasiswa->nim }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Mahasiswa</div>
            <div class="info-value">: {{ $mahasiswa->nama }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Program Studi</div>
            <div class="info-value">: {{ $mahasiswa->prodi->nama_prodi }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Semester</div>
            <div class="info-value">: {{ $semester->nama_semester }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tahun Akademik</div>
            <div class="info-value">: {{ $semester->tahun_ajaran }}/{{ $semester->tahun_ajaran + 1 }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Cetak</div>
            <div class="info-value">: {{ now()->format('d F Y H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Kode MK</th>
                <th style="width: 25%;">Nama Mata Kuliah</th>
                <th style="width: 6%;">SKS</th>
                <th style="width: 8%;">Nilai</th>
                <th style="width: 8%;">Huruf</th>
                <th style="width: 8%;">Bobot</th>
                <th style="width: 10%;">Nilai x SKS</th>
                <th style="width: 18%;">Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilais as $index => $nilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $nilai->jadwalKuliah->mataKuliah->kode_mk }}</td>
                    <td>{{ $nilai->jadwalKuliah->mataKuliah->nama_mk }}</td>
                    <td class="text-center">{{ $nilai->jadwalKuliah->mataKuliah->sks }}</td>
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
                    <td>{{ $nilai->dosen->nama }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data nilai</td>
                </tr>
            @endforelse
            <tr class="summary-row">
                <td colspan="3" class="text-right" style="padding-right: 20px;"><strong>Total</strong></td>
                <td class="text-center"><strong>{{ $total_sks }}</strong></td>
                <td colspan="2"></td>
                <td colspan="2" class="text-center">
                    <strong>
                        @php
                            $total_nilai_x_sks = $nilais->sum(function($nilai) {
                                $sks = $nilai->jadwalKuliah->mataKuliah->sks ?? 0;
                                $bobot = $nilai->bobot ?? 0;
                                return $sks * $bobot;
                            });
                        @endphp
                        {{ number_format($total_nilai_x_sks, 2) }}
                    </strong>
                </td>
                <td></td>
            </tr>
            <tr class="summary-row">
                <td colspan="6" class="text-right" style="padding-right: 20px;"><strong>IP Semester (IPS)</strong></td>
                <td colspan="3" class="text-center">
                    <strong>{{ number_format($ipk, 2) }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div style="margin-top: 20px;">
            <p><strong>Keterangan:</strong></p>
            <p>Nilai: A (4.00), A- (3.75), B+ (3.50), B (3.00), B- (2.75), C+ (2.50), C (2.00), C- (1.75), D (1.00), E (0.00)</p>
            <p>IPS = Total (Nilai x SKS) / Total SKS</p>
        </div>
    </div>
</body>
</html>

