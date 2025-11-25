<?php

namespace App\Http\Controllers\QnA;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['user', 'bestAnswer']);

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

        return view('qna.index', compact('questions'));
    }

    public function create()
    {
        return view('qna.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:akademik,administrasi,teknologi,umum',
        ]);

        Question::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
        ]);

        return redirect()->route('qna.index')
            ->with('success', 'Pertanyaan berhasil diajukan');
    }

    public function show(Question $question)
    {
        $question->incrementViews();
        $question->load(['user', 'answers.user']);

        return view('qna.show', compact('question'));
    }

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

        return back()->with('success', 'Jawaban berhasil dikirim');
    }

    public function markBestAnswer(Question $question, Answer $answer)
    {
        if ($question->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $answer->markAsBest();

        return back()->with('success', 'Jawaban terbaik telah dipilih');
    }
}

