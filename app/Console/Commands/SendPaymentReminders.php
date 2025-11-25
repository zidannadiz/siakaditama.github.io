<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Notifikasi;
use App\Services\NotifikasiService;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment deadline reminders to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil pembayaran yang akan expired dalam 6 jam dan belum dibayar
        $payments = Payment::where('status', 'pending')
            ->whereBetween('expired_at', [
                now(),
                now()->addHours(6)
            ])
            ->with('user')
            ->get()
            ->filter(function($payment) {
                // Cek apakah sudah ada notifikasi reminder dalam 6 jam terakhir
                $recentReminder = Notifikasi::where('user_id', $payment->user_id)
                    ->where('judul', 'like', '%Reminder Pembayaran%')
                    ->where('pesan', 'like', "%{$payment->invoice_number}%")
                    ->where('created_at', '>', now()->subHours(6))
                    ->exists();
                
                return !$recentReminder;
            });

        $count = 0;
        foreach ($payments as $payment) {
            $hoursLeft = now()->diffInHours($payment->expired_at);
            
            NotifikasiService::create(
                $payment->user_id,
                'Reminder Pembayaran',
                "Pembayaran dengan invoice {$payment->invoice_number} akan expired dalam {$hoursLeft} jam. Segera lakukan pembayaran sebelum {$payment->expired_at->format('d M Y H:i')}.",
                'warning',
                route('payment.show', $payment)
            );
            
            $count++;
        }

        $this->info("Sent {$count} payment reminders.");
        return 0;
    }
}
