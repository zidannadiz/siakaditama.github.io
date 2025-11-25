<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'total_amount' => Payment::where('status', 'paid')->sum('total_amount'),
            'pending_amount' => Payment::where('status', 'pending')->sum('total_amount'),
        ];

        return view('admin.payment.index', compact('payments', 'banks', 'stats'));
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

        return view('admin.payment.show', compact('payment'));
    }

    /**
     * Verify payment manually
     */
    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        if ($payment->status === 'paid') {
            return back()->with('error', 'Pembayaran sudah diverifikasi sebelumnya');
        }

        $payment->markAsPaid();
        
        if ($request->notes) {
            $payment->update(['notes' => $request->notes]);
        }

        // Kirim notifikasi ke user
        \App\Services\NotifikasiService::create(
            $payment->user_id,
            'Pembayaran Diverifikasi',
            "Pembayaran dengan invoice {$payment->invoice_number} telah diverifikasi dan diterima.",
            'success',
            route('payment.index')
        );

        return back()->with('success', 'Pembayaran berhasil diverifikasi');
    }

    /**
     * Cancel payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Hanya pembayaran pending yang bisa dibatalkan');
        }

        $payment->cancel();

        return back()->with('success', 'Pembayaran berhasil dibatalkan');
    }

    /**
     * Get payment statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'today' => [
                'count' => Payment::whereDate('created_at', today())->count(),
                'amount' => Payment::whereDate('created_at', today())->sum('amount'),
                'paid' => Payment::whereDate('created_at', today())->where('status', 'paid')->count(),
            ],
            'this_month' => [
                'count' => Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'amount' => Payment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->sum('amount'),
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

        return response()->json($stats);
    }
}

