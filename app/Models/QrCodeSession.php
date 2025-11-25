<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QrCodeSession extends Model
{
    protected $fillable = [
        'jadwal_kuliah_id',
        'pertemuan',
        'tanggal',
        'token',
        'expires_at',
        'is_active',
        'duration_minutes',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    /**
     * Generate token unik untuk QR code
     */
    public static function generateToken(): string
    {
        return Str::random(60);
    }

    /**
     * Cek apakah QR code masih valid
     */
    public function isValid(): bool
    {
        return $this->is_active && 
               $this->expires_at->isFuture() && 
               Carbon::now()->lessThanOrEqualTo($this->expires_at);
    }

    /**
     * Buat QR code session baru
     */
    public static function createSession(
        int $jadwal_kuliah_id,
        int $pertemuan,
        string $tanggal,
        int $duration_minutes = 30
    ): self {
        // Nonaktifkan session QR code sebelumnya untuk jadwal dan pertemuan yang sama
        self::where('jadwal_kuliah_id', $jadwal_kuliah_id)
            ->where('pertemuan', $pertemuan)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return self::create([
            'jadwal_kuliah_id' => $jadwal_kuliah_id,
            'pertemuan' => $pertemuan,
            'tanggal' => $tanggal,
            'token' => self::generateToken(),
            'expires_at' => Carbon::now()->addMinutes($duration_minutes),
            'is_active' => true,
            'duration_minutes' => $duration_minutes,
        ]);
    }
}
