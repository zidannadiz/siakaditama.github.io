<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
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
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('status', '!=', 'read')
            ->update([
                'status' => 'read',
                'is_read' => true,
                'read_at' => now(),
            ]);

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

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => auth()->id(),
                'user2_id' => $receiverId,
            ]);
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

        return back()->with('success', 'Pesan terkirim');
    }
}

