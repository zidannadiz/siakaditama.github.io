<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'jadwal_kuliah_id',
        'mahasiswa_id',
        'pertemuan',
        'tanggal',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
