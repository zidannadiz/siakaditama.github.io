<?php

namespace App\Http\Controllers\Api\QnA;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Get list of questions with filters
     */
    public function index(Request $request)
    {
        $query = Question::with(['user:id,name,email', 'bestAnswer:id,content,is_best_answer']);

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $questions
        ]);
    }

    /**
     * Create a new question
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:akademik,administrasi,teknologi,umum',
        ]);

        $question = Question::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
        ]);

        $question->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan berhasil diajukan',
            'data' => $question
        ], 201);
    }

    /**
     * Get question details
     */
    public function show(Question $question)
    {
        $question->incrementViews();
        $question->load(['user:id,name,email', 'answers.user:id,name,email']);

        return response()->json([
            'success' => true,
            'data' => $question
        ]);
    }

    /**
     * Update question (only by owner or admin)
     */
    public function update(Request $request, Question $question)
    {
        if ($question->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'category' => 'sometimes|required|in:akademik,administrasi,teknologi,umum',
        ]);

        $question->update($request->only(['title', 'content', 'category']));
        $question->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan berhasil diperbarui',
            'data' => $question
        ]);
    }

    /**
     * Delete question (only by owner or admin)
     */
    public function destroy(Question $question)
    {
        if ($question->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pertanyaan berhasil dihapus'
        ]);
    }

    /**
     * Submit an answer to a question
     */
    public function answer(Request $request, Question $question)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $answer = Answer::create([
            'question_id' => $question->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        $question->increment('answers_count');
        $answer->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil dikirim',
            'data' => $answer
        ], 201);
    }

    /**
     * Mark an answer as best answer
     */
    public function markBestAnswer(Question $question, Answer $answer)
    {
        if ($question->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($answer->question_id !== $question->id) {
            return response()->json([
                'success' => false,
                'message' => 'Answer does not belong to this question'
            ], 422);
        }

        $answer->markAsBest();
        $question->refresh();
        $question->load(['user:id,name,email', 'answers.user:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban terbaik telah dipilih',
            'data' => $question
        ]);
    }
}

