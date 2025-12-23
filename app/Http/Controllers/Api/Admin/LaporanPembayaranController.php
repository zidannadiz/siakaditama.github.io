<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Bank;
use Illuminate\Http\Request;

class LaporanPembayaranController extends Controller
{
    /**
     * Get laporan pembayaran with filters
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

        $perPage = $request->per_page ?? 20;
        $payments = $query->paginate($perPage);
        
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
                    'nim' => $mhs->nim,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'payments' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ],
                'stats' => $stats,
                'banks' => $banks->map(function($bank) {
                    return [
                        'id' => $bank->id,
                        'name' => $bank->name,
                        'code' => $bank->code,
                    ];
                }),
                'mahasiswas' => $mahasiswas,
            ],
        ]);
    }
}
