<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_kuliah_id',
        'dosen_id',
        'pertemuan',
        'tanggal',
        'kode_kelas',
        'status',
        'started_at',
        'closed_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Generate kode kelas unik
     */
    public static function generateKodeKelas(): string
    {
        do {
            $kode = strtoupper(Str::random(6));
        } while (self::where('kode_kelas', $kode)->exists());
        
        return $kode;
    }

    /**
     * Cek apakah kelas masih aktif (buka)
     */
    public function isActive(): bool
    {
        return $this->status === 'buka' && is_null($this->closed_at);
    }

    /**
     * Tutup kelas
     */
    public function close(): void
    {
        $this->update([
            'status' => 'tutup',
            'closed_at' => now(),
        ]);
    }

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'jadwal_kuliah_id', 'jadwal_kuliah_id')
            ->where('pertemuan', $this->pertemuan)
            ->where('tanggal', $this->tanggal);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ClassAttendance::class);
    }
}
