<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get list of conversations
     */
    public function index()
    {
        $conversations = Conversation::where('user1_id', auth()->id())
            ->orWhere('user2_id', auth()->id())
            ->with([
                'user1:id,name,email',
                'user2:id,name,email',
                'latestMessage.sender:id,name'
            ])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) {
                $otherUser = $conversation->user1_id === auth()->id() 
                    ? $conversation->user2 
                    : $conversation->user1;
                
                $unreadCount = $conversation->messages()
                    ->where('sender_id', '!=', auth()->id())
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $conversation->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'email' => $otherUser->email,
                    ],
                    'last_message' => $conversation->latestMessage ? [
                        'id' => $conversation->latestMessage->id,
                        'message' => $conversation->latestMessage->message,
                        'sender_id' => $conversation->latestMessage->sender_id,
                        'sender_name' => $conversation->latestMessage->sender->name ?? null,
                        'status' => $conversation->latestMessage->status,
                        'is_read' => $conversation->latestMessage->is_read,
                        'created_at' => $conversation->latestMessage->created_at,
                    ] : null,
                    'last_message_at' => $conversation->last_message_at,
                    'unread_count' => $unreadCount,
                    'created_at' => $conversation->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    /**
     * Get list of users to start conversation
     */
    public function getUsers()
    {
        $users = User::where('id', '!=', auth()->id())
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Create new conversation and send first message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $receiverId = $request->receiver_id;

        if ($receiverId == auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot send message to yourself'
            ], 422);
        }

        // Find or create conversation
        $conversation = Conversation::where(function($query) use ($receiverId) {
            $query->where('user1_id', auth()->id())
                  ->where('user2_id', $receiverId);
        })->orWhere(function($query) use ($receiverId) {
            $query->where('user1_id', $receiverId)
                  ->where('user2_id', auth()->id());
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => auth()->id(),
                'user2_id' => $receiverId,
            ]);
        }

        // Create message
        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'sent',
            'is_read' => false,
        ]);

        $message->update(['status' => 'delivered']);
        $conversation->update(['last_message_at' => now()]);

        $message->load('sender:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Pesan terkirim',
            'data' => [
                'conversation_id' => $conversation->id,
                'message' => $message
            ]
        ], 201);
    }

    /**
     * Get conversation details with messages
     */
    public function show(Conversation $conversation)
    {
        // Check if user is part of this conversation
        if ($conversation->user1_id !== auth()->id() && $conversation->user2_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update([
                'status' => 'read',
                'is_read' => true,
                'read_at' => now(),
            ]);

        $messages = $conversation->messages()
            ->with('sender:id,name,email')
            ->orderBy('created_at', 'asc')
            ->get();

        $otherUser = $conversation->user1_id === auth()->id() 
            ? $conversation->user2 
            : $conversation->user1;

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => [
                    'id' => $conversation->id,
                    'created_at' => $conversation->created_at,
                ],
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                ],
                'messages' => $messages
            ]
        ]);
    }

    /**
     * Send message in existing conversation
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        // Check if user is part of this conversation
        if ($conversation->user1_id !== auth()->id() && $conversation->user2_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'sent',
            'is_read' => false,
        ]);

        $message->update(['status' => 'delivered']);
        $conversation->update(['last_message_at' => now()]);

        $message->load('sender:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Pesan terkirim',
            'data' => $message
        ], 201);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Conversation $conversation)
    {
        // Check if user is part of this conversation
        if ($conversation->user1_id !== auth()->id() && $conversation->user2_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $updated = $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update([
                'status' => 'read',
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan ditandai sebagai sudah dibaca',
            'data' => [
                'updated_count' => $updated
            ]
        ]);
    }

    /**
     * Get unread messages count
     */
    public function unreadCount()
    {
        $conversations = Conversation::where('user1_id', auth()->id())
            ->orWhere('user2_id', auth()->id())
            ->get();

        $totalUnread = 0;
        foreach ($conversations as $conversation) {
            $totalUnread += $conversation->messages()
                ->where('sender_id', '!=', auth()->id())
                ->where('is_read', false)
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $totalUnread
            ]
        ]);
    }
}

