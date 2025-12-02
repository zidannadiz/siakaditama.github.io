<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_kuliah_id',
        'dosen_id',
        'judul',
        'deskripsi',
        'tipe',
        'durasi',
        'mulai',
        'selesai',
        'total_soal',
        'bobot',
        'random_soal',
        'random_pilihan',
        'tampilkan_nilai',
        'prevent_copy_paste',
        'prevent_new_tab',
        'fullscreen_mode',
        'status',
    ];

    protected $casts = [
        'durasi' => 'integer',
        'mulai' => 'datetime',
        'selesai' => 'datetime',
        'total_soal' => 'integer',
        'bobot' => 'decimal:2',
        'random_soal' => 'boolean',
        'random_pilihan' => 'boolean',
        'tampilkan_nilai' => 'boolean',
        'prevent_copy_paste' => 'boolean',
        'prevent_new_tab' => 'boolean',
        'fullscreen_mode' => 'boolean',
    ];

    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('urutan');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isOngoing(): bool
    {
        $now = \Carbon\Carbon::now(config('app.timezone'));
        if ($this->mulai) {
            $mulai = \Carbon\Carbon::parse($this->mulai)->setTimezone(config('app.timezone'));
            if ($now->isBefore($mulai)) {
                return false;
            }
        }
        $selesai = \Carbon\Carbon::parse($this->selesai)->setTimezone(config('app.timezone'));
        return $now->isBefore($selesai);
    }

    public function isFinished(): bool
    {
        $now = \Carbon\Carbon::now(config('app.timezone'));
        $selesai = \Carbon\Carbon::parse($this->selesai)->setTimezone(config('app.timezone'));
        return $now->isAfter($selesai);
    }
}
