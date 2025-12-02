<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'exam_question_id',
        'jawaban_pilgan',
        'jawaban_essay',
        'nilai',
        'feedback',
        'is_answered',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'is_answered' => 'boolean',
    ];

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function examQuestion(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class);
    }

    public function isCorrect(): bool
    {
        if (!$this->examQuestion) {
            return false;
        }

        if ($this->examQuestion->isPilgan()) {
            return $this->jawaban_pilgan === $this->examQuestion->jawaban_benar;
        }

        return false; // Essay tidak bisa auto-check
    }
}
