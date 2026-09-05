<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KurikulumDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'kurikulum_id',
        'mata_kuliah_id',
        'semester_ke',
        'jenis',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }
}
