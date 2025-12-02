<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_kuliah_id',
        'dosen_id',
        'judul',
        'deskripsi',
        'file_path',
        'deadline',
        'bobot',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->deadline);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
