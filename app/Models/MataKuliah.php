<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'prodi_id',
        'sks',
        'semester',
        'deskripsi',
        'jenis',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function jadwalKuliahs(): HasMany
    {
        return $this->hasMany(JadwalKuliah::class);
    }
}

