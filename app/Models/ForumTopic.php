<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'created_by',
        'is_pinned',
        'is_locked',
        'views',
        'replies_count',
        'last_reply_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_reply_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'topic_id')->orderBy('created_at', 'asc');
    }

    public function firstPost()
    {
        return $this->hasOne(ForumPost::class, 'topic_id')->where('is_first_post', true);
    }

    public function latestPost()
    {
        return $this->hasOne(ForumPost::class, 'topic_id')->latestOfMany();
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}

