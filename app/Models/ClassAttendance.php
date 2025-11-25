<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_session_id',
        'mahasiswa_id',
        'status',
        'waktu_masuk',
        'waktu_keluar',
        'catatan',
        'is_kicked',
        'kicked_at',
        'alasan_kick',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'kicked_at' => 'datetime',
        'is_kicked' => 'boolean',
    ];

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    /**
     * Set status sebagai dikeluarkan
     */
    public function markAsKicked(string $alasan = null): void
    {
        $this->update([
            'status' => 'dikeluarkan',
            'is_kicked' => true,
            'kicked_at' => now(),
            'alasan_kick' => $alasan,
            'waktu_keluar' => now(),
        ]);
    }

    /**
     * Update status presensi (izin/sakit)
     */
    public function updateStatus(string $status, string $catatan = null): void
    {
        $this->update([
            'status' => $status,
            'catatan' => $catatan,
        ]);
    }
}
