<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    use HasFactory;

    protected $fillable = [
        'prodi_id',
        'nama',
        'tahun',
        'status',
        'keterangan',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function details()
    {
        return $this->hasMany(KurikulumDetail::class);
    }

    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'kurikulum_details')
                    ->withPivot('semester_ke', 'jenis')
                    ->withTimestamps();
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
