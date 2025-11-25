<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $query = ForumTopic::with(['creator', 'latestPost.user'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_reply_at', 'desc');

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $topics = $query->paginate(20);

        return view('forum.index', compact('topics'));
    }

    public function create()
    {
        return view('forum.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:umum,akademik,organisasi,hobi,lainnya',
            'content' => 'required|string',
        ]);

        $topic = ForumTopic::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'created_by' => auth()->id(),
        ]);

        ForumPost::create([
            'topic_id' => $topic->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_first_post' => true,
        ]);

        return redirect()->route('forum.show', $topic)
            ->with('success', 'Topik forum berhasil dibuat');
    }

    public function show(ForumTopic $forumTopic)
    {
        $forumTopic->incrementViews();
        $forumTopic->load(['creator', 'posts.user']);

        return view('forum.show', compact('forumTopic'));
    }

    public function reply(Request $request, ForumTopic $forumTopic)
    {
        if ($forumTopic->is_locked) {
            return back()->with('error', 'Topik ini terkunci');
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        ForumPost::create([
            'topic_id' => $forumTopic->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        $forumTopic->increment('replies_count');
        $forumTopic->update(['last_reply_at' => now()]);

        return back()->with('success', 'Balasan berhasil dikirim');
    }
}

