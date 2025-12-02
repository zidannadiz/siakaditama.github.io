<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'tipe',
        'pertanyaan',
        'pilihan',
        'jawaban_benar',
        'jawaban_benar_essay',
        'bobot',
        'urutan',
        'penjelasan',
    ];

    protected $casts = [
        'pilihan' => 'array',
        'bobot' => 'decimal:2',
        'urutan' => 'integer',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function isPilgan(): bool
    {
        return $this->tipe === 'pilgan';
    }

    public function isEssay(): bool
    {
        return $this->tipe === 'essay';
    }

    public function getPilihanArray(): array
    {
        return $this->pilihan ?? [];
    }
}
