<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'mahasiswa_id',
        'jawaban',
        'file_path',
        'nilai',
        'feedback',
        'submitted_at',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function isLate(): bool
    {
        if (!$this->submitted_at || !$this->assignment) {
            return false;
        }
        return $this->submitted_at->isAfter($this->assignment->deadline);
    }
}
