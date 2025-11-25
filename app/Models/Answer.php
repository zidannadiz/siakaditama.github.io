<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'user_id',
        'content',
        'is_best_answer',
        'upvotes',
        'downvotes',
    ];

    protected $casts = [
        'is_best_answer' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsBest()
    {
        // Unmark previous best answer
        $this->question->answers()->where('is_best_answer', true)->update(['is_best_answer' => false]);
        
        // Mark this as best
        $this->update(['is_best_answer' => true]);
        
        // Update question
        $this->question->markAsAnswered($this->id);
    }
}

