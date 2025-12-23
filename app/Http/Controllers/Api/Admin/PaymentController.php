<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\NotifikasiService;

class PaymentController extends Controller
{
    /**
     * Display list of all payments
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

        // Search by invoice number, virtual account, or user name
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

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->paginate(20);
        $banks = Bank::orderBy('name')->get();
        
        // Statistics
        $stats = [
            'total' => Payment::count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'paid' => Payment::where('status', 'paid')->count(),
            'expired' => Payment::where('status', 'expired')->count(),
            'cancelled' => Payment::where('status', 'cancelled')->count(),
            'total_amount' => Payment::where('status', 'paid')->sum('total_amount'),
            'pending_amount' => Payment::where('status', 'pending')->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'payments' => $payments->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'invoice_number' => $payment->invoice_number,
                        'user' => [
                            'id' => $payment->user->id ?? null,
                            'name' => $payment->user->name ?? null,
                            'email' => $payment->user->email ?? null,
                        ],
                        'bank' => [
                            'id' => $payment->bank->id ?? null,
                            'name' => $payment->bank->name ?? null,
                        ],
                        'virtual_account' => $payment->virtual_account,
                        'payment_type' => $payment->payment_type,
                        'amount' => $payment->amount,
                        'fee' => $payment->fee,
                        'total_amount' => $payment->total_amount,
                        'status' => $payment->status,
                        'description' => $payment->description,
                        'expired_at' => $payment->expired_at?->toISOString(),
                        'paid_at' => $payment->paid_at?->toISOString(),
                        'created_at' => $payment->created_at->toISOString(),
                    ];
                }),
                'banks' => $banks->map(function($bank) {
                    return [
                        'id' => $bank->id,
                        'name' => $bank->name,
                    ];
                }),
                'statistics' => $stats,
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'total' => $payments->total(),
                ],
            ],
        ]);
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'bank']);
        
        // Auto expire jika sudah melewati expired_at
        if ($payment->isExpired()) {
            $payment->expire();
            $payment->refresh();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payment->id,
                'invoice_number' => $payment->invoice_number,
                'user' => [
                    'id' => $payment->user->id ?? null,
                    'name' => $payment->user->name ?? null,
                    'email' => $payment->user->email ?? null,
                ],
                'bank' => [
                    'id' => $payment->bank->id ?? null,
                    'name' => $payment->bank->name ?? null,
                    'account_number' => $payment->bank->account_number ?? null,
                    'account_name' => $payment->bank->account_name ?? null,
                ],
                'virtual_account' => $payment->virtual_account,
                'payment_type' => $payment->payment_type,
                'amount' => $payment->amount,
                'fee' => $payment->fee,
                'total_amount' => $payment->total_amount,
                'status' => $payment->status,
                'description' => $payment->description,
                'expired_at' => $payment->expired_at?->toISOString(),
                'paid_at' => $payment->paid_at?->toISOString(),
                'payment_proof' => $payment->payment_proof,
                'notes' => $payment->notes,
                'created_at' => $payment->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Verify payment manually
     */
    public function verify(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        if ($payment->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran sudah diverifikasi sebelumnya',
            ], 400);
        }

        $payment->markAsPaid();
        
        if ($validated['notes'] ?? null) {
            $payment->update(['notes' => $validated['notes']]);
        }

        // Kirim notifikasi ke user
        if ($payment->user_id) {
            NotifikasiService::create(
                $payment->user_id,
                'Pembayaran Diverifikasi',
                "Pembayaran dengan invoice {$payment->invoice_number} telah diverifikasi dan diterima.",
                'success',
                '/payment'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diverifikasi',
        ]);
    }

    /**
     * Cancel payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pembayaran pending yang bisa dibatalkan',
            ], 400);
        }

        $payment->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dibatalkan',
        ]);
    }

    /**
     * Get payment statistics
     */
    public function statistics()
    {
        $stats = [
            'today' => [
                'count' => Payment::whereDate('created_at', today())->count(),
                'amount' => Payment::whereDate('created_at', today())->sum('total_amount'),
                'paid' => Payment::whereDate('created_at', today())->where('status', 'paid')->count(),
            ],
            'this_month' => [
                'count' => Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'amount' => Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('total_amount'),
                'paid_amount' => Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('status', 'paid')->sum('total_amount'),
            ],
            'by_status' => Payment::select('status', DB::raw('count(*) as total'), DB::raw('sum(total_amount) as amount'))
                ->groupBy('status')
                ->get(),
            'by_bank' => Payment::join('banks', 'payments.bank_id', '=', 'banks.id')
                ->select('banks.name', DB::raw('count(*) as total'), DB::raw('sum(payments.total_amount) as amount'))
                ->where('payments.status', 'paid')
                ->groupBy('banks.id', 'banks.name')
                ->orderBy('total', 'desc')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
