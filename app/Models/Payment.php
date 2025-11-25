<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Bank;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'bank_id',
        'virtual_account',
        'xendit_id',
        'external_id',
        'xendit_response',
        'payment_type',
        'amount',
        'fee',
        'total_amount',
        'status',
        'description',
        'expired_at',
        'paid_at',
        'payment_proof',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->invoice_number)) {
                $payment->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(8));
            }
            
            if (empty($payment->virtual_account) && $payment->bank_id) {
                // Generate Virtual Account dari bank code + timestamp + random
                $bank = Bank::find($payment->bank_id);
                if ($bank) {
                    $bankCode = str_pad($bank->code, 3, '0', STR_PAD_LEFT);
                    $timestamp = substr(time(), -6);
                    $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                    $payment->virtual_account = $bankCode . $timestamp . $random;
                }
            }

            if (empty($payment->expired_at)) {
                // Default expired 24 jam dari sekarang
                $payment->expired_at = now()->addHours(24);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function isExpired()
    {
        return $this->expired_at < now() && $this->status === 'pending';
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function cancel()
    {
        if ($this->status === 'pending') {
            $this->update(['status' => 'cancelled']);
        }
    }

    public function expire()
    {
        if ($this->status === 'pending') {
            $this->update(['status' => 'expired']);
        }
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => ['label' => 'Menunggu Pembayaran', 'color' => 'yellow'],
            'paid' => ['label' => 'Sudah Dibayar', 'color' => 'green'],
            'expired' => ['label' => 'Kedaluwarsa', 'color' => 'red'],
            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'gray'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }
}

