<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KRS - {{ $mahasiswa->nim }}</title>
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
        .total-row {
            background-color: #dbeafe;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KARTU RENCANA STUDI (KRS)</h1>
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
                <th style="width: 30%;">Nama Mata Kuliah</th>
                <th style="width: 8%;">SKS</th>
                <th style="width: 20%;">Dosen</th>
                <th style="width: 10%;">Hari</th>
                <th style="width: 15%;">Jam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($krs_list as $index => $krs)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $krs->jadwalKuliah->mataKuliah->kode_mk }}</td>
                    <td>{{ $krs->jadwalKuliah->mataKuliah->nama_mk }}</td>
                    <td style="text-align: center;">{{ $krs->jadwalKuliah->mataKuliah->sks }}</td>
                    <td>{{ $krs->jadwalKuliah->dosen->nama }}</td>
                    <td style="text-align: center;">{{ $krs->jadwalKuliah->hari }}</td>
                    <td style="text-align: center;">{{ date('H:i', strtotime($krs->jadwalKuliah->jam_mulai)) }} - {{ date('H:i', strtotime($krs->jadwalKuliah->jam_selesai)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data KRS</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding-right: 20px;"><strong>Total SKS</strong></td>
                <td style="text-align: center;"><strong>{{ $total_sks }}</strong></td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div style="text-align: right; margin-bottom: 20px; padding-left: 60%; color: #000; font-size: 11px; font-weight: normal;">
            {{ now()->format('d F Y') }}
        </div>
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <div>Mahasiswa</div>
                    <div style="margin-top: 50px;">{{ $mahasiswa->nama }}</div>
                    <div style="margin-top: 5px;">NIM: {{ $mahasiswa->nim }}</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div>Koordinator Akademik</div>
                    <div style="margin-top: 50px;">_________________________</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

