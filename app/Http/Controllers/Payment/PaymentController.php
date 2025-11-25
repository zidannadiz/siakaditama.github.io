<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Payment;
use App\Services\XenditService;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display payment page - pilih bank
     */
    public function create(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $banks = Bank::where('is_active', true)->orderBy('name')->get();

        return view('payment.create', compact('banks'))->with([
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'description' => $request->description,
        ]);
    }

    /**
     * Store payment - generate virtual account
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
            return back()->with('error', 'Bank tidak aktif');
        }

        // Hitung biaya admin (misal 0.5% atau maksimal 5000)
        $fee = min($request->amount * 0.005, 5000);
        $totalAmount = $request->amount + $fee;

        // Create payment record dulu (virtual_account akan di-generate setelahnya)
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'bank_id' => $bank->id,
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
            'fee' => $fee,
            'total_amount' => $totalAmount,
            'description' => $request->description,
            'expired_at' => now()->addHours(24),
            'status' => 'pending',
            // virtual_account akan di-generate oleh Xendit atau fallback manual
        ]);

        // Coba create Virtual Account via Xendit
        $xenditService = new XenditService();
        $result = $xenditService->createVirtualAccount($payment);

        if (!$result['success']) {
            // Fallback: Generate virtual account manual jika Xendit gagal atau belum dikonfigurasi
            $bankCode = str_pad($bank->code, 3, '0', STR_PAD_LEFT);
            $timestamp = substr(time(), -6);
            $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $virtualAccount = $bankCode . $timestamp . $random;

            // Pastikan VA unik
            while (Payment::where('virtual_account', $virtualAccount)->exists()) {
                $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $virtualAccount = $bankCode . $timestamp . $random;
            }

            $payment->update([
                'virtual_account' => $virtualAccount,
            ]);
        }

        // Kirim notifikasi untuk pembayaran baru
        NotifikasiService::create(
            Auth::id(),
            'Pembayaran Baru',
            "Pembayaran dengan invoice {$payment->invoice_number} telah dibuat. Jangan lupa untuk melakukan pembayaran sebelum {$payment->expired_at->format('d M Y H:i')}.",
            'info',
            route('payment.show', $payment)
        );

        return redirect()->route('payment.show', $payment)
            ->with('success', 'Virtual account berhasil dibuat');
    }

    /**
     * Show payment details and virtual account
     */
    public function show(Payment $payment)
    {
        // Check if user owns this payment or is admin
        if ($payment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $payment->load(['bank', 'user']);

        // Auto expire jika sudah melewati expired_at
        if ($payment->isExpired()) {
            $payment->expire();
        }

        return view('payment.show', compact('payment'));
    }

    /**
     * List all payments for current user
     */
    public function index(Request $request)
    {
        $query = Payment::where('user_id', Auth::id())
            ->with('bank')
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }

        $payments = $query->paginate(15);

        return view('payment.index', compact('payments'));
    }

    /**
     * Cancel payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran tidak dapat dibatalkan');
        }

        $payment->cancel();

        return back()->with('success', 'Pembayaran berhasil dibatalkan');
    }

    /**
     * Verify payment manually (for admin)
     */
    public function verify(Request $request, Payment $payment)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $payment->markAsPaid();
        
        if ($request->notes) {
            $payment->update(['notes' => $request->notes]);
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi');
    }
}

