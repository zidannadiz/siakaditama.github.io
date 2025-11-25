<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KalenderAkademik extends Model
{
    use HasFactory;

    protected $table = 'kalender_akademik';

    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'jenis',
        'target_role',
        'semester_id',
        'warna',
        'is_important',
        'link',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
        'is_important' => 'boolean',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeForRole($query, $role)
    {
        return $query->where(function($q) use ($role) {
            $q->where('target_role', 'semua')
              ->orWhere('target_role', $role);
        });
    }

    /**
     * Scope untuk event penting
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Get warna berdasarkan jenis event
     */
    public function getColorAttribute()
    {
        $colors = [
            'semester' => '#3B82F6',      // Blue
            'krs' => '#10B981',           // Green
            'pembayaran' => '#F59E0B',    // Amber
            'ujian' => '#EF4444',         // Red
            'libur' => '#8B5CF6',         // Purple
            'kegiatan' => '#EC4899',      // Pink
            'pengumuman' => '#06B6D4',    // Cyan
            'lainnya' => '#6B7280',       // Gray
        ];

        return $this->warna ?? $colors[$this->jenis] ?? '#6B7280';
    }
}
