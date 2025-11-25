<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPembayaranExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPembayaranController extends Controller
{
    /**
     * Display laporan pembayaran page
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'bank'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment type
        if ($request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by bank
        if ($request->bank_id) {
            $query->where('bank_id', $request->bank_id);
        }

        // Filter by mahasiswa
        if ($request->mahasiswa_id) {
            $query->where('user_id', $request->mahasiswa_id);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhere('virtual_account', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $payments = $query->paginate(20);
        
        // Statistics
        $stats = [
            'total' => Payment::count(),
            'total_amount' => Payment::where('status', 'paid')->sum('total_amount'),
            'pending' => Payment::where('status', 'pending')->count(),
            'paid' => Payment::where('status', 'paid')->count(),
            'expired' => Payment::where('status', 'expired')->count(),
            'cancelled' => Payment::where('status', 'cancelled')->count(),
        ];

        // Filter stats
        if ($request->date_from || $request->date_to || $request->status || $request->payment_type) {
            $filteredQuery = Payment::query();
            
            if ($request->date_from) {
                $filteredQuery->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $filteredQuery->whereDate('created_at', '<=', $request->date_to);
            }
            if ($request->status) {
                $filteredQuery->where('status', $request->status);
            }
            if ($request->payment_type) {
                $filteredQuery->where('payment_type', $request->payment_type);
            }

            $stats['total'] = $filteredQuery->count();
            $stats['total_amount'] = $filteredQuery->where('status', 'paid')->sum('total_amount');
            $stats['pending'] = $filteredQuery->where('status', 'pending')->count();
            $stats['paid'] = $filteredQuery->where('status', 'paid')->count();
            $stats['expired'] = $filteredQuery->where('status', 'expired')->count();
            $stats['cancelled'] = $filteredQuery->where('status', 'cancelled')->count();
        }

        $banks = Bank::where('is_active', true)->orderBy('name')->get();
        $mahasiswas = \App\Models\Mahasiswa::with('user')
            ->orderBy('nama')
            ->get()
            ->map(function($mhs) {
                return [
                    'id' => $mhs->user_id,
                    'name' => $mhs->nama,
                    'mahasiswa' => $mhs,
                ];
            });

        return view('admin.laporan.pembayaran.index', compact('payments', 'stats', 'banks', 'mahasiswas'));
    }

    /**
     * Export laporan pembayaran to Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new LaporanPembayaranExport($request->all()),
            'laporan-pembayaran-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export laporan pembayaran to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Payment::with(['user', 'bank'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }
        if ($request->bank_id) {
            $query->where('bank_id', $request->bank_id);
        }
        if ($request->mahasiswa_id) {
            $query->where('user_id', $request->mahasiswa_id);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->get();

        // Calculate statistics
        $stats = [
            'total' => $payments->count(),
            'total_amount' => $payments->where('status', 'paid')->sum('total_amount'),
            'pending' => $payments->where('status', 'pending')->count(),
            'paid' => $payments->where('status', 'paid')->count(),
            'expired' => $payments->where('status', 'expired')->count(),
            'cancelled' => $payments->where('status', 'cancelled')->count(),
        ];

        $pdf = Pdf::loadView('admin.laporan.pembayaran.pdf', [
            'payments' => $payments,
            'stats' => $stats,
            'filters' => $request->all(),
        ]);

        return $pdf->download('laporan-pembayaran-' . date('Y-m-d') . '.pdf');
    }
}

