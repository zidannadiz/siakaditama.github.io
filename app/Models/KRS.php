<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KRS extends Model
{
    use HasFactory;

    protected $table = 'krs';

    protected $fillable = [
        'mahasiswa_id',
        'jadwal_kuliah_id',
        'semester_id',
        'status',
        'catatan',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function nilai(): HasOne
    {
        return $this->hasOne(Nilai::class, 'krs_id');
    }
}

