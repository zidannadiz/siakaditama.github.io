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
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->integer('waktu_tersisa'); // Waktu tersisa dalam detik
            $table->integer('tab_switch_count')->default(0); // Counter untuk tab switch
            $table->integer('copy_paste_attempt_count')->default(0); // Counter untuk copy paste attempt
            $table->json('violations')->nullable(); // Log pelanggaran (tab switch, copy paste, dll)
            $table->enum('status', ['started', 'submitted', 'auto_submitted', 'terminated'])->default('started');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['exam_id', 'mahasiswa_id']); // Satu session per mahasiswa per ujian
            $table->index('started_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
