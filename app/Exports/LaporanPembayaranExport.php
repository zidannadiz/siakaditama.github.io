<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanPembayaranExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Payment::with(['user', 'bank'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }
        if (isset($this->filters['payment_type']) && $this->filters['payment_type']) {
            $query->where('payment_type', $this->filters['payment_type']);
        }
        if (isset($this->filters['bank_id']) && $this->filters['bank_id']) {
            $query->where('bank_id', $this->filters['bank_id']);
        }
        if (isset($this->filters['mahasiswa_id']) && $this->filters['mahasiswa_id']) {
            $query->where('user_id', $this->filters['mahasiswa_id']);
        }
        if (isset($this->filters['date_from']) && $this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (isset($this->filters['date_to']) && $this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Invoice Number',
            'Tanggal',
            'Nama Mahasiswa',
            'Email',
            'Jenis Pembayaran',
            'Bank',
            'Virtual Account',
            'Jumlah',
            'Biaya Admin',
            'Total',
            'Status',
            'Tanggal Bayar',
        ];
    }

    public function map($payment): array
    {
        static $no = 1;
        return [
            $no++,
            $payment->invoice_number,
            $payment->created_at->format('d/m/Y H:i'),
            $payment->user->name ?? '-',
            $payment->user->email ?? '-',
            $payment->payment_type,
            $payment->bank->name ?? '-',
            $payment->virtual_account,
            number_format($payment->amount, 0, ',', '.'),
            number_format($payment->fee, 0, ',', '.'),
            number_format($payment->total_amount, 0, ',', '.'),
            $this->getStatusLabel($payment->status),
            $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-',
        ];
    }

    protected function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 18,
            'D' => 25,
            'E' => 25,
            'F' => 18,
            'G' => 15,
            'H' => 20,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 18,
            'M' => 18,
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

