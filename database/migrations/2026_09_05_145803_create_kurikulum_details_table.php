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
        Schema::create('kurikulum_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained('kurikulums')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->unsignedTinyInteger('semester_ke'); // 1–8
            $table->enum('jenis', ['wajib', 'pilihan'])->default('wajib');
            $table->timestamps();

            // Satu MK hanya muncul sekali dalam satu kurikulum
            $table->unique(['kurikulum_id', 'mata_kuliah_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurikulum_details');
    }
};
