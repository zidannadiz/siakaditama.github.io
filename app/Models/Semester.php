<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_semester',
        'jenis',
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function jadwalKuliahs(): HasMany
    {
        return $this->hasMany(JadwalKuliah::class);
    }

    public function krs(): HasMany
    {
        return $this->hasMany(KRS::class);
    }

    /**
     * Accessor untuk kompatibilitas dengan kode yang menggunakan ->nama
     */
    public function getNamaAttribute()
    {
        return $this->nama_semester;
    }
}

