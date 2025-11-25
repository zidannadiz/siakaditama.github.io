<?php

namespace App\Http\Controllers\Api\Forum;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    /**
     * Get list of forum topics with filters
     */
    public function index(Request $request)
    {
        $query = ForumTopic::with([
            'creator:id,name,email',
            'latestPost.user:id,name,email'
        ])
        ->orderBy('is_pinned', 'desc')
        ->orderBy('last_reply_at', 'desc');

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $topics = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $topics
        ]);
    }

    /**
     * Create a new forum topic
     */
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

        $post = ForumPost::create([
            'topic_id' => $topic->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_first_post' => true,
        ]);

        $topic->load(['creator:id,name,email', 'posts.user:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Topik forum berhasil dibuat',
            'data' => $topic
        ], 201);
    }

    /**
     * Get forum topic details
     */
    public function show(ForumTopic $forumTopic)
    {
        $forumTopic->incrementViews();
        $forumTopic->load([
            'creator:id,name,email',
            'posts.user:id,name,email'
        ]);

        return response()->json([
            'success' => true,
            'data' => $forumTopic
        ]);
    }

    /**
     * Update forum topic (only by creator or admin)
     */
    public function update(Request $request, ForumTopic $forumTopic)
    {
        if ($forumTopic->created_by !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|in:umum,akademik,organisasi,hobi,lainnya',
        ]);

        $forumTopic->update($request->only(['title', 'description', 'category']));
        $forumTopic->load(['creator:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Topik forum berhasil diperbarui',
            'data' => $forumTopic
        ]);
    }

    /**
     * Delete forum topic (only by creator or admin)
     */
    public function destroy(ForumTopic $forumTopic)
    {
        if ($forumTopic->created_by !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $forumTopic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topik forum berhasil dihapus'
        ]);
    }

    /**
     * Reply to a forum topic
     */
    public function reply(Request $request, ForumTopic $forumTopic)
    {
        if ($forumTopic->is_locked && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Topik ini terkunci'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $post = ForumPost::create([
            'topic_id' => $forumTopic->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        $forumTopic->increment('replies_count');
        $forumTopic->update(['last_reply_at' => now()]);
        
        $post->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Balasan berhasil dikirim',
            'data' => $post
        ], 201);
    }
}

