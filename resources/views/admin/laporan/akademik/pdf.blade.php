<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Akademik</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4A5568 !important;
            color: white !important;
            font-weight: bold;
        }
        .stats {
            margin-top: 20px;
            padding: 15px;
            background-color: #E2E8F0 !important;
            border: 2px solid #CBD5E0;
            border-radius: 5px;
        }
        .stats h3 {
            margin-top: 0;
            font-size: 14px;
            font-weight: bold;
            color: #1A202C;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .stat-item {
            padding: 10px;
            background-color: #FFFFFF !important;
            border: 1px solid #CBD5E0;
            border-radius: 5px;
            font-weight: 500;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN AKADEMIK</h1>
        <p>Sistem Informasi Akademik</p>
        @if(isset($semester) && $semester)
            <p>Semester: {{ $semester->nama_semester ?? $semester->jenis }} - {{ $semester->tahun_akademik ?? $semester->tahun_ajaran }}</p>
        @endif
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th class="text-center">IPK Semester</th>
                <th class="text-center">IPK Kumulatif</th>
                <th class="text-center">SKS Semester</th>
                <th class="text-center">SKS Kumulatif</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswas as $index => $mahasiswa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $mahasiswa->nim }}</td>
                    <td>{{ $mahasiswa->nama }}</td>
                    <td>{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
                    <td class="text-center">{{ number_format($mahasiswa->ipk, 2) }}</td>
                    <td class="text-center">{{ number_format($mahasiswa->ipk_cumulative, 2) }}</td>
                    <td class="text-center">{{ $mahasiswa->total_sks }}</td>
                    <td class="text-center">{{ $mahasiswa->cumulative_sks }}</td>
                    <td class="text-center">
                        @if($mahasiswa->ipk_cumulative >= 2.00 && $mahasiswa->cumulative_sks >= 144)
                            Lulus
                        @else
                            Belum Lulus
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

