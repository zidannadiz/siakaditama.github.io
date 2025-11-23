<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
        'deskripsi',
    ];

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function mataKuliahs(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }
}

