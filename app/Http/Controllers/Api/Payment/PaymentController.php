<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Get list of available banks
     */
    public function getBanks()
    {
        $banks = Bank::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function($bank) {
                return [
                    'id' => $bank->id,
                    'name' => $bank->name,
                    'code' => $bank->code,
                    'logo' => $bank->logo_url,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $banks
        ]);
    }

    /**
     * Get list of payments
     */
    public function index(Request $request)
    {
        $query = Payment::where('user_id', auth()->id())
            ->with('bank:id,name,code,logo')
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }

        $payments = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Create new payment - generate virtual account
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'amount' => 'required|numeric|min:1000',
            'payment_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $bank = Bank::findOrFail($request->bank_id);

        if (!$bank->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Bank tidak aktif'
            ], 422);
        }

        // Hitung biaya admin (misal 0.5% atau maksimal 5000)
        $fee = min($request->amount * 0.005, 5000);
        $totalAmount = $request->amount + $fee;

        // Generate virtual account
        $bankCode = str_pad($bank->code, 3, '0', STR_PAD_LEFT);
        $timestamp = substr(time(), -6);
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $virtualAccount = $bankCode . $timestamp . $random;

        // Pastikan VA unik
        while (Payment::where('virtual_account', $virtualAccount)->exists()) {
            $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $virtualAccount = $bankCode . $timestamp . $random;
        }

        $payment = Payment::create([
            'user_id' => auth()->id(),
            'bank_id' => $bank->id,
            'virtual_account' => $virtualAccount,
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
            'fee' => $fee,
            'total_amount' => $totalAmount,
            'description' => $request->description,
            'expired_at' => now()->addHours(24),
            'status' => 'pending',
        ]);

        $payment->load('bank:id,name,code,logo');

        return response()->json([
            'success' => true,
            'message' => 'Virtual account berhasil dibuat',
            'data' => $payment
        ], 201);
    }

    /**
     * Get payment details
     */
    public function show(Payment $payment)
    {
        if ($payment->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Auto expire jika sudah melewati expired_at
        if ($payment->isExpired()) {
            $payment->expire();
        }

        $payment->load(['bank:id,name,code,logo', 'user:id,name,email']);

        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    /**
     * Cancel payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak dapat dibatalkan'
            ], 422);
        }

        $payment->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dibatalkan'
        ]);
    }

    /**
     * Check payment status (untuk polling dari mobile)
     */
    public function checkStatus(Payment $payment)
    {
        if ($payment->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Auto expire jika sudah melewati expired_at
        if ($payment->isExpired()) {
            $payment->expire();
        }

        $payment->refresh();

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $payment->status,
                'paid_at' => $payment->paid_at,
                'is_expired' => $payment->isExpired(),
            ]
        ]);
    }
}

