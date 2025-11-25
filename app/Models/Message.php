<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'status',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    public function markAsDelivered()
    {
        if ($this->status === 'sent') {
            $this->update(['status' => 'delivered']);
        }
    }

    public function getStatusIcon()
    {
        return match($this->status) {
            'sent' => '✓', // Single check (gray)
            'delivered' => '✓✓', // Double check (gray)
            'read' => '✓✓', // Double check (blue)
            default => '✓',
        };
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'sent' => 'text-gray-400',
            'delivered' => 'text-gray-400',
            'read' => 'text-blue-500',
            default => 'text-gray-400',
        };
    }
}

