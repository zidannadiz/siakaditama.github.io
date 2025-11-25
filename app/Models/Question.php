<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'status',
        'best_answer_id',
        'views',
        'answers_count',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->orderBy('is_best_answer', 'desc')->orderBy('upvotes', 'desc');
    }

    public function bestAnswer()
    {
        return $this->belongsTo(Answer::class, 'best_answer_id');
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function markAsAnswered($answerId)
    {
        $this->update([
            'status' => 'answered',
            'best_answer_id' => $answerId,
            'answered_at' => now(),
        ]);
    }
}

