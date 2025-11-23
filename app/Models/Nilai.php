<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'krs_id',
        'mahasiswa_id',
        'jadwal_kuliah_id',
        'dosen_id',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'huruf_mutu',
        'bobot',
        'status',
        'catatan',
    ];

    protected $casts = [
        'nilai_tugas' => 'decimal:2',
        'nilai_uts' => 'decimal:2',
        'nilai_uas' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'bobot' => 'decimal:2',
    ];

    public function krs(): BelongsTo
    {
        return $this->belongsTo(KRS::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }
}

