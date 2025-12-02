<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter',
        'bobot',
        'min_score',
        'max_score',
        'order',
        'is_active',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'min_score' => 'integer',
        'max_score' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get letter grade by numeric score
     */
    public static function getByScore(float $score): ?self
    {
        return self::where('is_active', true)
            ->where('min_score', '<=', $score)
            ->where(function($query) use ($score) {
                $query->whereNull('max_score')
                      ->orWhere('max_score', '>=', $score);
            })
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * Get all active letter grades ordered by order
     */
    public static function getActiveOrdered()
    {
        return self::where('is_active', true)
            ->orderBy('order', 'desc')
            ->get();
    }

    /**
     * Check if score range overlaps with existing grades
     */
    public static function hasOverlap(int $minScore, ?int $maxScore, ?int $excludeId = null): bool
    {
        $query = self::where('is_active', true)
            ->where(function($q) use ($minScore, $maxScore) {
                // Check if new range overlaps with existing ranges
                $q->where(function($q2) use ($minScore, $maxScore) {
                    // Existing min is within new range
                    $q2->whereBetween('min_score', [$minScore, $maxScore ?? 100]);
                    // Or existing max is within new range (if exists)
                    if ($maxScore !== null) {
                        $q2->orWhereBetween('max_score', [$minScore, $maxScore]);
                    } else {
                        $q2->orWhereNull('max_score');
                    }
                    // Or new range is completely within existing range
                    $q2->orWhere(function($q3) use ($minScore, $maxScore) {
                        $q3->where('min_score', '<=', $minScore);
                        if ($maxScore !== null) {
                            $q3->where(function($q4) use ($maxScore) {
                                $q4->whereNull('max_score')
                                    ->orWhere('max_score', '>=', $maxScore);
                            });
                        }
                    });
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
