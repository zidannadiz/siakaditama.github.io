<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = Conversation::where('user1_id', auth()->id())
            ->orWhere('user2_id', auth()->id())
            ->with(['user1', 'user2', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('chat.conversations', compact('conversations'));
    }

    public function create()
    {
        // Get all users except current user
        $users = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        return view('chat.create', compact('users'));
    }

    public function show(Conversation $conversation)
    {
        // Check if user is part of this conversation
        if ($conversation->user1_id !== auth()->id() && $conversation->user2_id !== auth()->id()) {
            abort(403);
        }

        // Mark messages as delivered and read
        $unreadMessages = $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('status', '!=', 'read')
            ->get();
            
        $unreadCount = $unreadMessages->count();
        
        if ($unreadCount > 0) {
            $conversation->messages()
                ->where('sender_id', '!=', auth()->id())
                ->where('status', '!=', 'read')
                ->update([
                    'status' => 'read',
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            
            // Log audit - Read messages
            $otherUser = $conversation->other_user;
            AuditLogService::logCustom(
                'chat_read',
                $conversation,
                "Membaca {$unreadCount} pesan dari {$otherUser->name} ({$otherUser->email})"
            );
        }

        $messages = $conversation->messages()->with('sender')->get();
        $otherUser = $conversation->other_user;

        return view('chat.show', compact('conversation', 'messages', 'otherUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $receiverId = $request->receiver_id;

        // Find or create conversation
        $conversation = Conversation::where(function($query) use ($receiverId) {
            $query->where('user1_id', auth()->id())
                  ->where('user2_id', $receiverId);
        })->orWhere(function($query) use ($receiverId) {
            $query->where('user1_id', $receiverId)
                  ->where('user2_id', auth()->id());
        })->first();

        $isNewConversation = false;
        $receiver = User::find($receiverId);
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => auth()->id(),
                'user2_id' => $receiverId,
            ]);
            $isNewConversation = true;
            
            // Log audit - Create conversation
            if ($receiver) {
                AuditLogService::logCreate(
                    $conversation,
                    "Membuat percakapan baru dengan {$receiver->name} ({$receiver->email})"
                );
            }
        }

        // Create message with status 'sent' and is_read = false
        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'sent',
            'is_read' => false, // Explicitly set to false
        ]);

        // Mark as delivered immediately (in real app, this would be done when receiver opens chat)
        $message->update(['status' => 'delivered']);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Log audit - Send message
        if ($receiver) {
            $messagePreview = strlen($request->message) > 50 
                ? substr($request->message, 0, 50) . '...' 
                : $request->message;
            
            AuditLogService::logCustom(
                'chat_send',
                $message,
                "Mengirim pesan chat ke {$receiver->name} ({$receiver->email}): \"{$messagePreview}\""
            );
        }

        return redirect()->route('chat.show', $conversation)
            ->with('success', 'Pesan terkirim');
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if ($conversation->user1_id !== auth()->id() && $conversation->user2_id !== auth()->id()) {
            abort(403);
        }

        $message = $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'sent',
            'is_read' => false, // Explicitly set to false
        ]);

        // Mark as delivered immediately
        $message->update(['status' => 'delivered']);

        $conversation->update(['last_message_at' => now()]);

        // Log audit - Send message
        $otherUser = $conversation->other_user;
        $messagePreview = strlen($request->message) > 50 
            ? substr($request->message, 0, 50) . '...' 
            : $request->message;
        
        AuditLogService::logCustom(
            'chat_send',
            $message,
            "Mengirim pesan chat ke {$otherUser->name} ({$otherUser->email}): \"{$messagePreview}\""
        );

        return back()->with('success', 'Pesan terkirim');
    }
}

