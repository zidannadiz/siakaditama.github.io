<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
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
            grid-template-columns: repeat(3, 1fr);
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
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMBAYARAN</h1>
        <p>Sistem Informasi Akademik</p>
        <p>Periode: {{ request('date_from') ? date('d/m/Y', strtotime(request('date_from'))) : 'Semua' }} - 
           {{ request('date_to') ? date('d/m/Y', strtotime(request('date_to'))) : 'Semua' }}</p>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Mahasiswa</th>
                <th>Jenis</th>
                <th>Bank</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->invoice_number }}</td>
                    <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $payment->user->name ?? '-' }}</td>
                    <td>{{ $payment->payment_type }}</td>
                    <td>{{ $payment->bank->name ?? '-' }}</td>
                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $statusLabels = [
                                'pending' => 'Menunggu',
                                'paid' => 'Sudah Dibayar',
                                'expired' => 'Expired',
                                'cancelled' => 'Dibatalkan'
                            ];
                        @endphp
                        {{ $statusLabels[$payment->status] ?? $payment->status }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="stats">
        <h3>Statistik</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <strong>Total:</strong> {{ number_format($stats['total']) }}
            </div>
            <div class="stat-item">
                <strong>Pending:</strong> {{ number_format($stats['pending']) }}
            </div>
            <div class="stat-item">
                <strong>Sudah Dibayar:</strong> {{ number_format($stats['paid']) }}
            </div>
            <div class="stat-item">
                <strong>Expired:</strong> {{ number_format($stats['expired']) }}
            </div>
            <div class="stat-item">
                <strong>Dibatalkan:</strong> {{ number_format($stats['cancelled']) }}
            </div>
            <div class="stat-item">
                <strong>Total Paid:</strong> Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

