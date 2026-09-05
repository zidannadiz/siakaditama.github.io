<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tahun_mulai',
        'tahun_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public static function getAktif()
    {
        return static::where('status', 'aktif')->first();
    }
}
