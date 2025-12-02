<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'mahasiswa_id',
        'started_at',
        'finished_at',
        'waktu_tersisa',
        'tab_switch_count',
        'copy_paste_attempt_count',
        'violations',
        'status',
        'nilai',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'waktu_tersisa' => 'integer',
        'tab_switch_count' => 'integer',
        'copy_paste_attempt_count' => 'integer',
        'violations' => 'array',
        'nilai' => 'decimal:2',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'started' && is_null($this->finished_at);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['submitted', 'auto_submitted', 'terminated']);
    }

    public function addViolation(string $type, array $details = []): void
    {
        $violations = $this->violations ?? [];
        $violations[] = [
            'type' => $type,
            'timestamp' => now()->toISOString(),
            'details' => $details,
        ];
        $this->violations = $violations;
        $this->save();
    }
}
