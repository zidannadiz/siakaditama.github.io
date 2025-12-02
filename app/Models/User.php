<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Note: 'role' is NOT fillable to prevent mass assignment vulnerability
        // Role must be assigned explicitly in controllers
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function notifikasis()
    {
        return $this->hasMany(\App\Models\Notifikasi::class);
    }

    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function forumTopics()
    {
        return $this->hasMany(ForumTopic::class, 'created_by');
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function getUnreadMessagesCount()
    {
        try {
            // Get all conversations where this user is involved
            $conversations = \App\Models\Conversation::where(function($query) {
                $query->where('user1_id', $this->id)
                      ->orWhere('user2_id', $this->id);
            })->get();
            
            $totalUnread = 0;
            
            // Sum unread count from each conversation (same logic as conversations.blade.php)
            foreach ($conversations as $conversation) {
                $totalUnread += $conversation->messages()
                    ->where('sender_id', '!=', $this->id)
                    ->where('is_read', false)
                    ->count();
            }
            
            return $totalUnread;
        } catch (\Exception $e) {
            Log::error('Error counting unread messages: ' . $e->getMessage(), [
                'user_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
