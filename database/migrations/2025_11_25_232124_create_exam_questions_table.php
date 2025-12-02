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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->enum('tipe', ['pilgan', 'essay'])->default('pilgan');
            $table->text('pertanyaan');
            $table->json('pilihan')->nullable(); // Untuk pilgan: ['A' => 'text', 'B' => 'text', ...]
            $table->string('jawaban_benar')->nullable(); // Untuk pilgan: 'A', 'B', 'C', dll
            $table->text('jawaban_benar_essay')->nullable(); // Kunci jawaban untuk essay (optional)
            $table->decimal('bobot', 5, 2)->default(1); // Bobot nilai per soal
            $table->integer('urutan')->default(0);
            $table->text('penjelasan')->nullable(); // Penjelasan jawaban (tampil setelah selesai)
            $table->timestamps();
            
            $table->index('exam_id');
            $table->index(['exam_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
