<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    /**
     * Handle Xendit Virtual Account callback
     */
    public function handleCallback(Request $request)
    {
        try {
            // Verify webhook token (opsional tapi disarankan)
            $webhookToken = config('xendit.webhook_token');
            $receivedToken = $request->header('x-callback-token');

            // Verify token jika ada
            if ($webhookToken && $receivedToken && $receivedToken !== $webhookToken) {
                Log::warning('Xendit Webhook: Invalid token', [
                    'received' => $receivedToken,
                    'expected' => $webhookToken,
                ]);
                return response()->json(['error' => 'Invalid token'], 403);
            }

            $event = $request->all();

            Log::info('Xendit Webhook Received', [
                'event' => $event,
                'headers' => $request->headers->all(),
            ]);

            // Cek event type - untuk Virtual Account callback
            if (isset($event['status']) && $event['status'] === 'PAID') {
                // Cari payment berdasarkan external_id (invoice_number)
                $payment = Payment::where('external_id', $event['external_id'])
                    ->orWhere('invoice_number', $event['external_id'])
                    ->first();

                if ($payment && $payment->status === 'pending') {
                    // Update payment status
                    $payment->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'xendit_response' => json_encode($event),
                    ]);

                    // TODO: Kirim notifikasi ke user
                    // Notification::send($payment->user, new PaymentSuccessNotification($payment));

                    Log::info('Payment marked as paid via Xendit webhook', [
                        'payment_id' => $payment->id,
                        'external_id' => $event['external_id'],
                    ]);

                    return response()->json([
                        'status' => 'ok',
                        'message' => 'Payment updated successfully',
                    ], 200);
                } else {
                    Log::warning('Xendit Webhook: Payment not found or already processed', [
                        'external_id' => $event['external_id'] ?? null,
                    ]);
                }
            }

            return response()->json(['status' => 'ok', 'message' => 'Event received'], 200);

        } catch (\Exception $e) {
            Log::error('Xendit Webhook Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

