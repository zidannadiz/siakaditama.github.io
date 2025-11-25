<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateKrsKhs extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis',
        'nama_template',
        'file_path',
        'is_active',
        'deskripsi',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
