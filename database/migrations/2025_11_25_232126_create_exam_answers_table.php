<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->foreignId('exam_question_id')->constrained('exam_questions')->onDelete('cascade');
            $table->string('jawaban_pilgan')->nullable(); // Untuk pilgan: 'A', 'B', 'C', dll
            $table->text('jawaban_essay')->nullable(); // Untuk essay
            $table->decimal('nilai', 5, 2)->nullable(); // Nilai yang diberikan (setelah dinilai)
            $table->text('feedback')->nullable();
            $table->boolean('is_answered')->default(false);
            $table->timestamps();
            
            $table->unique(['exam_session_id', 'exam_question_id']); // Satu jawaban per soal per session
            $table->index('exam_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
